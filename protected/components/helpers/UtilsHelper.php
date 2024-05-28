<?php
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use PHPMailer\PHPMailer\PHPMailer;

class UtilsHelper
{

  
  public static function calledRecursively($attribute)
  {
    foreach ($attribute as $value) {
      if (empty($value)) {
        return false;
      }
    }
    return true;
  }
  public static function passwordCheck($attribute, $params)
  {
    $minLength = $params['minLength'];
        if (strlen($attribute) < $minLength) {
      return false;
      } else {
      return true;
     }
  }

  public static function getAWS(){    
    $s3Client = new S3Client([
      'version' => 'latest',
      'region'  => 'us-east-1',
      'credentials' => [
          'key'    => $_ENV['SECRET_KEY'],
          'secret' => $_ENV['SECRET_ACCESS_KEY'],
      ],
      ]);

          // Use S3 client to perform operations
          // For example, list objects in a bucket
          $objects = $s3Client->listObjects([
              'Bucket' => 'phptraining1',
          ]);


              $imageUrls = [];
              foreach($objects['Contents'] as $object){
                  $objectKey = $object['Key'];

                  $imageObject = $s3Client->getObject([
                      'Bucket'=> 'phptraining1',
                      'Key'=>$objectKey
                  ]);
                  $imageUrl = 'data:'.$imageObject['ContentType'].';base64,'. base64_encode($imageObject['Body']);
                  $imageUrls[] = $imageUrl;
              }
          return $imageUrls;
     
  }

  public static function putAWS(){

          $bucketName = 'phptraining1';
        
          // Instantiate the S3 client
          $s3 = new S3Client([
              'version' => 'latest',
              'region'  => 'us-east-1',
              'credentials' => [
                  'key'    => $_ENV['SECRET_KEY'],
                  'secret' => $_ENV['SECRET_ACCESS_KEY'],
              ],
          ]);

          // Check if the file is uploaded
          if (isset($_FILES['file'])) {
              // Get the uploaded file
              $file = $_FILES['file']['tmp_name'];

              // Upload the file to S3
              try {
                  $s3->putObject([
                      'Bucket' => $bucketName,
                      'Key'    => $_FILES['file']['name'],
                      'Body'   => fopen($file, 'rb'),
                      // Set ACL to public-read for public access
                    //    'ACL'    => 'public-read'
                  ]);
                  $imageObject = $s3->getObject([
                    'Bucket'=> 'phptraining1',
                    'Key'=>$_FILES['file']['name']
                ]);
                  // Generate the URL of the uploaded image
                //  $imageObject = $s3->getObject($bucketName, $_FILES['file']['name']);
                $imageUrl = 'data:'.$imageObject['ContentType'].';base64,'. base64_encode($imageObject['Body']);
                // Assuming $imageObject contains the image data fetched from S3
                    // $this->render('add', ['imageUrls'=>$imageUrl]);
                    // echo $imageUrl;
                    // exit;
                echo '<img src="' . $imageUrl . '" alt="Uploaded Image">';
                    return;
                  echo "uploaded successfully";
                  exit;
                  // Display a success message
                  Yii::app()->user->setFlash('success', 'File uploaded successfully to AWS S3.');
              } catch (S3Exception $e) {
                  // Display an error message
                  echo "$e";
                  exit;
                  Yii::app()->user->setFlash('error', 'Failed to upload file to AWS S3: ' . $e->getMessage());
              }
          }

  }

  public static function curlExec()
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://demo.darwinboxlocal.com/index.php/sample/add?queryType=findAll',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,te;q=0.7',
        'Connection: keep-alive',
        'Cookie: PHPSESSID=gq42quh7an5t9upp6864kuhhqf',
        'Referer: http://demo.darwinboxlocal.com/sample/add',
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
        'X-Requested-With: XMLHttpRequest'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
  }

  public static function curlPOST(){
  $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://demo.darwinboxlocal.com/index.php/sample/register',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'Register%5Busername%5D=kjkjbjb&Register%5Bpassword%5D=vvbkjb%2Cm&Register%5Bemail%5D=&UserAddress%5Bapartment%5D=&UserAddress%5Blandmark%5D%5B%5D=bkblkv&Register%5Bgender%5D=&Register%5BrememberMe%5D=0',
      CURLOPT_HTTPHEADER => array(
        'Accept: */*',
        'Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,te;q=0.7',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Cookie: PHPSESSID=gq42quh7an5t9upp6864kuhhqf',
        'Origin: http://demo.darwinboxlocal.com',
        'Referer: http://demo.darwinboxlocal.com/sample/register',
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
        'X-Requested-With: XMLHttpRequest'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
      
  } 


  //  public static function curlGET(){
            

  //         $curl = curl_init();

  //         curl_setopt_array($curl, array(
  //           CURLOPT_URL => 'http://demo.darwinboxlocal.com/sample/get',
  //           CURLOPT_RETURNTRANSFER => true,
  //           CURLOPT_ENCODING => '',
  //           CURLOPT_MAXREDIRS => 10,
  //           CURLOPT_TIMEOUT => 0,
  //           CURLOPT_FOLLOWLOCATION => true,
  //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //           CURLOPT_CUSTOMREQUEST => 'GET',
  //           CURLOPT_HTTPHEADER => array(
  //             'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
  //             'Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,te;q=0.7',
  //             'Connection: keep-alive',
  //             'Cookie: PHPSESSID=pgv7aua1b6h2n7dithkqvct5k0',
  //             'Upgrade-Insecure-Requests: 1',
  //             'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
  //           ),
  //         ));

  //         $response = curl_exec($curl);

  //         curl_close($curl);
  //         echo $response;

  // }


  public static function findHelper(){
    $model = Register::model()->find();
    return ($model);
  } 

  public static function findAllHelper(){
    $models = Register::model()->findAll();
    $dataArray = array();
    foreach ($models as $model) {
        $dataArray[] = $model->attributes;
    }

    return json_encode($dataArray);
  }

  public static function registerHelp(){
            $model = new Register();
            $model->attributes = $_POST['Register'];
            $arr = [];

            foreach($_POST['Register']['address'] as $address) {
                $temp = new UserAddress();
                // var_dump($address);
                // $temp->attributes = $address;  
                // $temp->apartment = $address['apartment']; // Set apartment attribute
                // $temp->landmark = $address['landmark']; 
                 $temp->attributes=$address;
                // var_dump($temp->attributes);
              // echo $address;
                $arr[] = $temp;
            }
            // Yii::app()->end(); 

            
            
            $model->address = $arr;
            $model->number->attributes = $_POST['UserNumber'];
        //     print_r($model);
        //  exit;
            if ($model->validate()) {
                $responseData = [
                    'status' => 1,
                    'data'=>$_POST
                ];
                $model->save();
            } else {
                $errors = $model->getErrors();
                $responseData = [
                    'status' => 0,
                    'error' => $errors
                ];
            }
          return $responseData; 
      
  }

  public static function mailHelper($email, $username){
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['SENDERMAIL'];
                    $mail->Password = $_ENV['APP_KEY'];
                    $mail->setFrom($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
                    $mail->addReplyTo($_ENV['SENDERMAIL'], $_ENV['SENDERNAME']);
                    $mail->addAddress($email, $username);
                    $mail->isHTML(true);
                    $mail->Subject = 'Hello ' .$username;
                    $mail->Body = 'Welcome to Dbox FTE';
                    return $mail->send();

  }
}
