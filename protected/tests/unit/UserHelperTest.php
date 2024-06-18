<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;


class UserHelperTest extends MockeryTestCase
{

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


    /**
     * @dataProvider dataforUpdateUserProfile
     */
    public function testUpdateUserProfile($userId, $id, $data, $user, $saveResult, $expected)
    {

        $this->yiiAppMock->mockSession(['user_id' => $id]);


        $userMock = $this->mongoMock->mockFindByPk(User::class, $user, [$userId]);

        if ($user !== null) {

            $userMock->shouldReceive('save')
                ->andReturn($saveResult)
                ->byDefault();
        }


        $result = UserHelper::updateUserProfile($userId, $data);


        $this->assertEquals($expected, $result);
    }

    public function dataforUpdateUserProfile()
    {
        $userId = "5ff9e4b9f1639b066c4d2e51";
        $expectedSuccess = "User updated successfully";
        $expectedFailure = "Failed to update user.";
        $expectedNotFound = "User not found.";
        $expectedNotAuthorized = "You can update only your account!";

        $data = [
            'email' => "wertyui@efgh.com",
            'username' => 'qwertuiop'
        ];

        $user = [
            'username' => 'oldusername',
            'email' => 'oldemail@domain.com'
        ];

        return [

            // [$userId, $userId, $data, $user, true, $expectedSuccess],

            [$userId, $userId, $data, $user, false, $expectedFailure],

            [$userId, $userId, $data, null, null, $expectedNotFound],

            [$userId, "5ff9e4b9f1639b066c4d2e52", $data, null, null, $expectedNotAuthorized]
        ];
    }



    /**
     * @dataProvider dataforDeleteUserProfile
     */

    public function testDeleteUserProfile($userId, $id, $user, $deleteResult, $expected)
    {
        $this->yiiAppMock->mockSession(['user_id' => $id]);
        $userMock = $this->mongoMock->mockFindByPk(User::class, $user, [$userId]);
        if ($user !== null) {
            $userMock->shouldReceive('deleteOne')
                ->andReturn($deleteResult)
                ->byDefault();
        }
        $result = UserHelper::deleteUserProfile($userId);
        $this->assertEquals($expected, $result);
    }

    public function dataforDeleteUserProfile()
    {

        $userId = "5ff9e4b9f1639b066c4d2e51";
        $expectedSuccess = "User deleted successfully";
        $expectedFailure = "Failed to delete the user";
        $expectedNotFound = "User not found";
        $expectedNotAuthorized = "You can delete only your account!";

        $user = [
            'email' => 'sdfbhn@sdcv.com'
        ];

        return [

            [$userId, $userId, $user, true, $expectedSuccess],

            [$userId, $userId, $user, false, $expectedFailure],

            [$userId, $userId, null, null, $expectedNotFound],

            [$userId, "5ff9e4b9f1639b066c4d2e52", null, null, $expectedNotAuthorized]
        ];
    }



    /**
     * @dataProvider dataforLikeVideo
     */

    public function testLikeVideo($userId, $id, $user, $video, $expected)
    {

        $this->yiiAppMock->mockSession(['user_id' => $userId]);
        $this->mongoMock->mockFindByPk(User::class, $user, [$id, $user]);
        $this->mongoMock->mockFindByPk(Video::class, $video, ['_id' => $id, $user]);
        $result = UserHelper::likeVideo($id);
        $this->assertEquals($expected, $result);
    }


    public function dataforLikeVideo()
    {


        $userId = "5ff9e4b9f1639b066c4d2e51";
        $id = ["id" => "5ff9e4b9f1639b066c4d2e52"];
        $user = [
            "email" => "sdfghjk@wdfg.com",
            "likedVideos" => [],
            "dislikedVideos" => $id
        ];

        $video = [
            "_id" => $id['id'],
            "likes" => 0,
            "dislikes" => 1
        ];
        $expected1 = "The video has been liked.";
        $expected2 = "User not found";

        return [
            [$userId, $id, $user, $video, $expected1],
            [$userId, $id, null, null, $expected2]
        ];
    }


    /**
     * @dataProvider dataforDislikeVideo
     */

    public function testDislikeVideo($userId, $id, $user, $video, $expected)
    {

        $this->yiiAppMock->mockSession(['user_id' => $userId]);
        $this->mongoMock->mockFindByPk(User::class, $user, [$id, $user]);
        $this->mongoMock->mockFindByPk(Video::class, $video, ['_id' => $id, $video]);
        $result = UserHelper::dislikeVideo($id);
        $this->assertEquals($expected, $result);
    }

    public function dataforDislikeVideo()
    {

        $userId = '5ff9e4b9f1639b066c4d2e51';
        $id = ["id" => "5ff9e4b9f1639b066c4d2e52"];
        $user = [
            "email" => "sdfgh@fghj.com",
            "likedVideos" => $id,
            "dislikedVideos" => []
        ];
        $video = [
            "_id" => $id['id'],
            "likes" => 1,
            "dislikes" => 0
        ];
        $expected1 = 'The video has been disliked.';
        $expected2 = 'User not found';

        return [
            [$userId, $id, $user, $video, $expected1],
            [$userId, $id, null, null, $expected2]
        ];
    }



    /**
     * @dataProvider dataforAddWatchLater
     */

    public function testAddWatchLater1($userId, $id, $user, $expected)
    {

        $this->yiiAppMock->mockSession(['user_id' => $userId]);
        $this->mongoMock->mockFind(User::class, $user);
        $result = UserHelper::addWatchLater($id);
        $this->assertEquals($expected, $result);
    }

    public function dataforAddWatchLater()
    {

        $userId = '5ff9e4b9f1639b066c4d2e51';
        $id = ['id' => '5ff9e4b9f1639b066c4d2e52'];
        $expected1 = 'The video has been added to watch later.';
        $expected2 = "User not found";
        $user = [
            "email" => 'tevggf@gm.com'
        ];

        return [
            [$userId, $id, $user, $expected1],
            [$userId, $id, null, $expected2]
        ];
    }


    /**
     * @dataProvider dataforTrackStatus
     */

    public function testTrackStatus($userId, $id, $user, $expected)
    {

        $this->yiiAppMock->mockSession(['user_id' => $userId]);
        $this->mongoMock->mockFind(User::class, $user, ['_id' => $userId, 'watchLater' => $id]);
        $result = UserHelper::trackStatus($id);
        $this->assertEquals($expected, $result);
    }

    public function dataforTrackStatus()
    {

        $userId = "5ff9e4b9f1639b066c4d2e51";

        $id = "5ff9e4b9f1639b066c4d2e52";
        $expected1 = ['watched' => 1];
        $expected2 = ['watched' => 0];
        $user = [
            'email' => 'jj@ghg.jd',
        ];
        return [
            [$userId, $id, $user, $expected1],
            [$userId, $id, null, $expected2]
        ];
    }


    /**
     * @dataProvider dataforCheckVideos
     */
    public function testCheckVideoStatus($userId, $id, $disliked, $liked, $expected)
    {
        $this->yiiAppMock->mockSession(['user_id' => '5ff9e4b9f1639b066c4d2e51']);
        $this->mongoMock->mockFind(User::class, $disliked, ['_id' => $userId, 'dislikedVideos' => $id]);
        $this->mongoMock->mockFind(User::class, $liked, ['_id' => $userId, 'likedVideos' => $id]);
        $result = UserHelper::checkVideoStatus($id);
        $this->assertEquals($expected, $result);
    }

    public function dataforCheckVideos()
    {
        $userId = '5ff9e4b9f1639b066c4d2e51';
        $id = '5ff9e4b9f1639b066c4d2e52';
        $disliked = [
            'email' => "abc@gmail.com",
        ];
        $liked = [
            'email' => 'abs@hj.cnkn',
        ];
        $expected1 = ['liked' => 0, 'disliked' => 1];
        $expected2 = ['liked' => 1, 'disliked' => 0];
        $expected3 = ['liked' => 0, 'disliked' => 0];

        return [
            [$userId, $id, $disliked, null, $expected1],
            [$userId, $id, null, $liked, $expected2],
            [$userId, $id, null, null, $expected3]
        ];
    }
}
