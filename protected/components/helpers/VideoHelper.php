<?php

use MongoDB\BSON\ObjectId;

    class VideoHelper{


        public static function homeHelper(){

                    $criteria = new EMongoCriteria();
                    $criteria->setLimit(15);
                    $videos = Video::model()->findAll($criteria);
                    return $videos;

        }

        // public static function myVideosHelper(){

        //     $criteria = new EMongoCriteria();
        //     $criteria->userId = new ObjectId(Yii::app()->session['user_id']);
 
        //     $videos = Video::model()->findAll($criteria);

        //     return $videos;

        // }

    }   
        
    

?>