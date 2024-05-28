<?php

use Firebase\JWT\JWT;
use PhpParser\Node\Expr\Print_;
use Aws\S3\S3Client;
use Aws\IVS\IVSClient;

class Auth2Controller extends Controller
{

    public function actionIndex()
    {
        echo "Welcome to signup page!";
    }

    public function actionSignup()
    {
        $model = new User();
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        try {
            if ($data) {
                $model->attributes = $data;
                if ($model->validate()) {
                    $model->save();
                    echo CJSON::encode(['success' => 'Registered Successfully']);
                } else {
                    $errors = $model->getErrors();
                    print_r($errors);
                    Yii::app()->end();
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionLogin()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if($data){
            $email = $data['email'];
            $password = $data['password'];
            $hashed = sha1($password);
            $user = User::model()->findByAttributes(array('email'=>$email, 'password'=>$hashed));
            if($user){
                $expiryTime = time() + (1 * 60 * 60); //for 1 hr
                $header = ['typ'=>'JWT', 'alg'=>'HS256'];
                $payload = array(
                    "user_id" => $user->_id, 
                    "email" => $user->email
                );
                $secretKey = $_ENV['JWT_SECRET_KEY'];
                $token = JWT::encode($payload, $secretKey, 'HS256', null, $header, $expiryTime);
                echo CJSON::encode(['token' => $token]);
                // return true;
            }
            else{
                echo json_encode(['error' => 'Invalid credentials']);
                Yii::app()->end();
            }
        }

    }

    public function actionIVS()
    {

        $credentials = [
            'key' => $_ENV['SECRET_KEY'],
            'secret' => $_ENV['SECRET_ACCESS_KEY'],
        ];
        $region = $_ENV['AWS_REGION']; // Change to your desired AWS region

        // Create IVS client
        $client = new IvsClient([
            'credentials' => $credentials,
            'region' => $region,
            'version' => 'latest',
        ]);

        // Start a stream
        $result = $client->createChannel([
            'name' => 'Live',
            'type' => 'BASIC',
        ]);

        $channelArn = $result['channel']['arn'];

        echo $channelArn;

        $streamKey = $result['streamKey']['arn']; // Get the stream key

        // echo "Channel ARN: $channelArn\n";
        // echo "Stream Key: $streamKey\n";
        $this->actionBroadcast($result, $streamKey);
    }

    public function actionBroadcast($result, $key)
    {

        // $configuration = array(
        //     'serverEndPoint' => $result['ingestEndPoint']
        // );

        $client = Yii::app()->ivsBroadcastClient->getClient();

        $serverEndPoint = $result['ingestEndPoint'];
        $channelArn = $result['channel']['arn'];


        try {
            // Create metadata to start the stream
            $metadata = json_encode([
                'streamKey' => $key, // Provided stream key
                'ingestEndpoint' => $serverEndPoint, // Ingest endpoint obtained from $result
                'action' => 'startStream' // Indicate the action to start the stream
            ]);

            // Send metadata to IVS
            $client->putMetadata([
                'channelArn' => $channelArn, // Replace with your channel ARN
                'metadata' => $metadata // Metadata to start the stream
            ]);

            echo "Broadcast started successfully!";
        } catch (\Exception $e) {
            echo "Error starting broadcast: " . $e->getMessage();
        }

        try {
            $client->startStream([
                'channelArn' => $channelArn,
                'streamKey' => $key,
                'ingestEndpoint' => $serverEndPoint,
            ]);

            echo "Broadcast started successfully!";
        } catch (\Exception $e) {
            echo "Error starting broadcast: " . $e->getMessage();
        }
    }
}
?>