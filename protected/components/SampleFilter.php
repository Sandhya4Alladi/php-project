<?php 
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
    class sampleFilter extends CFilter{
    protected function preFilter($filterChain)
    {
        // if(!isset(Yii::app()->session['user'])){
        //     // Yii::app()->controller->redirect(array('login/login'));
        //     // echo Yii::app()->session['user'];  
        //     // echo 'reached';  
        //     return false;
        // }
        // return true;

        $receivedJwt = '';
        // echo CJSON::encode($_SERVER);
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['HTTP_AUTHORIZATION']);
            if (strpos($authHeader, 'Bearer ') === 0) {
                $receivedJwt = substr($authHeader, 7);
            }
        }
        // echo json_encode([$receivedJwt]);
        // return true;
        try{
        $decoded = JWT::decode($receivedJwt, new Key('Sandhya123', 'HS256'));
        if($decoded){
            return true;
        }
       
    }
        catch(Exception $e){
            header('HTTP/1.0 403 Forbidden');
            echo 'Invalid Token';
            return false;
        }
    }

    // public function filterSampleFilter($filterChain)
    // {
    //     return $this->preFilter($filterChain);
    // }
       
    }
?>