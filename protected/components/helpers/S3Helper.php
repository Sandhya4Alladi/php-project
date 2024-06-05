<?php
 
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
 
class S3Helper
{
    public static function uploadFileToS3($bucketName, $fileName, $fileTmpName, $contentType)
    {
        $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $awsConfig['aws']['region'],
            'credentials' => $awsConfig['aws']['credentials'],
        ]);
 
        $fileContent = file_get_contents($fileTmpName);
 
        $uploadParams = array(
            'Bucket' => $bucketName,
            'Key'    => $fileName,
            'Body'   => $fileContent,
            'ContentType' => $contentType,
        );
 
        try {
            $result = $s3->putObject($uploadParams);
            if ($result) {
                return array('success' => true, 'url' => $result['ObjectURL']);
            } else {
                throw new Exception("File upload to S3 failed.");
            }
        } catch (AwsException $e) {
            Yii::log("Error uploading file to S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
 
    public static function deleteS3Object($bucketName, $objectKey)
    {
        try {
            $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');
            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $awsConfig['aws']['region'],
                'credentials' => $awsConfig['aws']['credentials'],
            ]);
 
 
            $result = $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ]);
 
            Yii::log("Object deleted successfully: " . print_r($result, true), 'info');
        } catch (Aws\S3\Exception\S3Exception $e) {
            Yii::log("Error deleting object: " . $e->getMessage(), 'error');
        }
    }
 
    public static function getS3Object($bucketName, $objectKey)
    {
        $awsConfig = require(Yii::getPathOfAlias('application.config') . '/aws-config.php');
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $awsConfig['aws']['region'],
            'credentials' => $awsConfig['aws']['credentials'],
        ]);
 
 
        $result = $s3->getObject([
            'Bucket' => $bucketName,
            'Key'    => $objectKey,
        ]);
 
        return $result;
    }
}
 
?>