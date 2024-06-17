<?php
 use MongoDB\BSON\ObjectId;
class VideoController extends Controller {
    
 
    public $layout = 'sample';
 
    public function actionIndex() {
        $this->render('home');
    }
 
    public function actionGetVtt() {
        $objectKey = Yii::app()->request->getQuery('data');
 
        $bucketName = $_ENV['VTT_BUCKET'];
 
        try {
            $result = S3Helper::getS3Object($bucketName, $objectKey);
            echo $result['Body'];
            return $result['Body'];
 
        } catch (Aws\S3\Exception\S3Exception $e) {
            Yii::log("Error streaming video from S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error streaming video from S3.');
        }
    }
 
    //addvideo
    public function actionAddVideo(){

        if (isset($_POST['title'], $_FILES['videoFile'], $_FILES['imageFile'])) {
            try {
                $videoFile = CUploadedFile::getInstanceByName('videoFile');
                $imageFile = CUploadedFile::getInstanceByName('imageFile');
 
                $videoOriginalName = $videoFile->getName();
                $imageOriginalName = $imageFile->getName();
 
                $uniqueId = uniqid();
                $videoObjectKey = $uniqueId . '-' . $videoOriginalName ;
                $imageObjectKey = $uniqueId . '-' . $imageOriginalName ;
                $vttObjectKey = $videoObjectKey . '.vtt';
 
                $videoUrl = S3Helper::uploadFileToS3($_ENV['VIDEO_BUCKET'], $videoObjectKey, $videoFile->tempName, 'video/mp4');
                echo "<pre>";
                print_r($videoUrl);
 
                $imageUrl = S3Helper::uploadFileToS3($_ENV['IMAGE_BUCKET'], $imageObjectKey, $imageFile->tempName, 'image/png');
                print_r($imageUrl);
 
                $newVideo = new Video();
                $newVideo->userId = new ObjectId(Yii::app()->session['user_id']);
                $newVideo->title = $_POST['title'];
                $newVideo->desc = $_POST['desc'];
                $newVideo->imgKey = $imageObjectKey;
                $newVideo->videoKey = $videoObjectKey;
                $newVideo->captionsKey = $vttObjectKey;
                $newVideo->tags = $_POST['tags'];
 
                if ($newVideo->save()) {
                    $this->redirect(array('video/success'));
                } else {
                    throw new CHttpException(500, 'Failed to save video details.');
                }
            } catch (Exception $e) {
                Yii::log("Error uploading video: " . $e->getMessage(), 'error');
                throw new CHttpException(500, 'Failed to upload video.');
            }
        } else {
            $this->render('upload');
        }
    }
 
    public function actionSuccess(){
        $this->render('success');
    }
 
    //getHomeCrads
    public function actionHome(){

        try{

            $result = VideoHelper::homeHelper();
            Yii::app()->controller->render('home', array('data' => $result));
        }

        catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }

    }
 
    //getMyVideos
    public function actionMyVideos() {
        try {
 
            $result = VideoHelper::myVideosHelper();
 
            $this->render('myvideos', array('data' => $result));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }
 
    //getMyFav
    public function actionLikedVideos(){
        try {
            
            $userId = new ObjectId(Yii::app()->session['user_id']);
 
            $user = User::model()->findByAttributes(array('_id' => $userId));
 
            $likedVideos = $user->likedVideos;
            $data = array();
 
            foreach ($likedVideos as $videoId) {
                $video = Video::model()->findByPk($videoId);
                if ($video) {
                    $data[] = $video;
                }
            }
 
            $this->render('home', array('data' => $data));
        } catch (Exception $e) {
            Yii::log("Error fetching user's liked videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            $this->render('error', array('message' => 'An error occurred while fetching liked videos.'));
        }
    }
 
    //getWatchLater
    public function actionWatchLater(){
        try {
            $userId = new ObjectId(Yii::app()->session['user_id']);
 
            $criteria = new EMongoCriteria();
            $criteria->_id = $userId;
 
            $user = User::model()->find($criteria);
 
            $watchLater = $user->watchLater;
            $data = array();
 
            foreach ($watchLater as $videoId) {
                $video = Video::model()->findByPk($videoId);
                if ($video) {
                    $data[] = $video;
                }
            }
 
            $this->render('home', array('data' => $data));
        } catch (Exception $e) {
            Yii::log("Error fetching user's watch later videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
 
    //deleteVideo
    public function actionDeleteVideo(){
        try {
            $id = Yii::app()->request->getQuery('videoId');
            $video = Video::model()->findByPk(new ObjectId($id));
            
            if ($video === null) {
                throw new CHttpException(404, 'Video not found!');
            }
 
            $imgKey = $video->imgKey;
            $videoKey = $video->videoKey;
            $captionsKey = $video->captionsKey;
            $userId = new ObjectId(Yii::app()->session['user_id']);
            $videoId = new ObjectId($id);
 
            if ($userId == $video->userId) {
                $criteria = new EMongoCriteria();
                $criteria->addCond('$or', '==', [
                    ['likedVideos' => ['$in' => [$videoId]]],
                    ['dislikedVideos' => ['$in' => [$videoId]]],
                    ['watchLater' => ['$in' => [$videoId]]]
                ]);
        
                $users = User::model()->findAll($criteria);
        
                foreach ($users as $user) {
                    $changed = false;
 
                    if (($key = array_search($videoId, $user->likedVideos)) !== false) {
                        unset($user->likedVideos[$key]);
                        $changed = true;
                    }
 
                    if (($key = array_search($videoId, $user->dislikedVideos)) !== false) {
                        unset($user->dislikedVideos[$key]);
                        $changed = true;
                    }
 
                    if (($key = array_search($videoId, $user->watchLater)) !== false) {
                        unset($user->watchLater[$key]);
                        $changed = true;
                    }
 
                    if ($changed) {
                        $user->save();
                    }
                }
                
                S3Helper::deleteS3Object($_ENV['IMAGE_BUCKET'], $imgKey);
                Yii::log("Image deleted: $imgKey", 'info');
 
                S3Helper::deleteS3Object($_ENV['VIDEO_BUCKET'], $videoKey);
                Yii::log("Video deleted: $videoKey", 'info');
 
                S3Helper::deleteS3Object($_ENV['VTT_BUCKET'], $captionsKey);
                Yii::log("Caption deleted: $captionsKey", 'info');
 
                $video = Video::model()->findByPk($videoId);
               
                if ($video) {
                    $video->delete();
                }
                Yii::log("Video deleted from DB: $id", 'info');
                echo CJSON::encode(array('success' => true, 'message' => 'The video has been deleted'));
            } else {
                throw new CHttpException(403, 'You can delete only your video!');
            }
        } catch (Exception $e) {
            Yii::log("Error: " . $e->getMessage(), 'error');
            throw new CHttpException(500, 'Internal Server Error');
        }
    }
 
    //AddView
    public function actionAddView(){
        try {
            $videoId = new ObjectId(Yii::app()->request->getQuery('id'));
            $video = Video::model()->findByPk($videoId);
 
            if (!$video) {
                throw new CHttpException(404, "Video Not Found");
            }
 
            $video->views += 1;
 
            if ($video->save()) {
                header('HTTP/1.1 200 OK');
                echo "View count incremented successfully.";
            } else {
                throw new CHttpException(500, "Failed to increment view count.");
            }
        } catch (Exception $e) {
            Yii::log("Error incrementing view count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            header('HTTP/1.1 500 Internal Server Error');
            echo "Internal Server Error";
        }
 
        Yii::app()->end();
    }
 
    //Plays
    public function actionAddPlay(){
        try {
            $videoId = new ObjectId(Yii::app()->request->getQuery('id'));
 
            $video = Video::model()->findByPk($videoId);
         
            if (!$video) {
                throw new CHttpException(404, "Video Not Found");
            }
 
            $video->plays += 1;
 
            if ($video->save()) {
                header('HTTP/1.1 200 OK');
                echo "play count incremented successfully.";
            } else {
                throw new CHttpException(500, "Failed to increment play count.");
            }
        } catch (Exception $e) {
            Yii::log("Error incrementing play count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            header('HTTP/1.1 500 Internal Server Error');
            echo "Internal Server Error";
        }
        Yii::app()->end();
    }
    
    //Trend
    public function actionTrends() {
        try {
            $criteria = new EMongoCriteria();
            $criteria->sort('views', EMongoCriteria::SORT_DESC);
            
            $videos = Video::model()->findAll($criteria);
 
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching trending videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Internal Server Error');
        }
    }
 
    public function actionTags(){
        $tag = Yii::app()->request->getQuery('tag');
 
        try {
            $criteria = new EMongoCriteria();
            $criteria->tags('in', array($tag));
            
            $criteria->limit(20);
 
            $videos = Video::model()->findAll($criteria);
 
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos by tag: " . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
 
    //search-----------------------
    public function actionSearch() {

        $query = "hr";
 
        if ($query === '') {
            $this->redirect(array('video/home'));
        }
 
        try {
            $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $mongoClient->database_name->videos;
            
            $pipeline = [
                [
                    '$search' => [
                        'index' => 'searchVideo',
                        'text' => [
                            'query' => $query,
                            'path' => [
                                'wildcard' => '*'
                            ]
                        ]
                    ]
                ]
            ];
            
            $cursor = $collection->aggregate($pipeline);
            $videos = iterator_to_array($cursor);
            
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Internal Server Error');
        }
    }
 
    public function actionPlayVideo(){
        $data = Yii::app()->request->getQuery('data');
        $id = Yii::app()->request->getQuery('id');
        $this->render('player', array(
            'data' => $data,
            'id' => $id
        ));
    }
 
    
 
    public function actionAnalytics(){
        try {
            $userId =  new ObjectId(Yii::app()->session['user_id']);
 
            $criteria = new EMongoCriteria();
            $criteria->userId = $userId;
 
            // $criteria->select(array('title', 'views', 'plays', 'likes', 'dislikes'));
 
            $videoAnalytics = Video::model()->findAll($criteria);
            
            $videoinfo = array();
            foreach ($videoAnalytics as $video) {
                $eachvideoinfo = array(
                    'title' => $video->title,
                    'views' => $video->views,
                    'plays' => $video->plays,
                    'likes' => $video->likes,
                    'dislikes' => $video->dislikes,
                );
                $videoinfo[] = $eachvideoinfo;
            }
            $overall = array(
                'totalViews' => array_sum(array_column($videoinfo, 'views')),
                'totalPlays' => array_sum(array_column($videoinfo, 'plays')),
                'totalLikes' => array_sum(array_column($videoinfo, 'likes')),
                'totalDisLikes' => array_sum(array_column($videoinfo, 'dislikes')),
            );
 
            $this->render('analytics', array(
                'videoinfo' => json_encode($videoinfo),
                'overall' => json_encode($overall),
                'n' => count($videoinfo),
            ));
        } catch (Exception $e) {
            Yii::log("Error fetching video analytics: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            $this->render('error', array('message' => 'Error fetching video analytics'));
        }
    }
 
    public function actionUploadVideo(){
        try {
            $this->render('uploadvideo');
        } catch (Exception $e) {
            Yii::log("Error fetching uploadvideo page: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }
 
    public function actionUpload(){
        try{
 
        } catch (Exception $e) {
            Yii::log("Error uploading video: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }
 
    public function actionStorePlaybackPosition($videoId){
        try {
            if (Yii::app()->request->isPostRequest) {
                $data = file_get_contents('php://input');
                $data = CJSON::decode($data, true);
                $playbackPosition = $data["playbackPosition"];
                Yii::app()->session["playbackPosition_$videoId"] = $playbackPosition;
                echo CJSON::encode(array('status' => 200));
            } else {
                throw new CHttpException(400, 'Invalid request. Playback position is missing.');
            }
        } catch (Exception $e) {
            Yii::log("Error storing playback position to session: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            echo CJSON::encode(array('status' => 500, 'error' => 'Failed to set playback position'));
        }
    }
 
    public function actionFetchPlaybackPosition($videoId){
        try {
            $playbackPosition = Yii::app()->session["playbackPosition_$videoId"] ?? 0;
            Yii::log(
                "Playback position for video $videoId retrieved: $playbackPosition",
                CLogger::LEVEL_INFO
            );
 
            header('Content-Type: application/json');
            Yii::app()->end(CJSON::encode(array('playbackPosition' => $playbackPosition)));
        } catch (Exception $e) {
            Yii::log("Error retrieving playback position from session: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            header('Content-Type: application/json');
            echo CJSON::encode(array('error' => 'Failed to retrieve playback position'));
        }
        Yii::app()->end();
    }
}
 
    
 
?>