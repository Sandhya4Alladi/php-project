<?php
    class Login extends EMongoDocument{
        // public $_id;
        public $email;
        public $password;
        public $rememberMe;
        public function getCollectionName(){
            return 'users';
        }   
        public function rules(){
            return array(
                array('email, password', 'required')
            );
        }

        public static function model($className = __CLASS__){
            return parent::model($className);
        }
    
    }
     
    
 ?>