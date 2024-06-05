<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;

    class AuthHelper{


        public static function jwtHelper(){
            $token = Yii::app()->session['jwt_token'];
            $secretKey = $_ENV['JWT_SECRET_KEY'];
            try{
                $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
                if($decoded && isset($_COOKIE['jwt_token'])){
                    $user_id = $decoded->user_id->{'$oid'}; 
                    $user_email = $decoded->email;
                    // echo $user_id;
                    Yii::app()->session['user_id'] = $user_id;
                    Yii::app()->session['email'] = $user_email;
                    return true;
                }
               
            }
                catch(Exception $e){
                    header('HTTP/1.0 403 Forbidden');
                    echo 'Invalid Token';
                    return false;
                }
        }
         
    }
?>