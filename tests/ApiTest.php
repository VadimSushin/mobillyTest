<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    public function testApi(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'h4',
            'Create web service with REST API which provide info about wether stations in Latvia.'
        );

        $client->request('GET', '/list');
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), '/list returns json');
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseArray);
        $this->assertTrue($responseArray['success']);

        $validId = $responseArray['result'][0]['station_id'];

        $client->request('GET', '/details');
        $response = $client->getResponse();
        $this->assertSame(404, $response->getStatusCode());
        $this->assertJson($response->getContent(), '/details returns json');
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseArray);
        $this->assertTrue(!$responseArray['success']);

        $client->request('GET', '/details/'.$validId);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), '/details returns json');
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseArray);
        $this->assertTrue($responseArray['success']);

        $client->request('GET', '/details?id='.$validId);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), '/details returns json');
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseArray);
        $this->assertTrue($responseArray['success']);
    }
}
