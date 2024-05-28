<?php

class SampleController extends Controller
{
    public function actionIndex()
    {
        // echo "Sample controller";
          

        Yii::app()->cache->executeCommand("hset", ["h1", "k1", "v1"]);
        $data = Yii::app()->cache->executeCommand("hget", ["h1", "k1"]);
        echo json_encode($data);
        Yii::app()->end();
        
        // $user = new Register();
        // echo $user;
        

    }
    public function actionRegister()
    {

        $model = new Register();
        try {
                if (isset($_POST['Register'])) {
                $responseData = UtilsHelper::registerHelp();
                    // echo $responseData;
                    // Yii::app()->end();
                    // exit('check');

                header('Content-Type:application/json');
                echo json_encode($responseData);
                Yii::app()->end();
                exit;
                
                }
                else {
                    $this->render('register', array(
                        'model' => $model,
                    ));
                }

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionAdd()
    {

        if (Yii::app()->request->isAjaxRequest) {
           
            $queryType = Yii::app()->request->getParam('queryType');
            if ($queryType === 'find') {
                // $criteria = new EMongoCriteria();
                // $criteria->address->apartment('==', 'Maribor house');
                $res = UtilsHelper::findHelper();
                echo json_encode(["data" => $res]);

            } 
            elseif ($queryType === 'findAll') {
               
                $res = UtilsHelper::findAllHelper();
            // header('Content-Type:application/json');
                echo json_encode(["data" => $res]);
            } elseif ($queryType === 'findByAttribute') {
                $model = Register::model()->findByAttributes(array('username' => 'qwertyuiop'));
                // $model = Register::model()->findByAttributes(array('address.apartment'=>'Maribor house'));
                echo json_encode(["data" => $model]);
            }
            //for findByPk - $model = Register::model()->findByPk( new MongoId(give id here));

            else {
                echo json_encode(["error" => "Invalid query type"]);
            }

            Yii::app()->end();
        }
        
        $this->render("add");
    }


    public function actionAWS()
    {
       
       $imageUrls = UtilsHelper::getAWS();
        $this->render('add', ['imageUrls'=>$imageUrls]);
            //  echo $imageUrl;
    }

     public function actionUpload()
     {
            UtilsHelper::putAWS();
            $this->render('upload');
     }


     public function actionCurl(){
           UtilsHelper::curlExec();
       }

    //   public function actionPost(){
    //     UtilsHelper::curlPOST();
    //   }

    //   public function actionGet(){
    //     echo "<pre>";
    //     echo json_encode(["data"=>["name"=>"sandhya", "age"=>21, "gender"=>"F"]]);
    //   }

    //   public function actionTest(){
    //     UtilsHelper::curlGET();
    //   }
    public function actionSend(){
        $data=[
            "1"=>"one",
            "2"=>"two"
        ];
        echo json_encode($data);
    }
      public function actionNode(){
                    // URL of the Node.js API endpoint
            $url = 'https://proj-live-backend.onrender.com/myapp/admin/get-members';
            // $url = 'http://35.154.237.68:5000/getData';

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Add any additional cURL options as needed, such as headers or request type

            // Execute cURL request
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                $error_message = curl_error($ch);
                // Handle the error appropriately
                // For example:
                // throw new Exception("cURL error: $error_message");
            }

            // Close cURL session
            curl_close($ch);

            // Process the response from the Node.js API
            // For example, you can decode JSON response if applicable
            $response_data = json_decode($response, true);
            print_r($response_data);
            // Handle the response data as needed

      }

      public function actionPost(){
        $url = 'http://demo.darwinboxlocal.com/sample/register';
        // exit("qwtuo");
        $postData = array(
            'Register'=>array(
            "username"=>"Sandhya", "age"=>21));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // Convert the data array to a URL-encoded query stringcurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout to 10 seconds
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error_message = curl_error($ch);
            // Handle the error appropriately
            // For example:
            // throw new Exception("cURL error: $error_message");
        }

        // Close cURL session
        curl_close($ch);

        // Process the response from the Node.js API
        // For example, you can decode JSON response if applicable
        $response_data = json_decode($response, true);
        print_r($response_data);

      }

    }