/home/sandhya/Downloads/S3Helper.php<?php 

use Aws\S3\S3Client;

class VideoController extends Controller {
    

    public $layout = 'sample';

    public function actionIndex() {
        $this->render('home');
    }

    public function actionGetVtt() {
        $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');

        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $awsConfig['aws']['region'],
            'credentials' => $awsConfig['aws']['credentials'],
        ]);

        $s3Helper = new S3Helper($s3Client);
        $objectKey = Yii::app()->request->getQuery('data');

        try {
            $result = $s3Helper->getS3Object($_ENV['VTT_BUCKET'], $objectKey);
            echo $result['Body'];
            return $result['Body'];

        } catch (Aws\S3\Exception\S3Exception $e) {
            Yii::log("Error streaming video from S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error streaming video from S3.');
        }
    }

    //addvideo
    public function actionAddVideo()
    {
        if (isset($_POST['title'], $_FILES['videoFile'], $_FILES['imageFile'])) {
            try {

                $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');

                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => $awsConfig['aws']['region'],
                    'credentials' => $awsConfig['aws']['credentials'],
                ]);

                $s3Helper = new S3Helper($s3Client);

                $videoFile = CUploadedFile::getInstanceByName('videoFile');
                $imageFile = CUploadedFile::getInstanceByName('imageFile');

                $videoOriginalName = $videoFile->getName();
                $imageOriginalName = $imageFile->getName();

                $uniqueId = uniqid();
                $videoObjectKey = $uniqueId . '-' . $videoOriginalName ;
                $imageObjectKey = $uniqueId . '-' . $imageOriginalName ;
                $vttObjectKey = $videoObjectKey . '.vtt';

                $videoUploaded = $s3Helper->uploadFileToS3($_ENV['VIDEO_BUCKET'], $videoObjectKey, $videoFile->tempName, 'video/mp4');
                $imageUploaded = $s3Helper->uploadFileToS3($_ENV['IMAGE_BUCKET'], $imageObjectKey, $imageFile->tempName, 'image/png');

                $video = VideoHelper::addVideo(Yii::app()->session['user_id'], $imageObjectKey, $videoObjectKey, $vttObjectKey);
                if ($video) {
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
    public function actionHome() {
        try {
            $videos = VideoHelper::getHomeVideos();
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }

    //getMyVideos
    public function actionMyVideos() {
        try {
            $videos = VideoHelper::getMyVideos(Yii::app()->session['user_id']);
            $this->render('myvideos', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }

    //getMyFav
    public function actionLikedVideos() {
        try {
            $videos = VideoHelper::getLikedVideos(Yii::app()->session['user_id']);
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }

    //getWatchLater
    public function actionWatchLater() {
        try {
            $videos = VideoHelper::getWatchLaterVideos(Yii::app()->session['user_id']);
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }

    //deleteVideo
    public function actionDeleteVideo()
    {
        try {
            $id = Yii::app()->request->getQuery('videoId');
            $userId = Yii::app()->session['user_id'];
            // $res = VideoHelper::deleteVideo($id, $userId); 
            $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');

            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => $awsConfig['aws']['region'],
                'credentials' => $awsConfig['aws']['credentials'],
            ]);

            $s3Helper = new S3Helper($s3Client);

            $videoId = new MongoDB\BSON\ObjectId($id);
            $video = Video::model()->findByPk($videoId);
            
            if ($video === null) {
                throw new CHttpException(404, 'Video not found!');
            }

            $imgKey = $video->imgKey;
            $videoKey = $video->videoKey;
            $captionsKey = $video->captionsKey;

            $userId = new MongoDB\BSON\ObjectId($userId);

            if ($userId == $video->userId) {
                
                $res = VideoHelper::deleteVideo($video);

                if($res){
                    
                $s3Helper->deleteS3Object($_ENV['IMAGE_BUCKET'], $imgKey);
                Yii::log("Image deleted: $imgKey", 'info');

                $s3Helper->deleteS3Object($_ENV['VIDEO_BUCKET'], $videoKey);
                Yii::log("Video deleted: $videoKey", 'info');

                $s3Helper->deleteS3Object($_ENV['VTT_BUCKET'], $captionsKey);
                Yii::log("Caption deleted: $captionsKey", 'info');

                Yii::log("Video deleted from DB: $id", 'info');
                echo CJSON::encode(array('success' => true, 'message' => 'The video has been deleted'));
                } else {
                    throw new CHttpException(403, 'You can delete only your video!');
                }
            }
        } catch (Exception $e) {
            Yii::log("Error: " . $e->getMessage(), 'error');
            throw new CHttpException(500, 'Internal Server Error');
        }
    }

    //AddView
    public function actionAddView()
    {
        try {
            $videoId = new MongoDB\BSON\ObjectId(Yii::app()->request->getQuery('id'));
            $res = VideoHelper::addView($videoId);
            if ($res) {
                echo "View count incremented successfully.";
            } else {
                throw new CHttpException(500, "Failed to increment view count.");
            }
        } catch (Exception $e) {
            Yii::log("Error incrementing view count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            echo "Internal Server Error";
        }
    }

    //Plays
    public function actionAddPlay()
    {
        try {
            $videoId = new MongoDB\BSON\ObjectId(Yii::app()->request->getQuery('id'));
            $res = VideoHelper::addPlay($videoId);
            if ($res) {
                echo "play count incremented successfully.";
            } else {
                throw new CHttpException(500, "Failed to increment play count.");
            }
        } catch (Exception $e) {
            Yii::log("Error incrementing play count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            echo "Internal Server Error";
        }
    }
    
    //Trend
    public function actionTrends()
    {
        try {
            $videos = VideoHelper::trendingVideos();
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching trending videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Internal Server Error');
        }
    }

    public function actionTags()
    {
        try {
            $tag = Yii::app()->request->getQuery('tag');
            $videos = VideoHelper::getVideoByTag($tag);
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos by tag: " . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    public function actionSearch()
    {
        $query = Yii::app()->request->getQuery('search');

        if ($query === '') {
            $this->redirect(array('video/home'));
        } else {
            try {
                $videos = VideoHelper::search($query);
                if (empty($videos)) {
                    Yii::log("No videos found for query: " . $query, CLogger::LEVEL_INFO);
                } else {
                    Yii::log("Videos found for query: " . $query, CLogger::LEVEL_INFO);
                }

                $this->render('home', array('data' => $videos));
            } catch (Exception $e) {
                Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
                throw new CHttpException(500, 'Internal Server Error');
            }
        }
    }

    public function actionPlayVideo()
    {
        $data = Yii::app()->request->getQuery('data');
        $id = Yii::app()->request->getQuery('id');
        $this->render('player', array(
            'data' => $data,
            'id' => $id
        ));
    }

    public function actionAnalytics()
    {
        try {
            list($videoinfo, $overall) = VideoHelper::getAnalytics(Yii::app()->session['user_id']);
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

    public function actionStorePlaybackPosition($videoId)
    {
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

    public function actionFetchPlaybackPosition($videoId)
    {
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
