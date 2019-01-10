<?php
namespace CakeAzureStorageBlob\Datasource;

use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;

/**
 * Class AzureStorageBlobTable
 *
 * @package CakeAzureStorageBlob\Database
 */
class AzureStorageBlobTable
{
    /** @var string Connection configure name */
    protected static $_connectionName = '';

    /** @var Connection Connection instance */
    protected $_connection;

    /**
     * Get default connection name
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return static::$_connectionName;
    }

    /**
     * AwsS3Table constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->initialize($config);
    }

    /**
     * Returns the connection instance or sets a new one
     *
     * @param Connection|null $conn The new connection instance
     *
     * @return Connection
     */
    public function connection(ConnectionInterface $conn = null)
    {
        if ($conn === null) {
            return $this->_connection;
        }

        return $this->_connection = $conn;
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        $this->connection(ConnectionManager::get(static::$_connectionName));
    }

    /**
     * Call createContainer API
     *
     * @see BlobRestProxy::createBlockBlob
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     * 
     * @param string                        $container The container name.
     *
     * @return void
     *
     */
    public function createContainer($containerName)
    {
        return $this->connection()->createContainer($containerName);
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
        return $this->connection()->createBlockBlob($containerName, $fileToUpload, $content);
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
        return $this->connection()->deleteBlob($container, $blob, $options);
    }
}