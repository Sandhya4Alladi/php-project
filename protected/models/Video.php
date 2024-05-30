<?php
    class Video extends EMongoDocument{


        public $userId;
        public $title;
        public $desc;
        public $imgKey;
        public $videoKey;
        public $tags = array();
        public $views;
        public $plays;
        public $llikes;
        public $dislike;
        public $createdAt;
        public $updatedAt;

        public function getCollectionName()
        {
            return 'videos';
        }

        public function rules()
        {
            return array(
                array('userId, title, desc, imgKey, videoKey, tags', 'required')
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
                $this->password = sha1($this->password);
        
                return true;
            }
            return false;
        }
        
        
        public static function model($className = __CLASS__) {
            return parent::model($className);
        }
    }
 ?>