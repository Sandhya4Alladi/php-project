<?php
 
use Mockery\Adapter\Phpunit\MockeryTestCase;
 
class VideoModelTest extends MockeryTestCase
{
    protected $video;
 
    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function testValidationRules()
    {
        $this->video->userId = null;
        $this->video->title = null;
        $this->video->desc = null;
        $this->video->imgKey = null;
        $this->video->videoKey = null;
        $this->video->tags = null;
        
        $this->assertFalse($this->video->validate());
        $this->assertArrayHasKey('userId', $this->video->getErrors());
        $this->assertArrayHasKey('title', $this->video->getErrors());
        $this->assertArrayHasKey('desc', $this->video->getErrors());
        $this->assertArrayHasKey('imgKey', $this->video->getErrors());
        $this->assertArrayHasKey('videoKey', $this->video->getErrors());
        $this->assertArrayHasKey('tags', $this->video->getErrors());
 
        $this->video->views = 'string';
        $this->video->plays = 'string';
        $this->video->likes = 'string';
        $this->video->dislikes = 'string';
 
        $this->assertFalse($this->video->validate());
        $this->assertArrayHasKey('views', $this->video->getErrors());
        $this->assertArrayHasKey('plays', $this->video->getErrors());
        $this->assertArrayHasKey('likes', $this->video->getErrors());
        $this->assertArrayHasKey('dislikes', $this->video->getErrors());
    }
 
    public function testInitialization()
    {
        $video = new Video();
        $this->assertEquals(0, $video->views);
        $this->assertEquals(0, $video->plays);
        $this->assertEquals(0, $video->likes);
        $this->assertEquals(0, $video->dislikes);
    }
 
    public function testBeforeSave()
    {
        $this->video->userId = 1;
        $this->video->title = "Sample Video";
        $this->video->desc = "Description of the video";
        $this->video->imgKey = "img_key";
        $this->video->videoKey = "video_key";
        $this->video->tags = ['sample', 'video'];
 
        $this->assertTrue($this->video->save());
 
        $this->assertNotNull($this->video->createdAt);
        $this->assertNotNull($this->video->updatedAt);
 
        $createdAtSec = $this->video->createdAt->sec;
 
        $this->video->title = "Updated Title";
        $this->assertTrue($this->video->save());
 
        $this->assertEquals($createdAtSec, $this->video->createdAt->sec);
       
    }
 
 
}
 
?>