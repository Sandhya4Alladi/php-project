<?php
 
    class User extends EMongoDocument{
         public $username;
         public $email;
         public $password;
         public $likedVideos = array();
         public $dislikedVideos = array();
         public $watchLater = array();
         public $createdAt;
         public $updatedAt;
 
         public function getCollectionName()
         {
            return "users";
         }
 
         public function rules()
         {
            return array(
                array('username, email, password', 'required')
            );
         }
 
         public function beforeSave() {
            if (parent::beforeSave()) {
                $time = new MongoDate();
        
                if ($this->getIsNewRecord()) {
                    $this->createdAt = $time;
                }
        
                // Set updatedAt for every save
                $this->updatedAt = $time;
        
                // Hash the password
                // if ($this->getIsNewRecord() || !empty($this->password)) {
                //     $this->password = password_hash($this->password, PASSWORD_BCRYPT);
                // }        
                return true;
            }
            return false;
        }
        
        
        public static function model($className = __CLASS__) {
            return parent::model($className);
        }
         
    }
 
?>