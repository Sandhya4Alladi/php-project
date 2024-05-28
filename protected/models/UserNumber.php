<?php

class UserNumber extends EMongoEmbeddedDocument{
    public $number = array();

    public function rules(){    
        return array(
            array('number', 'required')
        );
    }
    public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}

?>