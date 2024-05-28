<?php
// require_once ('/data/live/protected/tests/MongoMock.php');
// require_once ('/data/live/protected/tests/YiiAppMock.php');
// require_once ('/data/live/protected/components/helpers/UserHelper.php');

// use PHPUnit\Framework\TestCase;
use \Mockery\Adapter\Phpunit\MockeryTestCase;
use MongoDB\Client;
use Aws\S3\S3Client;
use LDAP\Result;

class UserHelperTest extends MockeryTestCase
{
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

 public function testTempFunction()
    {
        $model = new UserHelper();
        $result = $model->tempFunction();
        $this->assertEquals($result,10);
        $this->assertNotEquals($result, 20);
        $this->assertGreaterThanOrEqual($result, 30);
        $this->assertGreaterThan($result, 15);
        $this->assertLessThan($result, 5);
        $this->assertLessThanOrEqual($result, 9);
        $this->assertNotNull($result);
    }

 
 
    public function testPrimeFunction(){
        $model = new UserHelper();
        $this->assertTrue($model->primeFunction(11));
        $this->assertFalse($model->primeFunction(9));
    }


    // public function testGetAwsFunction(){
    //     $s3ClientMock = $this->getMockBuilder(S3Client::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();

    //     $s3ClientMock->expects($this->once())
    //     ->method('listObjects')
    //     ->with(['Bucket'=>'phptraining1'])
    //     ->willReturn(['Contents'=>[]]);

    //     Yii::app()->setComponent('s3Client', $s3ClientMock);
    //     UtilsHelper::getAWS();
    // }


  public function testFind(){
    $this->mongoMock->mockFind(Register::class, ["username"=> 'sandhya','email'=>'sandhya@gmail.com']);
    $res = UtilsHelper::findHelper();
    // var_dump($res);
    $this->assertEquals($res->username, "sandhya");
    
  }

  
  public function testFindAll(){
    $this->mongoMock->mockFindAll(Register::class, [["username"=> 'sandhya','email'=>'sandhya@gmail.com']]);
    $res = UtilsHelper::findAllHelper();
    // var_dump($res);

    // $this->assertEquals($res, '[{"username":"sandhya","password":null,"email":"sandhya@gmail.com","gender":null,"address":null,"rememberMe":null,"_id":null,"number":{"number":[]}}]');
    
  }
   
}
