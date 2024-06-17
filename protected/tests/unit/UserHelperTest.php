<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;


class UserHelperTest extends MockeryTestCase {

    private $yiiAppMock;
    private $mongoMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mongoMock = new MongoMock;
        $this->yiiAppMock = new YiiAppMock;
    }

    protected function tearDown(): void
    {
        $this->mongoMock->close();
        $this->yiiAppMock->close();
        parent::tearDown();
    }

    public function testGetUserProfile()
    {
      
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

  
        $mockUserCollection = $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'username' => 'test_user']
        );

       
        $result = UserHelper::getUserProfile();

      
        $this->assertInstanceOf('User', $result); 
        $this->assertEquals('5ff9e4b9f1639b066c4d2e51', $result->_id);
        $this->assertEquals('test_user', $result->username);
    }


    public function testUpdateUserProfileSuccess()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'), 'username' => 'old_username', 'email' => 'old_email@example.com'];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $saveAttributes = [];
        $this->mongoMock->mockSave('User', $saveAttributes);

        $requestData = [
            'username' => 'new_username',
            'email' => 'new_email@example.com'
        ];

        
        $result = UserHelper::updateUserProfile('5ff9e4b9f1639b066c4d2e51', $requestData);

        $this->assertEquals('new_username', $result->username);
        $this->assertEquals('new_email@example.com', $result->email);
        $this->assertEquals($requestData, $saveAttributes);
    }

    public function testUpdateUserProfileUserNotFound()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $this->mongoMock->mockFindByPk('User', null);

        $requestData = [
            'username' => 'new_username',
            'email' => 'new_email@example.com'
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found.');

        
        UserHelper::updateUserProfile('5ff9e4b9f1639b066c4d2e51', $requestData);
    }

    public function testUpdateUserProfileSaveFailure()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'), 'username' => 'old_username', 'email' => 'old_email@example.com'];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $userMock->shouldReceive('save')->andReturn(false);

        $requestData = [
            'username' => 'new_username',
            'email' => 'new_email@example.com'
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update user.');

       
        UserHelper::updateUserProfile('5ff9e4b9f1639b066c4d2e51', $requestData);
    }

    public function testUpdateUserProfileUnauthorized()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        $requestData = [
            'username' => 'new_username',
            'email' => 'new_email@example.com'
        ];

        
        $result = UserHelper::updateUserProfile('5ff9e4b9f1639b066c4d2e52', $requestData);

        $this->assertEquals(['message' => 'You can update only your account!'], $result);
    }


    public function testDeleteUserProfileSuccess()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $userMock = (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51')];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $userMock->shouldReceive('delete')->andReturn(true);

      
        $result = UserHelper::deleteUserProfile('5ff9e4b9f1639b066c4d2e51');

        $this->assertEquals(['status' => 200], $result);
    }

    public function testDeleteUserProfileUserNotFound()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $this->mongoMock->mockFindByPk('User', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found.');

       
        UserHelper::deleteUserProfile('5ff9e4b9f1639b066c4d2e51');
    }

    public function testDeleteUserProfileDeleteFailure()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51')];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $userMock->shouldReceive('delete')->andReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to delete user.');

        
        UserHelper::deleteUserProfile('5ff9e4b9f1639b066c4d2e51');
    }

    public function testDeleteUserProfileUnauthorized()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $result = UserHelper::deleteUserProfile('5ff9e4b9f1639b066c4d2e52');

        $this->assertEquals(['message' => 'You can delete only your account!'], $result);
    }



    public function testLikeVideo()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $mockUserCollection = $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'likedVideos' => []]
        );

        $mockVideoCollection = $this->mongoMock->mockFind(
            'Video', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e52', 'likes' => 0]
        );

        $data = ['id' => '5ff9e4b9f1639b066c4d2e52'];

     
        $result = UserHelper::likeVideo($data);

        
        $this->assertEquals("The video has been liked.", $result);
    }


    public function testLikeVideoSuccess()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [],
            'dislikedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);
        $this->mongoMock->mockFindByAttributes('User', null);

        
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 0,
            'dislikes' => 1
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

      
        $result = UserHelper::likeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been liked.", $result);
        $this->assertEquals([$videoMock->_id], $userMock->likedVideos);
        $this->assertEquals([], $userMock->dislikedVideos);
        $this->assertEquals(1, $videoMock->likes);
        $this->assertEquals(0, $videoMock->dislikes);
    }

    public function testLikeVideoUserNotFound()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $this->mongoMock->mockFindByPk('User', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found');

        
        UserHelper::likeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);
    }

    public function testLikeVideoAlreadyLiked()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')],
            'dislikedVideos' => []
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);

       
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 1,
            'dislikes' => 0
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

        
        $result = UserHelper::likeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been liked.", $result);
        $this->assertEquals([new ObjectId('5ff9e4b9f1639b066c4d2e52')], $userMock->likedVideos);
        $this->assertEquals(1, $videoMock->likes);
    }

    public function testLikeVideoDislikeRemoved()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [],
            'dislikedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);
        $this->mongoMock->mockFindByAttributes('User', $userMock);

       
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 0,
            'dislikes' => 1
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

        
        $result = UserHelper::likeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been liked.", $result);
        $this->assertEquals([new ObjectId('5ff9e4b9f1639b066c4d2e52')], $userMock->likedVideos);
        $this->assertEquals([], $userMock->dislikedVideos);
        $this->assertEquals(1, $videoMock->likes);
        $this->assertEquals(0, $videoMock->dislikes);
    }


    public function testDislikeVideo()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $mockUserCollection = $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'likedVideos' => ['5ff9e4b9f1639b066c4d2e52']]
        );

        $mockVideoCollection = $this->mongoMock->mockFind(
            'Video', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e52', 'likes' => 1, 'dislikes' => 0]
        );

        $data = ['id' => '5ff9e4b9f1639b066c4d2e52'];

       
        $result = UserHelper::dislikeVideo($data);

        
        $this->assertEquals("The video has been disliked.", $result);
    }

    public function testDislikeVideoSuccess()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [],
            'dislikedVideos' => []
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);
        $this->mongoMock->mockFindByAttributes('User', null);

        
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 0,
            'dislikes' => 1
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

       
        $result = UserHelper::dislikeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been disliked.", $result);
        $this->assertEquals([$videoMock->_id], $userMock->dislikedVideos);
        $this->assertEquals(0, $videoMock->likes);
        $this->assertEquals(1, $videoMock->dislikes);
    }

    public function testDislikeVideoUserNotFound()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $this->mongoMock->mockFindByPk('User', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found');

        
        UserHelper::dislikeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);
    }

    public function testDislikeVideoAlreadyDisliked()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

      
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [],
            'dislikedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 0,
            'dislikes' => 1
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

       
        $result = UserHelper::dislikeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been disliked.", $result);
        $this->assertEquals([new ObjectId('5ff9e4b9f1639b066c4d2e52')], $userMock->dislikedVideos);
        $this->assertEquals(0, $videoMock->likes);
        $this->assertEquals(1, $videoMock->dislikes);
    }

    public function testDislikeVideoLikeRemoved()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'likedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')],
            'dislikedVideos' => []
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);
        $this->mongoMock->mockFindByAttributes('User', $userMock);

        
        $videoMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e52'),
            'likes' => 1,
            'dislikes' => 0
        ];
        $this->mongoMock->mockFindByPk('Video', $videoMock);

      
        $result = UserHelper::dislikeVideo(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been disliked.", $result);
        $this->assertEquals([$videoMock->_id], $userMock->dislikedVideos);
        $this->assertEquals([], $userMock->likedVideos);
        $this->assertEquals(0, $videoMock->likes);
        $this->assertEquals(1, $videoMock->dislikes);
    }


    public function testAddWatchLater()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $mockUserCollection = $this->mongoMock->mockFind(
            'User',
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'watchLater' => []]
        );

        $data = ['id' => '5ff9e4b9f1639b066c4d2e52'];

     
        $result = UserHelper::addWatchLater($data);

        $this->assertEquals("The video has been added to watch later.", $result);
    }

    public function testAddWatchLaterSuccess()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'watchLater' => []
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);

        
        $result = UserHelper::addWatchLater(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been added to watch later.", $result);
        $this->assertEquals([new ObjectId('5ff9e4b9f1639b066c4d2e52')], $userMock->watchLater);
    }

    public function testAddWatchLaterUserNotFound()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $this->mongoMock->mockFindByPk('User', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found');

      
        UserHelper::addWatchLater(['id' => '5ff9e4b9f1639b066c4d2e52']);
    }

    public function testAddWatchLaterAlreadyAdded()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

       
        $userMock = (object)[
            '_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'),
            'watchLater' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]
        ];
        $this->mongoMock->mockFindByPk('User', $userMock);

   
        $result = UserHelper::addWatchLater(['id' => '5ff9e4b9f1639b066c4d2e52']);

        $this->assertEquals("The video has been added to watch later.", $result);
        $this->assertEquals([new ObjectId('5ff9e4b9f1639b066c4d2e52')], $userMock->watchLater);
    }


    public function testTrackStatusWatched()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $mockUserCollection = $this->mongoMock->mockFind(
            'User',
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'watchLater' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]]
        );

     
        $result = UserHelper::trackStatus('5ff9e4b9f1639b066c4d2e52');

      
        $this->assertEquals(['watched' => 1], $result);
    }

    public function testTrackStatusNotWatched()
    {
     
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

     
        $mockUserCollection = $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => '5ff9e4b9f1639b066c4d2e51', 'watchLater' => []]
        );

       
        $result = UserHelper::trackStatus('5ff9e4b9f1639b066c4d2e52');

       
        $this->assertEquals(['watched' => 0], $result);
    }

    public function testTrackStatusUserNotFound()
    {
      
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

   
        $this->mongoMock->mockFindByAttributes('User', null);

       
        $result = UserHelper::trackStatus('5ff9e4b9f1639b066c4d2e52');

     
        $this->assertEquals(['watched' => 0], $result);
    }

    public function testTrackStatusException()
    {
        
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $this->mongoMock->mockFindByAttributes('User', new Exception('Database error'));

        $mockLogger = $this->getMockBuilder('Logger')
                           ->disableOriginalConstructor()
                           ->getMock();
        Yii::setLogger($mockLogger);

    
        $mockLogger->expects($this->once())
                   ->method('log')
                   ->with('Database error', CLogger::LEVEL_ERROR);

        
        $result = UserHelper::trackStatus('5ff9e4b9f1639b066c4d2e52');

     
        $this->assertEquals(['watched' => 0], $result);
    }


    public function testCheckVideoStatusLiked()
    {
      
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

     
        $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'), 'likedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]]
        );

        $videoId = '5ff9e4b9f1639b066c4d2e52';


        $result = UserHelper::checkVideoStatus($videoId);

        $this->assertEquals(['liked' => 1, 'disliked' => 0], $result);
    }

    public function testCheckVideoStatusDisliked()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

   
        $this->mongoMock->mockFind(
            'User', 
            (object) ['_id' => new ObjectId('5ff9e4b9f1639b066c4d2e51'), 'dislikedVideos' => [new ObjectId('5ff9e4b9f1639b066c4d2e52')]]
        );

        $videoId = '5ff9e4b9f1639b066c4d2e52';

   
        $result = UserHelper::checkVideoStatus($videoId);

        $this->assertEquals(['liked' => 0, 'disliked' => 1], $result);
    }

    public function testCheckVideoStatusNeitherLikedNorDisliked()
    {
       
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        
        $this->mongoMock->mockFind(
            'User', 
            null 
        );

        $videoId = '5ff9e4b9f1639b066c4d2e52';

      
        $result = UserHelper::checkVideoStatus($videoId);

        $this->assertEquals(['liked' => 0, 'disliked' => 0], $result);
    }

    public function testCheckVideoStatusException()
    {
        // Mock Yii application and session
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);

        // Mock MongoDB find operation for User::model()
        $mock = $this->mongoMock->mockFind(
            'User', 
            null // No liked or disliked videos
        );

        $mock->shouldReceive('findByAttributes')->andThrow(new Exception('Database error'));

        $videoId = '5ff9e4b9f1639b066c4d2e52';

        // Test
        $result = UserHelper::checkVideoStatus($videoId);

        $this->assertEquals(['error' => 'Database error'], $result);
    }
    

}

?>
