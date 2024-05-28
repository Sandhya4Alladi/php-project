<?php

use PHPMailer\PHPMailer\PHPMailer;
use Firebase\JWT\JWT;

    class LoginController extends Controller{

        public function filters()
            {
                return array(
                    array(
                        'application.components.SampleFilter - login',
                    ),
                );
            }
        public function actiongetData() {
            echo json_encode(["name" => 'sandhya']);
        }


        public function actionIndex(){
            
            echo 'Welcome, Now create a login form';
            // $this->actionLogin('66459fa0dce90c25de0fe872');
        }
        public function actionLogin(){
            $model = new Login();                                                                                                                                                                                                                                                                                                                       
            // $objectId = new \MongoDB\BSON\ObjectId($id); //finding the whole document using object id
            // $model = Login::model()->findByAttributes(array('_id' => $objectId));
            
            // $data = json_encode($model->_id);
            // // Decode the JSON string to an associative array
            // $dataArray = json_decode($data, true);

            // // Access the '$oid' property
            // $objectId = $dataArray['$oid'];

            // echo $objectId;

            //  Yii::app()->end();

                  if(isset($_POST['Login'])){
                // exit($_POST['Login']['email']);
               
                    //  $model->attributes = $_POST['Login']; 
                    // $user =Login::model()->find();
                    
                     $user = Register::model()->findByAttributes(array('email' => $_POST['Login']['email']));
                    //  $user = Login::model()->findByAttributes(array('email' => $model->email));
                    
                        // echo '<pre>';
                        // echo json_encode($user);
                      
                        // Yii::app()->end();
                    
                    // echo json_encode(["user"=>$user]);

                    if ($user !== null) {
                        // exit('jhdsdgjk');
                        // echo "Welcome, " .$user->email;
                        Yii::app()->session['user']='darwinbox';
                        echo "session_created";
                        // echo 'session';
                        // exit;
                        try{
                        $this->actionMail($user->email, $user->username);
                    }catch(Exception $err){
                        echo $err;
                    }
                    echo "successfully mailed bro";
                        // print_r(Yii::app()->session);
                        // print_r(Yii::app()->session['user']);
                        // $this->render('logout');
                        // Yii::app()->end();
                    } else {
                        echo "Invalid username or password";
                        $this->redirect('login',array('model' => $model));
                    }
                    if(!isset(Yii::app()->session['user'])){
                        echo "NOT CREATED SESSION";
                        $this->render('login', array('model' => $model));
                    }
                    else{
                        // print_r("created session");;
                        echo json_encode(["success"=>true]);
                    }
                }
               
                if(isset($_POST['logout'])){
                    // echo json_encode(["data"=>"reached"]);
                    Yii::app()->session->clear();
                    Yii::app()->session->destroy();

                }
                else{
                    if(!isset(Yii::app()->session['user'])){
                    $this->render('login', array('model' => $model));
                }
                else{
                    // echo "why bro";
                    $this->render('logout');
                }
                }   
        }



           
        public function actionMail($email, $username){

            

                    // require_once(Yii::getPathOfAlias('application.vendor.phpmailer.phpmailer.src.PHPMailer') . '.php');

                    // $mail = Yii::app()->mail;

                    // // Compose email
                    // $to = 'sandhyaalladi25@gmail.com';
                    // $subject = 'Test Email';
                    // $body = 'This is a test email sent using PHPMailer in Yii 1.1.';

                    // // Set recipient, subject, and body
                    // $mail->addAddress($to);
                    // $mail->Subject = $subject;
                    // $mail->Body = $body;

                    // // Send email
                    // if (!$mail->send()) {
                    //     Yii::log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo, 'error');
                    //     // Handle error
                    // } else {
                    //     Yii::log('Message has been sent', 'info');
                    //     // Email sent successfully
                    // }
                    $result = UtilsHelper::mailHelper($email, $username);
                   
                  
                    if($result){
                        echo 'mail Sent successfully!';
                    }
                    else{
                        echo 'error sending mail.';
                    }
        }

        public function actionSample(){

            //without model from $_POST
            if(isset($_COOKIE['jwt_token'])) {
                // Token exists, render the logout page
                $this->render('logout');
                return;
            }
                if(isset($_POST['email']) && isset($_POST['password'])) {
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                  
                    
                    $user = Register::model()->findByAttributes(array('email' => $email));
            //         
                    if($user !== null){
                        $expiryTime = time() + (1 * 60); //for 1 hr
                        $header = ['typ'=>'JWT', 'alg'=>'HS256'];
                        $payload = array(
                            "user_id" => $user->_id, 
                            "email" => $user->email
                        );
                        $secretKey = 'Sandhya123';
                        // Encode payload into JWT token
                        $token = JWT::encode($payload, $secretKey, 'HS256', null, $header, $expiryTime);
                        // setcookie('jwt_token', $token, $expiryTime ,'/', '', false, true);      
                        // Return token to client
                        echo json_encode(['token' => $token]);
                        return true;

                        // if($token){
                        //     $this->render('logout');
                        // }

                        // list($header, $payload, $signature) = explode('.', $token);

                        // $decoded = JWT::jsonDecode(base64_decode($payload));

                        // echo json_encode($decoded);

                        // echo json_encode(JWT::jsonDecode(base64_decode($header)));                  
                        


                    
                     } 
                     else {
                        // Authentication failed
                        echo json_encode(['error' => 'Invalid credentials']);
                        Yii::app()->end();
                    }

                // // if ($user !== null) {
                // //     echo $user->uEmail;
                // //     Yii::app()->end();
                // //     exit('success');
                // // } else {
                // //     exit('User not found');
                // // }

                    // echo $user->username;
                    // Yii::app()->end();
                    // exit('success');
                    // $user = Login::model()->findByAttributes(array('email' => $_POST['Login']['email']));
                //    $res = UtilsHelper::mailHelper($user->email, $user->username);
                //    if($res){
                //     echo 'mail sent';
                //    }
                //    else{
                //     echo 'error';
                //    }
                    // Perform authentication (this is just a simple example, you should implement your own authentication logic)
                   
                }
                else{
                $this->render('sample');
                }
        }
            // public function actionProtected() {
            //     // Verify JWT token
            //     $token = Yii::app()->getRequest()->getQuery('token');
            //     $secretKey = 'Sandhya123';
                
            //     // Debugging: Print out the token
            //     echo "Token: $token"; // Check if $token is empty or null
                
            //     try {
            //         // Decode JWT token
            //         $decoded = JWT::decode($token, $secretKey, array('HS256'));
                    
            //         // Debugging: Print out the decoded token
            //         print_r($decoded); // Check the decoded payload
                    
            //         // Check if $decoded is an array and contains the 'user_id' key
            //         if (is_array($decoded) && isset($decoded['user_id'])) {
            //             // Token is valid, proceed with protected action
            //             echo $decoded['user_id']; // Assuming 'user_id' is present in the payload
            //         } else {
            //             // Token is invalid or doesn't contain expected data
            //             Yii::app()->getRequest()->sendError(401, 'Unauthorized');
            //         }
            //     } catch (JWTException $e) {
            //         // Token is invalid
            //         Yii::app()->getRequest()->sendError(401, 'Unauthorized');
            //     }
            // }
            
            
            
            // Simple authentication function, you should replace this with your own authentication logic
            


        }   


       
    
 ?>


