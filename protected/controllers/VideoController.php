<?php
 
use Aws\S3\S3Client;
use MongoDB\BSON\ObjectId;
use SebastianBergmann\Environment\Console;
 
class VideoController extends CController {
 
    public $layout = 'sample';

 
    public function actionIndex() {
        $this->render('home');
    }
 

    //getHomeCrads
    public function actionHome() {
        try {
            $criteria = new EMongoCriteria();
            $criteria->setLimit(15);
            $videos = Video::model()->findAll($criteria);
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }


    public function actionGetVtt() {
        $data = Yii::app()->request->getQuery('data');
        //echo $data;
 
        $bucketName = $_ENV['VTT_BUCKET'];
        $key = $data;
 
        //echo $key;
      
 
        // Load AWS configuration
        $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');
        
        // Initialize AWS S3 Client
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $awsConfig['aws']['region'],
            'credentials' => $awsConfig['aws']['credentials'],
        ]);
 
        try {
            $result = $s3Client->getObject([
                'Bucket' => $bucketName,
                'Key'    => $key,
            ]);
 
            // Set headers
            header('Content-Type: ' . $result['ContentType']);
            header('Content-Length: ' . $result['ContentLength']);
 
            // Stream the file
            echo $result['Body'];
 
            return $result['Body'];
 
        } catch (Aws\S3\Exception\S3Exception $e) {
            Yii::log("Error streaming video from S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error streaming video from S3.');
        }
    }
 
    //addvideo-------------------
 
   
 
    //getMyVideos
    public function actionMyVideos() {
        try {
 
            $criteria = new EMongoCriteria();
            $criteria->userId = Yii::app()->session['jwt_payload']['user_id']->{'$oid'};
 
            $videos = Video::model()->findAll($criteria);
 
            $this->render('myvideos', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Error fetching videos.');
        }
    }
 
    //getMyFav
    public function actionLikedVideos()
    {
        try {
            $userEmail = Yii::app()->session['email'];
 
            $criteria = new EMongoCriteria();
            $criteria->email = $userEmail;
 
            $user = User::model()->find($criteria);
 
            if (!$user) {
                throw new Exception('User not found.');
            }
 
            $likedVideos = $user->likedVideos;
            $data = array();
 
            foreach ($likedVideos as $videoId) {
                $video = Video::model()->findByPk(new ObjectId($videoId));
                if ($video) {
                    $data[] = $video;
                }
            }
 
            $this->render('home', array('data' => $data));
        } catch (Exception $e) {
            Yii::log("Error fetching user's liked videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
 
    //getWatchLater
    public function actionWatchLater()
    {
        try {
            $userEmail = Yii::app()->session['jwt_payload']['email'];
 
            $criteria = new EMongoCriteria();
            $criteria->email = $userEmail;
 
            $user = User::model()->find($criteria);
 
            if (!$user) {
                throw new Exception('User not found.');
            }
 
            $watchLater = $user->watchLater;
            $data = array();
 
            foreach ($watchLater as $videoId) {
                $video = Video::model()->findByPk(new ObjectId($videoId));
                if ($video) {
                    $data[] = $video;
                }
            }
 
            $this->render('home', array('data' => $data));
        } catch (Exception $e) {
            Yii::log("Error fetching user's watch later videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
 
    //deleteVideo---------------------------------------------------
 
    //AddView
    public function actionAddView()
    {
        try {
            // Convert the string ID to MongoId or ObjectId
            $videoId = new ObjectId("660f98af7ac35193d83b73a1");
 
            // Find the video by its ID
            $video = Video::model()->findByPk($videoId);
            // print_r($video);
            // Check if the video exists
            if (!$video) {
                throw new CHttpException(404, "Video Not Found");
            }
 
            // Increment the view count
            $video->views += 1;
 
            // Save the updated video
            if ($video->save()) {
                // Return a success response
                header('HTTP/1.1 200 OK');
                echo "View count incremented successfully.";
            } else {
                // Return an error response if save fails
                throw new CHttpException(500, "Failed to increment view count.");
            }
        } catch (Exception $e) {
            // Log the error
            Yii::log("Error incrementing view count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
 
            // Return a 500 internal server error
            header('HTTP/1.1 500 Internal Server Error');
            echo "Internal Server Error";
        }
 
        // End the application to prevent Yii from rendering the view
        Yii::app()->end();
    }
 
    //Plays
    public function actionAddPlay()
    {
        try {
            // Convert the string ID to MongoId or ObjectId
            $videoId = new ObjectId("660f98af7ac35193d83b73a1");
 
            // Find the video by its ID
            $video = Video::model()->findByPk($videoId);
            // print_r($video);
            // Check if the video exists
            if (!$video) {
                throw new CHttpException(404, "Video Not Found");
            }
 
            // Increment the view count
            $video->plays += 1;
 
            // Save the updated video
            if ($video->save()) {
                // Return a success response
                header('HTTP/1.1 200 OK');
                echo "play count incremented successfully.";
            } else {
                // Return an error response if save fails
                throw new CHttpException(500, "Failed to increment play count.");
            }
        } catch (Exception $e) {
            // Log the error
            Yii::log("Error incrementing play count: " . $e->getMessage(), CLogger::LEVEL_ERROR);
 
            // Return a 500 internal server error
            header('HTTP/1.1 500 Internal Server Error');
            echo "Internal Server Error";
        }
 
        // End the application to prevent Yii from rendering the view
        Yii::app()->end();
    }
    
    //Trend
    public function actionTrends()
    {
        try {
            // Fetch videos sorted by views in descending order
            $criteria = new EMongoCriteria();
            $criteria->sort('views', EMongoCriteria::SORT_DESC);
            
            $videos = Video::model()->findAll($criteria);
 
            // Render the 'home' view with the retrieved video data
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            // Log the error
            Yii::log("Error fetching trending videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
 
            // Render an error view or redirect as needed
            throw new CHttpException(500, 'Internal Server Error');
        }
    }
 
    //search
    public function actionSearch()
    {
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
            
            // Render the 'home' view with the search results
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            Yii::log("Error fetching videos: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Internal Server Error');
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
 
    public function actionTag()
    {
 
        $tag = Yii::app()->request->getQuery('tag');
        echo $tag;
 
        try {
            // Create a MongoDB criteria to find videos with the given tag
            $criteria = new EMongoCriteria();
            $criteria->tags('in', array($tag));
            
            // Limit the results to 20
            $criteria->limit(20);
 
            // Find the videos
            $videos = Video::model()->findAll($criteria);
            
            Yii::app()->end();
 
            // Render the 'home' view with the retrieved video data
            $this->render('home', array('data' => $videos));
        } catch (Exception $e) {
            // Handle any errors that occur during the process
            Yii::log("Error fetching videos by tag: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            // Render an error view or redirect as needed
        }
    }
 
}
 
?>
 