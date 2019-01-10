<?php
namespace CakeAzureStorageBlob\Datasource;

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

        // echo $connectionString;
        // Create blob client.
        $this->_blobClient = BlobRestProxy::createBlobService($connectionString);
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

    public function newContainer($containerName)
    {
        echo $this->_blobClient;
        $cntrName  = $this->__keyPreProcess($containerName);
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
            $this->_blobClient->createContainer($cntrName, $createContainerOptions);
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

    // /**
    //  * Call CopyObject API.
    //  *
    //  * @see S3Client::copyObject
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#copyobject
    //  *
    //  * @param string $srcKey
    //  * @param string $destKey
    //  * @param array  $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function copyObject($srcKey, $destKey, array $options = [])
    // {
    //     $srcKey  = $this->__keyPreProcess($srcKey);
    //     $destKey = $this->__keyPreProcess($destKey);

    //     $options += [
    //         'Bucket'     => $this->_config['bucketName'],
    //         'Key'        => $destKey,
    //         'CopySource' => $this->_config['bucketName'] . '/' . $srcKey,
    //         'ACL'        => 'public-read',
    //     ];

    //     return $this->_blobClient->copyObject($options);
    // }

    // /**
    //  * Call DeleteObject API.
    //  *
    //  * @see S3Client::deleteObject
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobject
    //  *
    //  * @param string $key
    //  * @param array  $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function deleteObject($key, array $options = [])
    // {
    //     $key = $this->__keyPreProcess($key);

    //     $options += [
    //         'Bucket' => $this->_config['bucketName'],
    //         'Key'    => $key,
    //     ];

    //     return $this->_blobClient->deleteObject($options);
    // }

    // /**
    //  * Call DeleteObjects API.
    //  *
    //  * @see S3Client::deleteObjects
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobjects
    //  *
    //  * @param array $keys
    //  * @param array $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function deleteObjects($keys, array $options = [])
    // {
    //     foreach ($keys as $index => $key) {
    //         $keys[$index] = [
    //             'Key' => $this->__keyPreProcess($key),
    //         ];
    //     }

    //     $options += [
    //         'Bucket' => $this->_config['bucketName'],
    //         'Delete' => [
    //             'Objects' => $keys,
    //         ],
    //     ];

    //     return $this->_blobClient->deleteObjects($options);
    // }

    // /**
    //  * Call doesObjectExists API.
    //  *
    //  * @see S3Client::doesObjectExist
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#headobject
    //  *
    //  * @param string $key
    //  * @param array  $options
    //  *
    //  * @return bool
    //  */
    // public function doesObjectExist($key, array $options = [])
    // {
    //     $key = $this->__keyPreProcess($key);

    //     return $this->_blobClient->doesObjectExist(
    //         $this->_config['bucketName'],
    //         $key,
    //         $options
    //     );
    // }

    // /**
    //  * Call GetObject API.
    //  *
    //  * @see S3Client::getObject
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#getobject
    //  *
    //  * @param string $key
    //  * @param array  $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function getObject($key, array $options = [])
    // {
    //     $key = $this->__keyPreProcess($key);

    //     $options += [
    //         'Bucket' => $this->_config['bucketName'],
    //         'Key'    => $key,
    //         'ACL'    => 'public-read',
    //     ];

    //     return $this->_blobClient->getObject($options);
    // }

    // /**
    //  * Call HeadObject API.
    //  *
    //  * @see S3Client::headObject
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#headobject
    //  *
    //  * @param string $key
    //  * @param array  $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function headObject($key, array $options = [])
    // {
    //     $key = $this->__keyPreProcess($key);

    //     $options += [
    //         'Bucket' => $this->_config['bucketName'],
    //         'Key'    => $key,
    //     ];

    //     return $this->_blobClient->headObject($options);
    // }

    // /**
    //  * Call PutObject API.
    //  *
    //  * @see S3Client::putObject
    //  * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
    //  *
    //  * @param string $key
    //  * @param string $content
    //  * @param array  $options
    //  *
    //  * @return \Aws\Result
    //  */
    // public function putObject($key, $content, array $options = [])
    // {
    //     $key = $this->__keyPreProcess($key);

    //     $options += [
    //         'Bucket' => $this->_config['bucketName'],
    //         'Key'    => $key,
    //         'ACL'    => 'public-read',
    //         'Body'   => $content,
    //     ];

    //     return $this->_blobClient->putObject($options);
    // }
}
