<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use MongoDB\BSON\ObjectId;

class VideoHelperTest extends MockeryTestCase
{
    private $mongoMock;
    protected $s3ClientMock;
    protected $videoMock;
    protected $saveAttributes;

    protected function setUp(): void
    {
        $this->mongoMock = new MongoMock;
        $this->saveAttributes = [];
        $this->videoMock = (new MongoMock())->mockSave(Video::class, $this->saveAttributes);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->mongoMock->close();
        Mockery::close();
        parent::tearDown();
    }

    public function testAddVideo()
    {
        $_POST['title'] = 'Test Title';
        $_POST['desc'] = 'Test Description';
        $_POST['tags'] = ['tag1', 'tag2'];

        $userId = '60b725f10c9eb56e68f75e61';
        $imageObjectKey = 'test-image-key';
        $videoObjectKey = 'test-video-key';
        $vttObjectKey = 'test-vtt-key';

        $result = VideoHelper::addVideo($userId, $imageObjectKey, $videoObjectKey, $vttObjectKey);

        $this->assertTrue($result);
        $this->assertEquals(new ObjectId($userId), $this->saveAttributes['userId']);
        $this->assertEquals('Test Title', $this->saveAttributes['title']);
        $this->assertEquals('Test Description', $this->saveAttributes['desc']);
        $this->assertEquals($imageObjectKey, $this->saveAttributes['imgKey']);
        $this->assertEquals($videoObjectKey, $this->saveAttributes['videoKey']);
        $this->assertEquals($vttObjectKey, $this->saveAttributes['captionsKey']);
        $this->assertEquals(['tag1', 'tag2'], $this->saveAttributes['tags']);
    }

    /**
     * @dataProvider dataGetHomeVideos
     */
    public function testGetHomeVideos($expected, $video)
    {
        $this->mongoMock->mockFindAll('Video', $video, null, 15);
        $result = VideoHelper::getHomeVideos();

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataGetHomeVideos()
    {
        $video1 = [
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c88')]),
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c89')]),
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c90')])
        ];
        $expected1 = $video1;

        $video2 = [
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c93')]),
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c94')])
        ];
        $expected2 = $video2;

        return [
            [$expected1, $video1],
            [$expected2, $video2]
        ];
    }

    /**
     * @dataProvider dataGetMyVideos
     */
    public function testGetMyVideos($userId, $expected, $video)
    {
        $this->mongoMock->mockFindAll('Video', $video, null);
        $result = VideoHelper::getMyVideos($userId);

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataGetMyVideos()
    {
        $userId1 = new ObjectId('60d21b4667d0d8992e610c86');
        $video1 = [
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c88'), 'userId' => $userId1]),
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c89'), 'userId' => $userId1])
        ];
        $expected1 = $video1;

        $userId2 = new ObjectId('60d21b4667d0d8992e610c87');
        $video2 = [
            new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c90'), 'userId' => $userId2])
        ];
        $expected2 = $video2;

        return [
            [$userId1, $expected1, $video1],
            [$userId2, $expected2, $video2]
        ];
    }

    /**
     * @dataProvider dataGetLikedVideos
     */
    public function testGetLikedVideos($userId, $likedVideos, $expected)
    {
        $this->mongoMock->mockFindByPk('User', (object) ['likedVideos' => $likedVideos], new ObjectId($userId));

        $criteria = new EMongoCriteria();
        $criteria->_id('in', $likedVideos);
        $this->mongoMock->mockFindAll('Video', $expected, $criteria);

        $result = VideoHelper::getLikedVideos($userId);

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataGetLikedVideos()
    {
        $userId1 = '60d21b4667d0d8992e610c86';
        $likedVideos1 = [
            new ObjectId('60d21b4667d0d8992e610c88'),
            new ObjectId('60d21b4667d0d8992e610c89')
        ];
        $video1 = [
            new Video(['_id' => $likedVideos1[0]]),
            new Video(['_id' => $likedVideos1[1]])
        ];
        $expected1 = $video1;

        $userId2 = '60d21b4667d0d8992e610c87';
        $likedVideos2 = [
            new ObjectId('60d21b4667d0d8992e610c90')
        ];
        $video2 = [
            new Video(['_id' => $likedVideos2[0]])
        ];
        $expected2 = $video2;

        return [
            [$userId1, $likedVideos1, $expected1],
            [$userId2, $likedVideos2, $expected2]
        ];
    }

    /**
     * @dataProvider dataGetWatchLaterVideos
     */
    public function testGetWatchLaterVideos($userId, $watchLater, $expected) 
    {
        $this->mongoMock->mockFindByPk('User', (object) ['watchLater' => $watchLater], new ObjectId($userId));

        // Mocking Video::model()->findAll
        $criteria = new EMongoCriteria();
        $criteria->_id('in', $watchLater);
        $this->mongoMock->mockFindAll('Video', $expected, $criteria);

        $result = VideoHelper::getWatchLaterVideos($userId);

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataGetWatchLaterVideos() {
        $userId1 = '60d21b4667d0d8992e610c86';
        $watchLaterVideos1 = [
            new ObjectId('60d21b4667d0d8992e610c88'),
            new ObjectId('60d21b4667d0d8992e610c89')
        ];
        $video1 = [
            new Video(['_id' => $watchLaterVideos1[0]]),
            new Video(['_id' => $watchLaterVideos1[1]])
        ];
        $expected1 = $video1;

        $userId2 = '60d21b4667d0d8992e610c87';
        $watchLaterVideos2 = [
            new ObjectId('60d21b4667d0d8992e610c90')
        ];
        $video2 = [
            new Video(['_id' => $watchLaterVideos2[0]])
        ];
        $expected2 = $video2;

        return [
            [$userId1, $watchLaterVideos1, $expected1],
            [$userId2, $watchLaterVideos2, $expected2]
        ];
    }

    /**
     * @dataProvider dataTrendingVideos
     */
    public function testTrendingVideos($videos, $expected)
    {
        $criteria = new EMongoCriteria();
        $criteria->sort('views', EMongoCriteria::SORT_DESC);
        $this->mongoMock->mockFindAll('Video', $expected, $criteria);

        $result = VideoHelper::trendingVideos();

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataTrendingVideos()
    {
        $video1 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c88'), 'views' => 100]);
        $video2 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c89'), 'views' => 200]);
        $video3 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c90'), 'views' => 300]);

        $expectedVideos1 = [$video3, $video2, $video1];
        $expectedVideos2 = [$video1];

        return [
            [[$video1, $video2, $video3], $expectedVideos1],
            [[$video1], $expectedVideos2]
        ];
    }

    /**
     * @dataProvider dataGetVideoByTag
     */
    public function testGetVideoByTag($tag, $expected)
    {
        $criteria = new EMongoCriteria();
        $criteria->tags('in', [$tag]);
        $this->mongoMock->mockFindAll('Video', $expected, $criteria);

        $result = VideoHelper::getVideoByTag($tag);

        $resultArray = array_map(function($video) {
            return $video->attributes;
        }, $result);

        $expectedArray = array_map(function($video) {
            return $video->attributes;
        }, $expected);

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function dataGetVideoByTag()
    {
        $tag1 = 'webinars';
        $video1 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c88'), 'tags' => [$tag1]]);
        $video2 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c89'), 'tags' => [$tag1]]);

        $tag2 = 'events';
        $video3 = new Video(['_id' => new ObjectId('60d21b4667d0d8992e610c90'), 'tags' => [$tag2]]);

        return [
            [$tag1, [$video1, $video2]],
            [$tag2, [$video3]],
        ];
    }

    /**
     * @dataProvider dataSearch
     */
    public function testSearch($query, $mockAggregateResult, $expected)
    {
        $this->mongoMock->mockAggregate('Video', $mockAggregateResult);
        $result = VideoHelper::search($query);
        $this->assertEquals($expected, $result);
    }

    public function dataSearch()
    {
        $query1 = "process";
        $mockAggregateResult1 = [
            ['_id' => 1, 'title' => 'Process Training', 'desc' => 'Process Training Video'],
            ['_id' => 2, 'title' => 'Workflow Process', 'desc' => 'Workflow Process Explanation'],
        ];
        $expected1 = $mockAggregateResult1;
    
        $query2 = "events";
        $mockAggregateResult2 = [
            ['_id' => 3, 'title' => 'Event Coverage', 'desc' => 'Event Coverage Video'],
        ];
        $expected2 = $mockAggregateResult2;
    
        return [
            [$query1, $mockAggregateResult1, $expected1],
            [$query2, $mockAggregateResult2, $expected2],
        ];
    }


    /**
     * @dataProvider dataGetAnalytics
     */
    public function testGetAnalytics($userId, $mockFindAllResult, $expectedVideoInfo, $expectedOverall)
    {
        $this->mongoMock->mockFindAll('Video', $mockFindAllResult);

        list($videoInfo, $overall) = VideoHelper::getAnalytics($userId);

        $this->assertEquals($expectedVideoInfo, $videoInfo);

        $this->assertEquals($expectedOverall['totalViews'], $overall['totalViews']);
        $this->assertEquals($expectedOverall['totalPlays'], $overall['totalPlays']);
        $this->assertEquals($expectedOverall['totalLikes'], $overall['totalLikes']);
        $this->assertEquals($expectedOverall['totalDisLikes'], $overall['totalDisLikes']);
    }

    public function dataGetAnalytics()
    {
        $userId1 = '60d21b4667d0d8992e610c86';
        $mockFindAllResult1 = [
            (object) ['_id' => 1, 'title' => 'Video 1', 'views' => 100, 'plays' => 50, 'likes' => 20, 'dislikes' => 5],
            (object) ['_id' => 2, 'title' => 'Video 2', 'views' => 150, 'plays' => 70, 'likes' => 30, 'dislikes' => 10],
        ];
        $expectedVideoInfo1 = [
            ['title' => 'Video 1', 'views' => 100, 'plays' => 50, 'likes' => 20, 'dislikes' => 5],
            ['title' => 'Video 2', 'views' => 150, 'plays' => 70, 'likes' => 30, 'dislikes' => 10],
        ];
        $expectedOverall1 = [
            'totalViews' => 250,
            'totalPlays' => 120,
            'totalLikes' => 50,
            'totalDisLikes' => 15,
        ];
    
        $userId2 = '60d21b4667d0d8992e610c87';
        $mockFindAllResult2 = [
            (object) ['_id' => 3, 'title' => 'Video 3', 'views' => 200, 'plays' => 80, 'likes' => 40, 'dislikes' => 15],
        ];
        $expectedVideoInfo2 = [
            ['title' => 'Video 3', 'views' => 200, 'plays' => 80, 'likes' => 40, 'dislikes' => 15],
        ];
        $expectedOverall2 = [
            'totalViews' => 200,
            'totalPlays' => 80,
            'totalLikes' => 40,
            'totalDisLikes' => 15,
        ];
    
        return [
            [$userId1, $mockFindAllResult1, $expectedVideoInfo1, $expectedOverall1],
            [$userId2, $mockFindAllResult2, $expectedVideoInfo2, $expectedOverall2],
        ];
    }    

    /**
     * @dataProvider dataAddView
     */
    public function testAddView($videoId, $videoExists, $expected)
    {
        if ($videoExists) {
            $video = new Video(['_id' => $videoId, 'views' => 0]);
            $this->mongoMock->mockFindByPk('Video', $video, $videoId);
            $this->mongoMock->mockSave('Video', $video);
        } else {
            $this->mongoMock->mockFindByPk('Video', null, $videoId);
        }

        try {
            $result = VideoHelper::addView($videoId);
            $this->assertEquals($expected, $result);
        } catch (CHttpException $e) {
            if ($expected === false) {
                $this->assertEquals(404, $e->statusCode);
            } else {
                $this->fail('Unexpected CHttpException');
            }
        }
    }

    public function dataAddView()
    {
        $videoId1 = new ObjectId('60d21b4667d0d8992e610c86');
        $videoExists1 = true;
        $expected1 = false;

        $videoId2 = new ObjectId('60d21b4667d0d8992e610c87');
        $videoExists2 = false;
        $expected2 = false;

        return [
            [$videoId1, $videoExists1, $expected1],
            [$videoId2, $videoExists2, $expected2],
        ];
    }

    /**
     * @dataProvider dataAddPlay
     */
    public function testAddPlay($videoId, $videoExists, $expected)
    {
        if ($videoExists) {
            $video = new Video(['_id' => $videoId, 'plays' => 0]);
            $this->mongoMock->mockFindByPk('Video', $video, $videoId);
            $this->mongoMock->mockSave('Video', $video);
        } else {
            $this->mongoMock->mockFindByPk('Video', null, $videoId);
        }

        try {
            $result = VideoHelper::addPlay($videoId);
            $this->assertEquals($expected, $result);
        } catch (CHttpException $e) {
            if ($expected === false) {
                $this->assertEquals(404, $e->statusCode);
            } else {
                $this->fail('Unexpected CHttpException');
            }
        }
    }

    public function dataAddPlay()
    {
        $videoId1 = new ObjectId('60d21b4667d0d8992e610c86');
        $videoExists1 = true;
        $expected1 = false;

        $videoId2 = new ObjectId('60d21b4667d0d8992e610c87');
        $videoExists2 = false;
        $expected2 = false;

        return [
            [$videoId1, $videoExists1, $expected1],
            [$videoId2, $videoExists2, $expected2],
        ];
    }

    public function testDeleteVideo()
    {
        $videoId = new ObjectId();
        $videoMock = Mockery::mock('Video');
        $videoMock->_id = $videoId;
    
        $user1 = Mockery::mock('User');
        $user1->likedVideos = [$videoId];
        $user1->dislikedVideos = [];
        $user1->watchLater = [];
    
        $user2 = Mockery::mock('User');
        $user2->likedVideos = [];
        $user2->dislikedVideos = [$videoId];
        $user2->watchLater = [];
    
        $user3 = Mockery::mock('User');
        $user3->likedVideos = [];
        $user3->dislikedVideos = [];
        $user3->watchLater = [$videoId];
    
        $user1->shouldReceive('getIterator')->andReturn(new ArrayIterator($user1));
        $user2->shouldReceive('getIterator')->andReturn(new ArrayIterator($user2));
        $user3->shouldReceive('getIterator')->andReturn(new ArrayIterator($user3));
    
        $mongoMock = new MongoMock();
        $mongoMock->mockFindAll('User', [$user1, $user2, $user3]);
    
        $videoMock->shouldReceive('delete')->once()->andReturn(true);
    
        $result = VideoHelper::deleteVideo($videoMock);
    
        $this->assertTrue($result);
    }
    
}

?>