
<?php

use Aws\Credentials\Credentials;
use Aws\Ivs\IvsClient;

class IvsBroadcastClient extends CApplicationComponent
{
    // AWS credentials
    public $accessKey;
    public $secretKey;

    // AWS region
    public $region = "ap-south-1";

    // IVS client instance
    private $_client;

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();

        // Initialize IVS client
        
        $this->_client = new IvsClient([
            'credentials' => new Credentials($this->accessKey, $this->secretKey),
            'region' => $this->region,
            'version' => 'latest',
        ]);
    }

    /**
     * Get IVS client instance.
     * @return IvsClient
     */
    public function getClient()
    {
        return $this->_client;
    }
}
