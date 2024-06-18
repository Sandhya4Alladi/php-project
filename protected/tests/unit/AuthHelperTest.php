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
    
        $this->yiiAppMock->mockSession(['jwt_token' => 'valid_jwt_token']);

      
        $decodedToken = (object) ['user_id' => '12345'];

      
        m::mock('alias:Firebase\JWT\JWT')
            ->shouldReceive('decode')
            ->with('valid_jwt_token', m::type(Key::class))
            ->andReturn($decodedToken);

        $_COOKIE['jwt_token'] = 'valid_jwt_token';

       
        $result = AuthHelper::jwtHelper();

        
        $this->assertTrue($result);
        $this->assertEquals('12345', Yii::app()->session['user_id']);
    }


    public function testJwtHelperInvalidToken()
    {
        $this->yiiAppMock->mockSession(['jwt_token' => 'invalid_token']);
    
        $this->assertFalse(AuthHelper::jwtHelper());
    }
    


    public function testJwtHelperWithoutToken()
    {
       
        Yii::app()->session['jwt_token'] = null;

        $this->assertFalse(AuthHelper::jwtHelper());
    }


    /**
     * @dataProvider dataSignUp
     */
    public function testSignUp($postData, $expectedResult, $mockValidate, $mockSave=true)
    {
        
        if (!empty($postData['email'])) {
            $this->yiiAppMock->mockSession(['email' => $postData['email']]);
        } else {
            $this->yiiAppMock->mockSession(['email' => null]);
        }

       
        $userMock = $this->mongoMock->mock(User::class);
        $userMock->shouldReceive('validate')->andReturn($mockValidate);
        $userMock->shouldReceive('save')->andReturn($mockSave);

        
        $result = AuthHelper::signUp($postData);

        if ($expectedResult) {
            $this->assertTrue($result);
            $this->assertEquals($postData['email'], $userMock->email);
            $this->assertTrue(password_verify($postData['password'], $userMock->password));
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataSignUp()
    {
        $postData1 = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $postData2 = [];

        return [
           
            // [$postData1, true, true, true],
            
            [$postData1, false, false],
            
            [$postData2, false, false, false]
        ];
    }


    /**
     * @dataProvider dataLogin
     */
    public function testLogin($postData, $isEmptyData = false)
    {

        if (!$isEmptyData) {
            $hashedPassword = password_hash($postData['password'], PASSWORD_BCRYPT);
            $this->mongoMock->mockFind(User::class, (object)['email' => $postData['email'], 'password' => $hashedPassword]);
        }


        $this->yiiAppMock->mockSession([]);


        $result = AuthHelper::login($postData);


        if (!$isEmptyData) {
            $this->assertTrue($result);
            $this->assertNotNull(Yii::app()->session['jwt_token']);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataLogin()
    {
        $data1 = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $data2 = [];

        return [
            [$data1, false],
            [$data2, true]
        ];
    }



    /**
     * @dataProvider mailDataProvider
     */
    public function testMail($postData, $sendResult, $expectedResult)
    {
        $_ENV['SENDERMAIL'] = 'sender@example.com';
        $_ENV['APP_KEY'] = 'your_app_key';
        $_ENV['SENDERNAME'] = 'Sender Name';

        $this->yiiAppMock->mockSession([]);

     
        $mailMock = Mockery::mock('overload:PHPMailer');
        $mailMock->shouldReceive('isSMTP')->andReturn(true);
        $mailMock->shouldReceive('Host')->andReturn('smtp.gmail.com');
        $mailMock->shouldReceive('Port')->andReturn(587);
        $mailMock->shouldReceive('SMTPAuth')->andReturn(true);
        $mailMock->shouldReceive('Username')->andReturn($_ENV['SENDERMAIL']);
        $mailMock->shouldReceive('Password')->andReturn($_ENV['APP_KEY']);
        $mailMock->shouldReceive('setFrom')->andReturn(true);
        $mailMock->shouldReceive('addReplyTo')->andReturn(true);
        $mailMock->shouldReceive('addAddress')->andReturn(true);
        $mailMock->shouldReceive('isHTML')->andReturn(true);
        $mailMock->shouldReceive('Subject')->andReturn(true);
        $mailMock->shouldReceive('Body')->andReturn(true);
        $mailMock->shouldReceive('send')->andReturn($sendResult);

        // Call the mail method
        $result = AuthHelper::mail($postData);
        $this->assertEquals($expectedResult, $result);

        if ($sendResult) {
            $this->assertEquals($postData['email'], Yii::app()->session['email']);
            $this->assertNotNull(Yii::app()->session['otp']);
        }
    }

    public function mailDataProvider()
    {
        return [
            // [['email' => 'test@example.com'], true, true],  
            [['email' => 'test@example.com'], false, false]
        ];
    }


    /**
     * @dataProvider dataVerifyMail
     */
    public function testVerifyMail($data, $exptected, $user)
    {
        $this->mongoMock->mockFind('User', $user, null);
        $result = AuthHelper::verifyMail($data);
        $this->assertEquals($exptected, $result);
    }

    public function dataVerifyMail()
    {
        $data = [
            "email" => '1234',
        ];
        $user1 =  ['email' => "1234567890"];
        $user2 = null;
        return [
            [$data, true, $user1],
            [$data, false, $user2],

        ];
    }


    /**
     * @dataProvider dataResetPw
     */

    public function testResetPw($data, $exptected, $user)
    {


        $this->yiiAppMock->mockSession(['email' => $user['email']]);
        $this->mongoMock->mockFind('User', $user, null);
        $result = AuthHelper::resetPw($data);
        print_r($result);
        $this->assertEquals($exptected, $result);
    }

    public function dataResetPw()
    {

        $data1 = [
            "password" => "123",
            "confirm_password" => "123"
        ];

        $data2 = [
            "password" => "123",
            "confirm_password" => "12345"
        ];

        $user = ['email' => "test@gmail.com"];


        return [
            [$data1, true, $user],
            [$data2, false, $user]
        ];
    }

}
