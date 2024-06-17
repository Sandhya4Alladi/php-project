<?php

use \Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

$dotenv = Dotenv\Dotenv::createImmutable('/data/live');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

    /**
    * @runTestsInSeparateProcesses
    */

class AuthHelperTest extends MockeryTestCase
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
        $this->yiiAppMock->close();
        $this->mongoMock->close();
        Mockery::close();
        m::close();
        parent::tearDown();
    }



    public function testJwtHelperWithValidTokenAndCookie()
    {
        // Mock the session
        $this->yiiAppMock->mockSession(['jwt_token' => 'valid_jwt_token']);

        // Set up the expected decoded token
        $decodedToken = (object) ['user_id' => '12345'];

        // Mock the JWT decode method
        m::mock('alias:Firebase\JWT\JWT')
            ->shouldReceive('decode')
            ->with('valid_jwt_token', m::type(Key::class))
            ->andReturn($decodedToken);

        // Mock the cookie
        $_COOKIE['jwt_token'] = 'valid_jwt_token';

        // Test the jwtHelper method
        $result = AuthHelper::jwtHelper();

        // Assertions
        $this->assertTrue($result);
        $this->assertEquals('12345', Yii::app()->session['user_id']);
    }

    public function testJwtHelperValidToken()
    {
        $jwtSecretKey = 'your_jwt_secret_key';
        $_ENV['JWT_SECRET_KEY'] = $jwtSecretKey;

        $userId = 123;
        $token = Firebase\JWT\JWT::encode(['user_id' => $userId], $jwtSecretKey);

        $this->yiiAppMock->mockSession(['jwt_token' => $token]);

        $this->assertTrue(AuthHelper::jwtHelper());
        $this->assertEquals($userId, Yii::app()->session['user_id']);
    }

   

    public function testJwtHelperInvalidToken()
    {
        $this->yiiAppMock->mockSession(['jwt_token' => 'invalid_token']);

        $this->expectOutputString('Invalid Token');
        $this->assertFalse(AuthHelper::jwtHelper());
    }



    public function testJwtHelperWithoutToken()
    {
        // $this->yiiAppMock->mockSession([]);
        Yii::app()->session['jwt_token'] = null;

        $this->assertFalse(AuthHelper::jwtHelper());
    }


    public function testSignUp()
    {
        $postData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $this->yiiAppMock->mockSession(['email' => $postData['email']]);


        $userMock = $this->mongoMock->mockSave(User::class, $savedAttributes);

        $this->assertTrue(AuthHelper::signUp($postData));
        $this->assertEquals($postData['email'], $savedAttributes['email']);
        $this->assertTrue(password_verify($postData['password'], $savedAttributes['password']));
    }
   

    public function testSignUpWithEmptyData()
    {
        $postData = [];

      
        Yii::app()->session['email'] = null;

        $result = AuthHelper::signUp($postData);

      
        $this->assertFalse($result);
    }


    public function testLogin()
    {
        $postData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $hashedPassword = password_hash($postData['password'], PASSWORD_BCRYPT);
        $userMock = $this->mongoMock->mockFind(User::class, (object)['email' => $postData['email'], 'password' => $hashedPassword]);

        $this->yiiAppMock->mockSession([]);

        $this->assertTrue(AuthHelper::login($postData));
        $this->assertNotNull(Yii::app()->session['jwt_token']);
    }

    public function testLoginWithEmptyData()
    {
        $postData = [];

        // Mock the session
        Yii::app()->session['email'] = null;

        // Call the signUp method
        $result = AuthHelper::login($postData);

        // Assertions
        $this->assertFalse($result);
    }


    public function testMail()
    {
        $postData = [
            'email' => 'test@example.com'
        ];

        $_ENV['SENDERMAIL'] = 'sender@example.com';
        $_ENV['APP_KEY'] = 'your_app_key';
        $_ENV['SENDERNAME'] = 'Sender Name';

        $this->yiiAppMock->mockSession([]);

        $this->assertTrue(AuthHelper::mail($postData));
        $this->assertEquals($postData['email'], Yii::app()->session['email']);
        $this->assertNotNull(Yii::app()->session['otp']);
    }

    /**
     * @dataProvider dataVerifyMail
     */
    public function testVerifyMail($data,$exptected,$user){
        // $modelMock = Mockery::mock('User')->makePartial();
        // $modelMock->shouldReceive('find')->andReturn("123");
        // $user = ['email' => "1234567890"];
        $this->mongoMock->mockFind('User',$user,null);
        $result = AuthHelper::verifyMail($data);
        $this->assertEquals($exptected,$result);
    }

    public function dataVerifyMail(){
        $data = [
            "email" => '1234',
        ];
        $user1 =  ['email' => "1234567890"];
        $user2 = null;
        return [
            [$data,true,$user1],
            [$data,false,$user2],

        ];
    }


    /**
     * @dataProvider dataResetPw
     */
    

    public function testResetPw($data, $exptected, $user){


        $this->yiiAppMock->mockSession(['email'=>$user['email']]);
        $this->mongoMock->mockFind('User', $user, null);
        $result = AuthHelper::resetPw($data);
        // var_dump($result);
        // var_dump($exptected);
        $this->assertEquals($exptected, $result);


    }

    public function dataResetPw(){

        $data1 = [
            "password" => "123",
            "confirm_password" => "123"
        ];

        $data2 = [
            "password" => "123",
            "confirm_password" => "12345"
        ];

        $user = ['email' => "test@gmail.com"];
        

        return[
            [$data1, true, $user],
            [$data2, false, $user]
        ];

    }
    

    public function testGenerateOTP(){

        for ($i = 0; $i < 1000; $i++) {
            $otp = AuthHelper::generateOTP();
            
            $this->assertIsInt($otp);
            $this->assertGreaterThanOrEqual(100000, $otp);
            $this->assertLessThanOrEqual(999999, $otp);
        }

    }

    

}
