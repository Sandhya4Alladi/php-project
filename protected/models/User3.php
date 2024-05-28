    <?php

    class User3 extends EMongoDocument{

        public $firstName;
        public $lastName;
        public $email;
        public $password;
        public $department;
        
        public function getCollectionName() {
            return 'users';
        }

        public function rules()
        {
            return array(
                array('firstName, lastName, email, department, password', 'required'),
                // array('email', 'validateEmail'),
                // array('password', 'validatePassord'),
            );
        }
        // public function validateEmail($attribute, $params){
        //     $email = $this->$attribute;
        //     if (!AuthHelper::emailPattern($email)) {
        //         $this->addError($attribute, 'Invalid email address.');
        //     }
           
        // }

        // public function validatePassord($attribute, $params){
        //     $password = $this->$attribute;

        //     $errors = AuthHelper::passwordPattern($password);
        //     if(!empty($errors)){
        //         foreach ($errors as $error) {
        //             $this->addError($attribute, $error);
        //         }
        //     }
        // }
        public function beforeSave() {
            if (parent::beforeSave()) {
                // Hash the password before saving
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