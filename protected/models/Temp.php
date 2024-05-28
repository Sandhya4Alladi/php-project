<?php

class Temp extends EMongoDocument {
	public $username;
	public $password;
	public $name;
	public $gender;
	public $language;
	public $skills;

	public function getCollectionName() {
		return 'users';
	}

	public function primaryKey() {
		return 'username';
	}

	public function indexes() {
		return [
			'username'=>[
				'key'=>[
					'username'=>EMongoCriteria::SORT_ASC,
				],
				'unique'=>true
			]
		];
	}

	public function rules() {
		return [
			['username, password, name', 'required']
		];
	}


	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

}
