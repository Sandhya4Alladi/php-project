<?php

class UserAddress extends EMongoEmbeddedDocument
{
    public $apartment;
    public $landmark;
    public function rules()
    {
        return array(
            array('apartment, landmark', 'required'),
        );
    }

    public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}

?>