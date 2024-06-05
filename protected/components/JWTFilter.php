<?php 
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
    class JWTFilter extends CFilter{
    protected function preFilter($filterChain)
    { 
        $valid = AuthHelper::jwtHelper();
        if($valid){
            return true;
        }
        else{
            Yii::app()->controller->redirect(array('/auth/login'));
            return false;
        }
       
    }
       
    }
?>