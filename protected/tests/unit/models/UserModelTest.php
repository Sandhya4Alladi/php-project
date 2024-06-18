<?php
 
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
 
class UserModelTest extends MockeryTestCase
{
    protected $user;
 
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
 
    public function testValidationRules()
    {
     
        $this->user->username = null;
        $this->user->email = null;
        $this->user->password = null;
 
        $this->assertFalse($this->user->validate());
        $this->assertArrayHasKey('username', $this->user->getErrors());
        $this->assertArrayHasKey('email', $this->user->getErrors());
        $this->assertArrayHasKey('password', $this->user->getErrors());
    }
 
    public function testInitialization()
    {
        $user = new User();
        $this->assertIsArray($user->likedVideos);
        $this->assertIsArray($user->dislikedVideos);
        $this->assertIsArray($user->watchLater);
    }
 
    
    public function testBeforeSaveNewRecord()
    {
        $user = m::mock('User[getIsNewRecord, parent::beforeSave, updateTimestamps]')
                 ->makePartial()
                 ->shouldAllowMockingProtectedMethods();
        $user->shouldReceive('getIsNewRecord')->andReturn(true);
        $user->shouldReceive('parent::beforeSave')->andReturn(true);
        $user->shouldReceive('updateTimestamps')->once()->passthru();
 
        $result = $user->beforeSave();
 
        $this->assertTrue($result);
        $this->assertNotNull($user->createdAt);
        $this->assertNotNull($user->updatedAt);
        $this->assertEquals($user->createdAt, $user->updatedAt);
    }
 
    public function testBeforeSaveExistingRecord()
    {
        $user = m::mock('User[getIsNewRecord, parent::beforeSave, updateTimestamps]')
                 ->makePartial()
                 ->shouldAllowMockingProtectedMethods();
        $user->shouldReceive('getIsNewRecord')->andReturn(false);
        $user->shouldReceive('parent::beforeSave')->andReturn(true);
        $user->shouldReceive('updateTimestamps')->once()->passthru();
 
        $createdAt = new MongoDate(strtotime('-1 day'));
        $user->createdAt = $createdAt;
 
        $result = $user->beforeSave();
 
        $this->assertTrue($result);
        $this->assertNotNull($user->updatedAt);
        $this->assertEquals($createdAt, $user->createdAt);
        $this->assertNotEquals($createdAt, $user->updatedAt);
    }
    
 
}
 
?>