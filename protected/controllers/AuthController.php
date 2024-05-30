<?php
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
    class AuthController extends Controller{

        public $layout = 'sample';

        // public function filters()
        // {
        //     return array(
        //         'accessControl', // you might have other filters here, add your filter after them
        //         array(
        //             'application.helpers.sampleFilter' - 'login', // assuming your filter is in the 'filters' directory
        //             // 'except' => array('login'), // actions exempt from the filter, if any
        //         ),
        //     );
        // }

        public function actionIndex(){
            echo "User Authentication page";
            $this->render('forgotpw');
        }

        public function actionSignup(){

            $model = new User();
            try {

                    if (isset($_POST['signup'])) {
                        $model->attributes = $_POST;
                        if($model->validate()){
                            $model->save();
                            $this->render('login');
                            Yii::app()->end();
                        }
                         Yii::app()->end();
                    }
                    else {
                        $this->render('signup', array(
                            'model' => $model,
                        ));
                    }

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        }

        public function actionLogin(){
            if(isset($_POST['login'])){
                $email = $_POST['email'];
                $password = $_POST['password'];
                $hashed = sha1($password);
                $user = User::model()->findByAttributes(array('email'=>$email, 'password'=>$hashed));
                // $username = $user['username'];
                if($user){
                    // $this->actionMail($email, $username);
                    $expiryTime = time() + (1 * 60 * 60); 
                    $header = ['typ'=>'JWT', 'alg'=>'HS256'];
                    $payload = array(
                        "user_id" => $user->_id, 
                        "email" => $user->email
                    );
                    $secretKey = $_ENV['JWT_SECRET_KEY'];
                    $token = JWT::encode($payload, $secretKey, 'HS256', null, $header, $expiryTime);
                    echo CJSON::encode(['token' => $token]);
                }
                Yii::app()->end();
            }
            else{
                $this->render('login');
                Yii::app()->end();
            }
        }

        

        

        // public static function actionMail($email, $username){
        //     // $email = 'sandhyaalladi2@gmail.com';
        //     // $username = "sandhya";
        //     $mail = new PHPMailer;
        //     $mail->isSMTP();
        //     $mail->Host = 'smtp.gmail.com';
        //     $mail->Port = 587;
        //     $mail->SMTPAuth = true;
        //     $mail->Username = $_ENV['SENDERMAIL'];
        //     $mail->Password = $_ENV['APP_KEY'];
        //     $mail->setFrom($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
        //     $mail->addReplyTo($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
        //     $mail->addAddress($email, $username);
        //     $mail->isHTML(true);
        //     $mail->Subject = 'Hello ' .$username;
        //     $mail->Body = 'Welcome to Dbox FTE';
        //     return $mail->send();

        // }

    }

?>