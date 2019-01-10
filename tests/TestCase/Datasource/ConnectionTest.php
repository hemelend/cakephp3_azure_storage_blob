<?php
namespace CakeAzureStorageBlob\Test\TestCase\Datasource;

use CakeAzureStorageBlob\Datasource\Connection;
use Cake\TestSuite\TestCase;
use \Mockery as m;

/**
 * Connection Testcase
 */

class ConnectionTest extends TestCase
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
     * @return \Mockery\MockInterface
     */
    private function __getblobClientMock()
    {
        $mock = m::mock('overload:\MicrosoftAzure\Storage\Blob\BlobRestProxy');
        $mock->shouldReceive('createBlobService')
            ->once();

        return $mock;
    }

    /**
     * @return \CakeAzureStorageBlob\Datasource\Connection
     */
    private function __getConnectionInstance()
    {
        $params = [
            'AccountName'   => 'testaccount',
            'AccountKey'    => 'iqwneoifhaofoifnoaifha/O0Ny7ug79r9qZRlBKrT37JP87RJlGetCN7gUQ4SNttrDggEsdoEleUov7tPLARXg==',
            'ContainerName' => 'testcontainer',
        ];

        return new Connection($params);
    }

    /**
     * Test new instance success
     *
     * 
     * @return void
     */
    public function testNewInstanceSuccess()
    {
        $this->__getblobClientMock();

        $connection = $this->__getConnectionInstance();

        $config = $connection->config();
        
        $this->assertEquals('iqwneoifhaofoifnoaifha/O0Ny7ug79r9qZRlBKrT37JP87RJlGetCN7gUQ4SNttrDggEsdoEleUov7tPLARXg==', $config['AccountKey']);
    }

    // /**
    //  * Test new instance failed, missing arguments
    //  *
    //  * @return void
    //  */
    // public function testNewInstanceMissingArguments()
    // {
    //     $this->expectException(\InvalidArgumentException::class);

    //     $params = [];
    //     new Connection($params);
    // }

    // /**
    //  * Test createContainer method
    //  */
    // public function testcreateContainer()
    // {
    //     $mock = $this->__getblobClientMock();
    //     $mock->shouldReceive('createContainer')
    //         ->once()
    //         ->with([
    //             'ContainerName' => 'testcontainer',
    //         ]);

    //     $connection = $this->__getConnectionInstance();
    //     $connection->createContainer('/testcontainer');
    // }
}
?>