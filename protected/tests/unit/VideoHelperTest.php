<?php


use \Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use MockeryPHPUnitIntegration;

class VideoHelperTest extends MockeryTestCase {

    

    private $yiiAppMock;
    private $mongoMock;

    protected function setUp(): void
    {
        $this->mongoMock = new MongoMock;
        $this->yiiAppMock = new YiiAppMock;
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->mongoMock->close();
        Mockery::close();
        parent::tearDown();
    }

    public function testHomeHelper(){

        $video1 = new Video();
        $video2 = new Video();

        $expectedVideos = [$video1, $video2]; 

        $this->mongoMock->mockFindAll('Video', $expectedVideos, null);

        $actualVideos = VideoHelper::homeHelper();

        $this->assertEquals($expectedVideos, $actualVideos);

    }


    // public function testMyVideosHelper(){

    //     $user_id = '12345'; // Replace with actual user ID
    //     $this->yiiAppMock->mockSession(['user_id' => $user_id]);
    
    //     // Create mock videos
    //     $video = ['_id' => '1234567890'];

    //     $this->mongoMock->mockFindAll('Video', $video, null);

    //     $result = VideoHelper::myVideosHelper();

    //     var_dump('sample.....' ,$result);

    //     $this->assertEquals($expected, $result);

    // }








}

?>