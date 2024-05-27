<?php

namespace App\Tests;

use App\Services\DatasourceConnectorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatasourceTest extends KernelTestCase
{
    public function testDatasource(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $datasourceConnectorService = static::getContainer()->get(DatasourceConnectorService::class);

        $listResponse = $datasourceConnectorService->getList();
        $this->assertArrayHasKey('success', $listResponse, 'list retrieve');
        $this->assertTrue($listResponse['success'], 'list success');
        $this->assertGreaterThan(0, $listResponse['result']['total'], 'list is not empty');

        $detailsResponse = $datasourceConnectorService->getDetails('wrong_id');
        $this->assertArrayHasKey('success', $detailsResponse, 'details retrieve wrong_id');
        $this->assertTrue($detailsResponse['success'], 'details success wrong_id');
        $this->assertEquals($detailsResponse['result']['total'], 0,'details not found wrong_id');

        $detailsResponse = $datasourceConnectorService->getDetails($listResponse['result']['records'][0]['STATION_ID']);
        $this->assertArrayHasKey('success', $detailsResponse, 'details retrieve '.$listResponse['result']['records'][0]['STATION_ID']);
        $this->assertTrue($detailsResponse['success'], 'details success '.$listResponse['result']['records'][0]['STATION_ID']);
        $this->assertEquals($detailsResponse['result']['total'], 1,'details found '.$listResponse['result']['records'][0]['STATION_ID']);
    }
}
