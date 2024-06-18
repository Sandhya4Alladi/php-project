<?php

use MongoDB\BSON\ObjectId;

class VideoHelper 
{
    public static function addVideo($userId, $imageObjectKey, $videoObjectKey, $vttObjectKey)
    {
        $newVideo = new Video();
        $newVideo->userId = new ObjectId($userId);
        $newVideo->title = $_POST['title'];
        $newVideo->desc = $_POST['desc'];
        $newVideo->imgKey = $imageObjectKey;
        $newVideo->videoKey = $videoObjectKey;
        $newVideo->captionsKey = $vttObjectKey;
        $newVideo->tags = $_POST['tags']; 
        $res = $newVideo->save();
        return $res;
    }

    public static function getHomeVideos()
    {
        $criteria = new EMongoCriteria();
        $criteria->setLimit(15);
        $videos = Video::model()->findAll($criteria);
        return $videos;
    }

    public static function getMyVideos($userId)
    {
        $criteria = new EMongoCriteria();
        $criteria->userId = new ObjectId($userId);
        $videos = Video::model()->findAll($criteria);
        return $videos;
    }

    public static function getLikedVideos($userId) {
      
        $user = User::model()->findByPk(new MongoDB\BSON\ObjectId($userId));
        $criteria = new EMongoCriteria();
        $criteria->_id('in', $user->likedVideos);
        $likedVideos = Video::model()->findAll($criteria);

        return $likedVideos;
    }

    public static function getWatchLaterVideos($userId) {
        $user = User::model()->findByPk(new MongoDB\BSON\ObjectId($userId));
        $criteria = new EMongoCriteria();
        $criteria->_id('in', $user->watchLater);
        $watchLater = Video::model()->findAll($criteria);
        return $watchLater;
    }

    public static function deleteVideo($video)
    {
        $videoId = $video->_id;
    
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
    
        return $video->delete();
    }
    

    public static function addView($videoId)
    {
        $video = Video::model()->findByPk($videoId);
        if (!$video) {
            throw new CHttpException(404, "Video Not Found");
        }
        $video->views += 1;
        $res = $video->save();
        return $res;
    }

    public static function addPlay($videoId)
    {
        $video = Video::model()->findByPk($videoId);
        if (!$video) {
            throw new CHttpException(404, "Video Not Found");
        }
        $video->plays += 1;
        $res = $video->save();
        return $res;
    }

    public static function trendingVideos()
    {
        $criteria = new EMongoCriteria();
        $criteria->sort('views', EMongoCriteria::SORT_DESC);
        $videos = Video::model()->findAll($criteria);
        return $videos;
    }

    public static function getVideoByTag($tag)
    {
        $criteria = new EMongoCriteria();
        $criteria->tags('in', array($tag));
        $videos = Video::model()->findAll($criteria);
        return $videos;
    }

    public static function search($query)
    {
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

        $videos = Video::model()
            ->getCollection()
            ->aggregate($pipeline);
        
        $videosArray = iterator_to_array($videos);
        return $videosArray;
    }

    public static function getAnalytics($userId)
    {
        $userId =  new MongoDB\BSON\ObjectId($userId);

        $criteria = new EMongoCriteria();
        $criteria->userId = $userId;
        $criteria->select(array('title', 'views', 'plays', 'likes', 'dislikes'));
        
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

        return array($videoinfo, $overall);
    }
}

?>