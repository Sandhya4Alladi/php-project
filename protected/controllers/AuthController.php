<?php
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use function PHPUnit\Framework\isNull;

    class AuthController extends Controller{

        public $layout = 'sample';
  
        public function actionIndex(){
            echo "User Authentication page";
        }
        
        public function actionSignup(){
            $model = new User();
            try {
                   
                if(isset($_POST['signup'])){
                      
                    $model->attributes = $_POST;
                   
                    $model['email'] = Yii::app()->session['email']; //email is saved while sending mail

                        if($model->validate()){
                            $model->save();
                            $this->redirect('login');
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
                    // $hash = CPasswordHelper::hashPassword($password);
                    $hashed = sha1($password);
                    // echo $hashed;

                    // Yii::app()->end();
                    
                    $user = User::model()->findByAttributes(array('email'=>$email, 'password'=>$hashed));

                    if($user){
                        $expiryTime = time() + (1 * 60 * 60 * 24); 
                        $header = ['typ'=>'JWT', 'alg'=>'HS256'];
                        $payload = array(
                            "user_id" => $user->_id,
                            "email" => $user->email
                        );
                        $secretKey = $_ENV['JWT_SECRET_KEY'];
                        
                        $token = JWT::encode($payload, $secretKey, 'HS256', null, $header);
                        if($token){
                            Yii::app()->session['jwt_token'] = $token;
                            setcookie("jwt_token", $token, $expiryTime, "/", "", false, true);
                            $this->redirect(Yii::app()->createUrl('video/home'));
                        }
                        else{
                            $this->render('login');
                        }

                    }
                    else{
                        $this->render('login');
                    }
                
                    Yii::app()->end();
                }
                else{
                    $this->render('login');
                    Yii::app()->end();
                }
        }


        public function actionMail(){
            
            
            if(Yii::app()->request->isPostRequest) {
                $otp = $this->generateOTP();
                Yii::app()->session['otp'] = $otp;
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                $email = $data['email'];
                Yii::app()->session['email'] = $email;
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SENDERMAIL'];
                $mail->Password = $_ENV['APP_KEY'];
                $mail->setFrom($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
                $mail->addReplyTo($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Hello ' .$email;
                $mail->Body = 'Your 6 digit verification code is '. $otp;    
                if($mail->send()){
                    echo json_encode(array('status' => 'success', 'message' => 'mail sent successfully'));
                }
                else{
                    echo json_encode(array('status' => 'error', 'message' => 'error sending mail'));
                }
                Yii::app()->end();
            }
        
        }

        public function actionVerifyotp(){
            if(Yii::app()->request->isPostRequest) {
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                list($d1, $d2, $d3, $d4, $d5, $d6) = array_values($data);
                $otp = Yii::app()->session['otp'];
                $otp_data = $d1*100000 + $d2*10000 + $d3*1000 + $d4*100 + $d5*10 + $d6*1;
                echo $otp;
                echo $otp_data;
                if($otp==$otp_data){
                   return true;
                }
                else{
                  return false;
                }

            }
            // unset(Yii::app()->session['otp']);
            else{
                $this->render('otp');
            }
            Yii::app()->end();
        }

        public function actionVerifymail(){
            if(Yii::app()->request->isPostRequest){
               
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                $email = $data['email'];
                $user = User::model()->findByAttributes(array('email'=>$email));
                if($user){
                    echo json_encode(array('status' => 'error', 'message' => 'User already exists.'));
                }
                else{
                    echo json_encode(array('status' => 'success', 'message' => 'User does not exist.'));
                }
                Yii::app()->end();
            }
        }

        public function actionReset(){
            $this->render('otp');
        }

        public function actionForgot(){
            if (Yii::app()->request->getRequestType() === 'GET') {
              $this->render('forgotpw');
            }
        }

        public function actionResetpw(){
           
            if(Yii::app()->request->isPostRequest){
        
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                $password = $data['password'];
                $confirm_pw = $data['confirm_password'];
                if($password===$confirm_pw){
                
                    $hashed_pw = sha1($password);

                    $email = Yii::app()->session['email'];
                    $user = User::model()->findByAttributes(array('email'=>$email));
                    $user->password = $hashed_pw;

                    if($user->save()){
                        echo json_encode(array('status' => 'success', 'message' => 'password reset successfully'));
                    }
                    else{
                        echo json_encode(array('status' => 'error', 'message' => 'failed to reset'));
                    }
                    Yii::app()->end();
                    
                }
                Yii::app()->end();
               
            }
            else{
                $this->render('resetpw');
            }
            
        }

        public function actionLogout(){
            Yii::app()->request->cookies->remove('jwt_token');
            Yii::app()->session->destroy();
            $this->redirect(array('/auth/signup'));
            Yii::app()->end();
        }

        private function generateOTP() {
            return mt_rand(100000, 999999);
        }

    }

?>