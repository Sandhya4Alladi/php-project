<?php
 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
 
    class AuthHelper{
 
        public static function jwtHelper(){
            $token = Yii::app()->session['jwt_token'];
            if($token){
            $secretKey = $_ENV['JWT_SECRET_KEY'];
            try{
                $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
                if($decoded && isset($_COOKIE['jwt_token'])){
                    $user_id = $decoded->user_id;
                    Yii::app()->session['user_id'] = $user_id;
                    return true;
                }
               
            }
                catch(Exception $e){
                    header('HTTP/1.0 403 Forbidden');
                    echo 'Invalid Token';
                    return false;
                }
            }else{
                return false;
            }
        }

       public static function signUp($postData){

            $model = new User();
           if(!empty($postData)){

            $model->attributes = $_POST;
            $model->email = Yii::app()->session['email'];
            $model->password = password_hash($model->password, PASSWORD_BCRYPT);

            if ($model->validate()) {
                if ($model->save()) {
                    return true;
                } 
            }
            else{
                return false;
            }
           }
           return false;

       }

       public static function login($postData){

        if(!empty($postData)){

            $email = $postData['email'];
            $password = $postData['password'];
            $user = User::model()->findByAttributes(array('email' => $email));

                if ($user) {
                    if (password_verify($password, $user->password)) {
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
                           return true;
                        }
                }

            }

        }
        return false;
        
    }

        public static function mail(){

                $otp = self::generateOTP();
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
                    return true;
                }  

                return false;

        }

        public static function verifyMail(){

            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            $email = $data['email'];
            $user = User::model()->findByAttributes(array('email'=>$email));
            if(!empty($user)){
                return true;
            }
            return false;

        }

        public static function verifyOtp(){

            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            list($d1, $d2, $d3, $d4, $d5, $d6) = array_values($data);
            $otp = Yii::app()->session['otp'];
            $otp_data = $d1*100000 + $d2*10000 + $d3*1000 + $d4*100 + $d5*10 + $d6*1;
            echo $otp;
            echo $otp_data;

            if($otp===$otp_data){
                return true;
            }
            return false;

        }

        public static function resetPw(){

            $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                $password = $data['password'];
                $confirm_pw = $data['confirm_password'];
                if($password===$confirm_pw){
                
                    $hashed_pw = password_hash($password, PASSWORD_BCRYPT);
 
                    $email = Yii::app()->session['email'];
                    
                    $user = User::model()->findByAttributes(array('email'=>$email));
 
                    $user->password = $hashed_pw;

                    if($user->save){
                        return true;
                    }
                }

                    return false;

        
    }

        public static function generateOTP(){
            return mt_rand(100000, 999999);
         }
    }
?>






