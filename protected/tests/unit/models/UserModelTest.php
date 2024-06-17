<?php
 
use Mockery\Adapter\Phpunit\MockeryTestCase;
 
class UserModelTest extends MockeryTestCase
{
    protected $user;
 
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }
 
    public function testValidationRules()
    {
        // Required fields
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
 
    public function testBeforeSave()
    {
        $this->user->username = "testuser";
        $this->user->email = "test@example.com";
        $this->user->password = "password";
 
        $this->assertTrue($this->user->save());
 
        $this->assertNotNull($this->user->createdAt);
        $this->assertNotNull($this->user->updatedAt);
 
        $createdAt = $this->user->createdAt;
 
        // Simulate update
        $this->user->email = "updated@example.com";
        $this->assertTrue($this->user->save());
 
        $this->assertEquals($createdAt, $this->user->createdAt);
        $this->assertNotEquals($createdAt, $this->user->updatedAt);
    }
 
    public function testSave()
    {
        $this->user->username = "testuser";
        $this->user->email = "test@example.com";
        $this->user->password = "password";
 
        $this->assertTrue($this->user->save());
        $this->assertNotNull(User::model()->findByPk($this->user->_id));
    }
 
    protected function tearDown(): void
    {
        if (!$this->user->getIsNewRecord()) {
            $this->user->delete();
        }
        parent::tearDown();
    }
}
 
?>
 
 