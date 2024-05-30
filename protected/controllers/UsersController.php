<?php

    class UsersController extends Controller{

        public $layout = 'sample';
        public function actionIndex(){
            echo 'dgj';
            $this->render('profile');
        }
    }

?>