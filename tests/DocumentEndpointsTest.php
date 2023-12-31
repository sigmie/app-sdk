<?php

declare(strict_types=1);

namespace Sigmie\Application;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Http\JSONRequest;

class DocumentEndpointsTest extends TestCase
{
    /**
     * @test
     */
    public function upsert_doc_index()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->upsertDocument($index, [
            'name' => 'Snowflake'
        ]);

        $this->assertEquals(201, $res->code());

        $res = $client->clearIndex($index);

        $this->assertNotEquals(404, $res->code());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function route_not_found_on_api_prefix()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $req = new JSONRequest('PUT', new Uri("/v1/something"), []);

        $res = $client->http->request($req);

        $this->assertEquals('route/not_found', $res->json('error'));
    }

    /**
     * @test
     */
    public function get_doc()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->upsertDocument($index, [
            'name' => 'Snowflake'
        ]);

        $this->assertEquals(201, $res->code());

        $res = $client->getDocument($index, $res->json('_id'));

        $this->assertEquals('Snowflake', $res->json('name'));
        $this->assertArrayHasKey('autocomplete', $res->json());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function patch_document()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->upsertDocument($index, [
            'name' => 'Snowflake'
        ]);

        $this->assertEquals('Snowflake', $res->json('name'));

        $res = $client->patchDocument($index, [
            'name' => 'Magic Fire'
        ], $res->json('_id'));

        $this->assertEquals('Magic Fire', $res->json('name'));

        $this->assertEquals(200, $res->code());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function delete_document()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->upsertDocument($index, [
            'name' => 'Snowflake'
        ]);

        $this->assertEquals(201, $res->code());

        $id = $res->json('_id');

        $res = $client->deleteDocument($index, $id);

        $this->assertEquals(202, $res->code());
        $this->assertEquals($id, $res->json('_id'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals(404, $res->code());
        $this->assertEquals('document/not_found', $res->json('error'));

        $client->deleteIndex($index);
    }
}
