<?php
class Video extends EMongoDocument
{
    public $userId;
    public $title;
    public $desc;
    public $imgKey;
    public $videoKey;
    public $captionsKey;
    public $tags = array();
    public $views;
    public $plays;
    public $likes;
    public $dislikes;
    public $createdAt;
    public $updatedAt;
 
    public function getCollectionName()
    {
        return 'videos';
    }
 
    public function rules()
    {
        return array(
            array('userId, title, desc, imgKey, videoKey, tags', 'required'),
            array('views, plays, likes, dislikes', 'numerical', 'integerOnly' => true)
        );
    }
 
    public function init()
    {
        parent::init();
        // Set default values
        $this->views = 0;
        $this->plays = 0;
        $this->likes = 0;
        $this->dislikes = 0;
    }
 
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $time = new MongoDate();
 
            if ($this->getIsNewRecord()) {
                $this->createdAt = $time;
            }
 
            // Set updatedAt for every save
            $this->updatedAt = $time;
 
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