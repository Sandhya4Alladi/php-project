<?php
    class AuthHelper{

        public static function emailPattern($attribute){
            if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $attribute)) {
                return false;
            }
                return true;
        }

        public static function passwordPattern($password){
            $errors = array();

            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter.';
            }
    
            if (!preg_match('/\d/', $password)) {
                $errors[] = 'Password must contain at least one numeric digit.';
            }
    
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $errors[] = 'Password must contain at least one special character.';
            }
    
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            }
    
            return $errors;
        }
         
    }
?>