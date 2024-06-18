<?php
 
class User extends EMongoDocument
{
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
 
    protected function updateTimestamps()
    {
        $time = new MongoDate();
 
        if ($this->getIsNewRecord()) {
            $this->createdAt = $time;
        }
 
        $this->updatedAt = $time;
    }
 
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->updateTimestamps();
            return true;
        }
        return false;
    }
 
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
 
?>