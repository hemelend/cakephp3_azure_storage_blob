<?php
namespace CakeAzureStorageBlob\Test\TestCase\Datasource;

use CakeAzureStorageBlob\Datasource\AzureStorageBlobTable;
use Cake\TestSuite\TestCase;
use GuzzleHttp\Psr7\Stream;
use \Mockery as m;

/**
 * AwsS3Table Testcase
 */
class AzureStorageBlobTableTest extends TestCase
{
    /**
     * tear down method
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Connection APIs
     */
    public function testConnectionApi()
    {
        // Create Connection mock -> using ConnectionManager mock
        $connectionMock = m::mock('\CakeAzureStorageBlob\Datasource\Connection');
        $connectionMock->shouldReceive('createContainer')
            ->once()
            ->with('/testcontainer');
        // $connectionMock->shouldReceive('createBlockBlob')
        //     ->once()
        //     ->with('/test-key', ['option' => true]);
        // $connectionMock->shouldReceive('deleteBlob')
        //     ->once()
        //     ->with(['/test-key1', '/test-key2', '/test-key3'], ['option' => true]);

        // Create ConnectionManager mock
        $connectionManagerMock = m::mock('overload:\Cake\Datasource\ConnectionManager');
        $connectionManagerMock->shouldReceive('get')
            ->once()
            ->andReturn($connectionMock);

        // Test start.
        $AzureStorageBlobTable = new AzureStorageBlobTable();
        $AzureStorageBlobTable->createContainer('/testcontainer');
        // $AzureStorageBlobTable->createBlockBlob('/test-src-key', '/test-dest-key', ['option' => true]);
        // $AzureStorageBlobTable->deleteBlob('/test-key', ['option' => true]);
    }
}