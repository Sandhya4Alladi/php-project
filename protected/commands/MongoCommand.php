<?php
 
// require 'vendor/autoload.php';  // Assuming Composer autoloader
 
// Replace these with your connection details
// use MongoDB\Client;
class MongoCommand extends CConsoleCommand
{
    
    public function run($args)
    {
       $model = new Register();

    //    $model->username = "sandhya";
    //    $model->email = "sandhyaalladi25@gmail.com";
         $model->username = $args[0];
         $model->email = $args[1];
       if($model->save()){
        echo "Details uploaded successfully!";
       }
       else{
        echo "error uploading details";
       }
    }

    // public function run($args){
    //     $models = Register::model()->findAll();
    //     foreach ($models as $model){
    //         var_dump($model);
    //     }
    // }

}