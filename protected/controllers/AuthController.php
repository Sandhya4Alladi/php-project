<?php
use Firebase\JWT\JWT;
use PharIo\Manifest\Author;
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

                   $result = AuthHelper::signUp($_POST);
                   if($result){

                     $this->redirect(Yii::app()->createUrl('/auth/login'));
                   
                    }
                             
                    else {
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

               $result = AuthHelper::login($_POST);
               if($result){
                $this->redirect(Yii::app()->createUrl('video/home'));
                
                // VideoHelper::homeHelper();
               }

               else{
                    $this->render('login', array('error' => 'Invalid Credentials'));
               }
              
            } else {
                $this->render('login');
            }
        }
        
 
 
        public function actionMail(){
    
            if(Yii::app()->request->isPostRequest) {

                $data = file_get_contents('php://input');
                $data = json_decode($data, true);

                $result = AuthHelper::mail($data);

                if($result){
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

                if($otp===$otp_data){
                    
                   return true;
                }
                else{
            
                  return false;
                }
            }
          
            else{
                $this->render('otp');
            }
            Yii::app()->end();
        }
 
        public function actionVerifymail(){
            if(Yii::app()->request->isPostRequest){
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                $result = AuthHelper::verifyMail($data);

                if($result){
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
        
                $result = AuthHelper::resetPw($data);

                    if($result!==null){
                        if($result->save())
                        echo json_encode(array('status' => 'success', 'message' => 'password reset successfully'));
                    }
                    else{
                        echo json_encode(array('status' => 'error', 'message' => 'failed to reset'));
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
                Yii::app()->end();
            }catch(Exception $e){
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                echo CJSON::encode(array('status' => 500, 'error' => 'Logout failed'));
                Yii::app()->end();
            }
        }
 
 
    }
 
?>