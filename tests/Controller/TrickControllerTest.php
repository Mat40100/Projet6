<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', 'http://127.0.0.1:8000/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testView()
    {
        $client = static::createClient();

        $client->request('GET', 'http://127.0.0.1:8000/view/503-Aerial');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}