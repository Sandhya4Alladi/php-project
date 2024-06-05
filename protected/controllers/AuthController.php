<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
 
    class AuthController extends Controller{
 
        public $layout = 'sample';
  
        public function actionIndex(){
            echo "User Authentication page";
        }
 
        public function actionSignup() {
            $model = new User();
            try {
                if (isset($_POST['signup'])) {
                    $model->attributes = $_POST;
                    $model->email = Yii::app()->session['email'];
                    $model->password = password_hash($model->password, PASSWORD_BCRYPT);
 
                    if ($model->validate()) {
                        if ($model->save()) {
                            $this->redirect(Yii::app()->createUrl('/auth/login'));
                        } else {
                            $this->render('signup', array('model' => $model, 'error' => 'Failed to save user'));
                        }
                    } else {
                        $this->render('signup', array('model' => $model, 'error' => 'Validation failed'));
                    }
                } else {
                    $this->render('signup', array('model' => $model));
                }
            } catch (Exception $e) {
                $this->render('signup', array('model' => $model, 'error' => $e->getMessage()));
            }
        }
        
 
        public function actionLogin() {
            if (isset($_POST['login'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];
               // echo password_hash("")
                $user = User::model()->findByAttributes(array('email' => $email));
                // echo "<pre>";
                // print_r($user);
                // echo "hi",password_verify("Afrin@123", '$2y$10$v2Iv16p1xfH31gLkb.LoZODRMuHtuspPlG1eMX7lTQ/.BzKK5U9mG'),"<br>";
                //echo "hi",password_verify("Afrin@123", $user->password);
                if ($user) {
                    if (password_verify($password, $user->password)) {
                        // echo "verified";
                        // Yii::app()->end();
                        $expiryTime = time() + (1 * 60 * 60 * 24);
                        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
                        $payload = array(
                            "user_id" => (string) $user->_id
                        );
                        $secretKey = $_ENV['JWT_SECRET_KEY'];
        
                        $token = JWT::encode($payload, $secretKey, 'HS256', null, $header);
                        if ($token) {
                            Yii::app()->session['jwt_token'] = $token;
                            setcookie("jwt_token", $token, $expiryTime, "/", "", false, true);
                            $this->redirect(Yii::app()->createUrl('video/home'));
                        } else {
                            $this->render('login', array('error' => 'Token generation failed'));
                        }
                    } else {
                        $this->render('login', array('error' => 'Incorrect password'));
                    }
                } else {
                    $this->render('login', array('error' => 'User not found'));
                }
            } else {
                $this->render('login');
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
                echo "hi";
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                list($d1, $d2, $d3, $d4, $d5, $d6) = array_values($data);
                $otp = Yii::app()->session['otp'];
                $otp_data = $d1*100000 + $d2*10000 + $d3*1000 + $d4*100 + $d5*10 + $d6*1;
                echo $otp;
                echo $otp_data;
                if($otp==$otp_data){
                    echo "hello";
                    Yii::app()->end();
                   return true;
                }
                else{
                    echo "bye";
                    Yii::app()->end();
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
                
                    $hashed_pw = password_hash($password, PASSWORD_BCRYPT);
 
                    $email = Yii::app()->session['email'];
                    
                    $user = User::model()->findByAttributes(array('email'=>$email));
 
                    // echo "<pre>";
                    // print_r($user);
                    // echo $user->password,"<br>";
                    // echo $hashed_pw;
                    // Yii::app()->end();
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
            try{
                Yii::app()->request->cookies->remove('jwt_token');
                Yii::app()->session->destroy();
                Yii::app()->end(CJSON::encode(array('status' => 200)));
            }catch(Exception $e){
                echo $e;
            }
        }
 
        private function generateOTP() {
            return mt_rand(100000, 999999);
        }
 
    }
 
?>