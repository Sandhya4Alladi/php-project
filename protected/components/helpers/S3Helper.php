<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Helper
{
    protected $s3Client;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }
    protected function readFileContent($fileTmpName)
    {
        return file_get_contents($fileTmpName);
    }

    public function uploadFileToS3($bucketName, $fileName, $fileTmpName, $contentType)
    {
        $fileContent = $this->readFileContent($fileTmpName);
        $uploadParams = array(
            'Bucket' => $bucketName,
            'Key'    => $fileName,
            'Body'   => $fileContent,
            'ContentType' => $contentType,
        );

        try {
            $result = $this->s3Client->putObject($uploadParams);
            return 1;
        } catch (AwsException $e) {
            Yii::log("Error uploading file to S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            return 0;
        }
    }

    public function deleteS3Object($bucketName, $objectKey)
    {
        try {
            $result = $this->s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ]);

            Yii::log("Object deleted successfully: " . print_r($result, true), 'info');
            return 1;
        } catch (AwsException $e) {
            Yii::log("Error deleting object: " . $e->getMessage(), 'error');
            return 0;
        }
    }

    public function getS3Object($bucketName, $objectKey)
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $bucketName,
                'Key'    => $objectKey,
            ]);
            return $result;
        } catch (AwsException $e) {
            Yii::log("Error getting object from S3: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            return null;
        }
    }
}

?>