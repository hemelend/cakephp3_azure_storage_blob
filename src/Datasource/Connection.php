<?php
namespace CakeAzureStorageBlob\Datasource;

require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use Cake\Datasource\ConnectionInterface;

/**
 * Class Azure Storage Blob Connection
 *
 * @package CakeAzureStorageBlob\Database
 */
class Connection implements ConnectionInterface
{
    /** @var array Connection configure parameter */
    protected $_config = [];

    /** @var blobClient|null */
    protected $_blobClient = null;

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['AccountName']) || empty($config['AccountKey']) ||
            empty($config['ContainerName'])
        ) {
            throw new \InvalidArgumentException('Config "AccountName" or "AccountKey" or "ContainerName" missing.');
        }

        $this->_config = $config;

        $connectionString = "DefaultEndpointsProtocol=https;AccountName={$this->_config['AccountName']};AccountKey={$this->_config['AccountKey']}";
        
        // Create blob client.
        $this->_blobClient = BlobRestProxy::createBlobService($connectionString);;
    }

    /**
     * Get configure name.
     *
     * @return mixed|string
     */
    public function configName()
    {
        if (empty($this->_config['name'])) {
            return '';
        }

        return $this->_config['name'];
    }

    /**
     * Get configure
     *
     * @return array
     */
    public function config()
    {
        return $this->_config;
    }
    
    /**
     * This method is not supported.
     *
     * @param callable $transaction
     */
    public function transactional(callable $transaction)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function disableConstraints(callable $operation)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function logQueries($enable = null)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function logger($instance = null)
    {
    }

    /**
     * This method is not supported.
     *
     * @return \Cake\Database\Log\QueryLogger logger instance
     */
    public function getLogger()
    {
        return new \Cake\Database\Log\QueryLogger();
    }

    /**
     * This method is not supported.
     *
     * @param \Cake\Database\Log\QueryLogger $logger Logger object
     * @return $this
     */
    public function setLogger($logger)
    {
        return $this;
    }

    /**
     * Pre processing to convert the key.
     * ex) '/key' => 'key'
     *
     * @param $key
     *
     * @return string
     */
    private function __keyPreProcess($key)
    {
        if (strpos($key, '/') === 0) {
            $key = substr($key, 1);
        }

        return $key;
    }

    /**
     * Call createContainer API
     *
     * @see BlobRestProxy::createBlockBlob
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     * 
     * @param string                        $container The container name.
     * @param Models\CreateContainerOptions $options   The optional parameters.
     *
     * @return void
     *
     */
    public function createContainer($containerName)
    {
        $containerName  = $this->__keyPreProcess($containerName);
        // Create container options object.
        $createContainerOptions = new CreateContainerOptions();

        // Set public access policy. Possible values are
        // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
        // CONTAINER_AND_BLOBS:
        // Specifies full public read access for container and blob data.
        // proxys can enumerate blobs within the container via anonymous
        // request, but cannot enumerate containers within the storage account.
        //
        // BLOBS_ONLY:
        // Specifies public read access for blobs. Blob data within this
        // container can be read via anonymous request, but container data is not
        // available. proxys cannot enumerate blobs within the container via
        // anonymous request.
        // If this value is not specified in the request, container data is
        // private to the account owner.
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

        // Set container metadata.
        $createContainerOptions->addMetaData("language", "php");
        $createContainerOptions->addMetaData("framework", "cakephp 3");

        try {
            // Create container.
            $this->_blobClient->createContainer($containerName, $createContainerOptions);
        } 
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        catch(InvalidArgumentTypeException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }

    /**
     * Call CreateBlockBlob API
     *
     * @see BlobRestProxy::createBlockBlob
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     * 
     * @param string                          $container The name of the container.
     * @param string                          $blob      The name of the blob.
     * @param string|resource|StreamInterface $content   The content of the blob.
     * @param Models\CreateBlockBlobOptions   $options   The optional parameters.
     *
     * @return Models\PutBlobResult
     */
    public function createBlockBlob($containerName, $fileToUpload, $content)
    {
        $containerName  = $this->__keyPreProcess($containerName);
        $fileToUpload = $this->__keyPreProcess($fileToUpload);

        try {
            //Upload blob
            $this->_blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        catch(InvalidArgumentTypeException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }

    /**
     * Call deleteBlob API.
     *
     * @see BlobRestProxy::deleteBlob
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     *
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param Models\DeleteBlobOptions $options   optional parameters
     *
     * @return void
     */
    public function deleteBlob($container, $blob, array $options = [])
    {
        try {
            $container = $this->__keyPreProcess($container);
            $blob = $this->__keyPreProcess($blob);

            //Delete blob
            $this->_blobClient->deleteBlob($containerName, $blob, $options);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
}
