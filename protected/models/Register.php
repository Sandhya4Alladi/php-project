<?php

class Register extends EMongoDocument {
	public $username;
	public $password;
	public $email;
	public $gender;
	public $address;
    public $rememberMe;	
	public function getCollectionName() {
		return 'users';
	}

	
	public function rules() {
		return [
			['username, password, email, gender', 'required'],
            ['password','done', 'minLength'=>8],
			['address', 'validateAddress']
		];
	}

	public function validateAddress($attribute, $params) {
		foreach($this->address as $ind => $address) {
			$temp = new UserAddress();
			$temp =  $address;
			if(!$temp->validate()) {
				$this->addError('address[]', $temp->getErrors());
				// foreach ($temp->getErrors() as $attr => $errors) {
				// 	foreach ($errors as $error) {
				// 		// Add error to the correct attribute of the parent model
				// 		// $this->addError("address[$ind][$attr]", $error);
				// 		$this->addError("address[]", $error);

				// 	}
				// }
			}
		}
	}
    public function done($attribute, $params){

		if(!UtilsHelper::passwordCheck($attribute, $params)){
			$this->addError($attribute, 'password must be at least min length');
		}
		else{
		$this->password = sha1($_POST['Register']['password']);
		}
    }


	public function behaviors()
    {
        return array(
            'embeddedArrays' => array(
                'class'=>'ext.YiiMongoDbSuite.extra.EEmbeddedArraysBehavior',
                'arrayPropertyName'=>'address',       // name of property, that will be used as an array
                'arrayDocClassName'=>'UserAddress'    // class name of embedded documents in array
            ),
        );
    }
	

	public function embeddedDocuments()
    {
        return array(
            // property field name => class name to use for this embedded document
			'number' => 'UserNumber'
        );
    }

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

}
