<?php 

    class VideoController extends Controller{

        public $layout = 'sample';

        public function actionIndex(){
            echo 'sdg';
            $this->render('home');
        }
    }

?>