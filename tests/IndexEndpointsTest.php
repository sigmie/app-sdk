<?php

declare(strict_types=1);

namespace Sigmie\Application;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Http\JSONRequest;

class IndexEndpointsTest extends TestCase
{
    /**
     * @test
     */
    public function delete_no_existing_index()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $name = uniqid();

        $res = $client->deleteIndex(name: $name);

        $this->assertEquals(404, $res->psr()->getStatusCode());

        $this->assertArrayHasKey('error', $res->json());
        $this->assertEquals('index/not_found', $res->json('error'));
    }

    /**
     * @test
     */
    public function create_delete_index()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $name = uniqid();

        $res = $client->createIndex(name: $name);

        $this->assertEquals(202, $res->psr()->getStatusCode());
        $this->assertArrayHasKey('name', $res->json());
        $this->assertEquals($name, $res->json('name'));

        $res = $client->deleteIndex(name: $name);

        $this->assertEquals(200, $res->psr()->getStatusCode());

        $res = $client->deleteIndex($name);

        $this->assertEquals(404, $res->psr()->getStatusCode());
    }

    /**
     * @test
     */
    public function clear_index_no_found()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $name = uniqid();

        $res = $client->clearIndex(name: $name);

        $this->assertEquals('index/not_found', $res->json('error'));
        $this->assertEquals(404, $res->psr()->getStatusCode());
    }

    /**
     * @test
     */
    public function clear_index()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $name = uniqid();

        $res = $client->createIndex($name);

        $res = $client->clearIndex(name: $name);

        $this->assertEquals(202, $res->psr()->getStatusCode());
        $this->assertEquals($name, $res->json('name'));

        $client->deleteIndex($name);
    }

    /**
     * @test
     */
    public function route_no_found()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $res = $client->http->request(new JSONRequest('PUT', new Uri('/not/existing-route')));

        $this->assertEquals('route/not_found',$res->json('error'));
    }
}
