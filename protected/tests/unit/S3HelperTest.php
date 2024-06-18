<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Aws\S3\S3Client;
use Aws\Result;
use Aws\Exception\AwsException;
use Aws\CommandInterface;

class S3HelperTest extends MockeryTestCase
{
    protected $s3ClientMock;
    protected $s3Helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s3ClientMock = Mockery::mock(S3Client::class);
        $this->s3Helper = Mockery::mock(S3Helper::class, [$this->s3ClientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function mockS3Client()
    {
        return $this->s3ClientMock;
    }

    public function testReadFileContent()
    {
        $fileTmpName = tempnam(sys_get_temp_dir(), 'Tux');
        $expectedContent = 'This is a test file content';
        file_put_contents($fileTmpName, $expectedContent);

        $actualContent = $this->s3Helper->readFileContent($fileTmpName);

        $this->assertEquals($expectedContent, $actualContent);

        unlink($fileTmpName);
    }

    public function testGetS3ObjectSuccess()
    {
        $bucketName = 'test-bucket';
        $objectKey = 'test-object';
        $expectedResult = [
            'ContentType' => 'image/jpeg',
            'ContentLength' => 1234,
            'Body' => 'Mocked S3 Object Body',
        ];

        $this->mockS3GetObject($this->s3ClientMock, $bucketName, $objectKey, $expectedResult);

        $s3Helper = new S3Helper($this->s3ClientMock);

        $result = $s3Helper->getS3Object($bucketName, $objectKey);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expectedResult['ContentType'], $result['ContentType']);
        $this->assertEquals($expectedResult['ContentLength'], $result['ContentLength']);
    }

    protected function mockS3GetObject($s3ClientMock, $bucketName, $objectKey, $expectedResult)
    {
        $s3ClientMock->shouldReceive('getObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ])
            ->andReturn(new Result($expectedResult));
    }

    public function testGetS3ObjectFailure()
    {
        $bucketName = 'test-bucket';
        $objectKey = 'test-object';

        $exceptionMessage = 'Error getting object';
        $mockCommand = Mockery::mock(CommandInterface::class);
        $this->s3ClientMock
            ->shouldReceive('getObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ])
            ->andThrow(new AwsException($exceptionMessage, $mockCommand));

        $s3Helper = new S3Helper($this->s3ClientMock);

        $result = $s3Helper->getS3Object($bucketName, $objectKey);

        $this->assertNull($result);
    }

    public function testDeleteS3ObjectFailure()
    {
        $bucketName = 'test-bucket';
        $objectKey = 'test-object';

        $exceptionMessage = 'Error deleteing object';
        $mockCommand = Mockery::mock(CommandInterface::class);
        $this->s3ClientMock
            ->shouldReceive('deleteObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ])
            ->andThrow(new AwsException($exceptionMessage, $mockCommand));

        $s3Helper = new S3Helper($this->s3ClientMock);

        $result = $s3Helper->deleteS3Object($bucketName, $objectKey);

        $this->assertEquals(0,$result);
    }

    public function testDeleteS3ObjectSuccess()
    {
        $s3ClientMock = $this->mockS3Client();

        $bucketName = 'test-bucket';
        $objectKey = 'test-object';
        $expectedResult = [
            'DeleteMarker' => true,
        ];

        $this->mockS3DeleteObject($s3ClientMock, $bucketName, $objectKey, $expectedResult);

        $s3Helper = new S3Helper($s3ClientMock);

        $result = $s3Helper->deleteS3Object($bucketName, $objectKey);

        $this->assertEquals(1, $result);
    }


    protected function mockS3DeleteObject($s3ClientMock, $bucketName, $objectKey, $expectedResult)
    {
        $s3ClientMock->shouldReceive('deleteObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
            ])
            ->andReturn(new Result($expectedResult));
    }

    

    public function testUploadFileToS3Success()
    {
        $bucketName = 'test-bucket';
        $fileName = 'test-file.jpg';
        $fileTmpName = '/path/to/temp/file';
        $contentType = 'image/jpeg';
        $fileContent = 'file content';

        $this->s3Helper->shouldReceive('readFileContent')
            ->with($fileTmpName)
            ->andReturn($fileContent);

        $expectedResult = new Result(['ObjectURL' => 'https://test-bucket.s3.amazonaws.com/test-file.jpg']);

        $this->s3ClientMock
            ->shouldReceive('putObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $fileName,
                'Body' => $fileContent,
                'ContentType' => $contentType,
            ])
            ->andReturn($expectedResult);

        $result = $this->s3Helper->uploadFileToS3($bucketName, $fileName, $fileTmpName, $contentType);

        $this->assertEquals(1, $result); // Ensure uploadFileToS3 returns 1 on success
    }

    public function testUploadFileToS3Failure()
    {
        $bucketName = 'test-bucket';
        $fileName = 'test-file.jpg';
        $fileTmpName = '/path/to/temp/file';
        $contentType = 'image/jpeg';
        $fileContent = 'file content';

        $this->s3Helper->shouldReceive('readFileContent')
            ->with($fileTmpName)
            ->andReturn($fileContent);

        $exceptionMessage = 'Error uploading file';
        $mockCommand = Mockery::mock(CommandInterface::class);
        $this->s3ClientMock
            ->shouldReceive('putObject')
            ->with([
                'Bucket' => $bucketName,
                'Key' => $fileName,
                'Body' => $fileContent,
                'ContentType' => $contentType,
            ])
            ->andThrow(new AwsException($exceptionMessage, $mockCommand));

        $result = $this->s3Helper->uploadFileToS3($bucketName, $fileName, $fileTmpName, $contentType);

        $this->assertEquals(0, $result); // Ensure uploadFileToS3 returns 0 on failure
    }
}

?>
