<?php

declare(strict_types=1);

namespace Sigmie\Application;

class BatchEndpointsTest extends TestCase
{
    /**
     * @test
     */
    public function batch_read_empty()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $client->createIndex($index);

        $res = $client->batchRead($index, []);

        $this->assertEquals(200, $res->code());
        $this->assertEmpty($res->json());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_read_null_id()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchRead($index, [
            [
                '_id' => null
            ]
        ]);

        $this->assertEquals(200, $res->code());
        $this->assertEquals([
            [
                "error" => "document/not_found",
                "message" => "A document with the _id '' couldn't be found."
            ]
        ], $res->json());

        $client->deleteIndex($index);
    }


    /**
     * @test
     */
    public function batch_read()
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

        $res = $client->batchRead($index, []);

        $this->assertEquals(200, $res->code());
        $this->assertEmpty($res->json());

        $res = $client->batchRead($index, [
            [
                '_id' => $id
            ],
            [
                '_id' => 'undefined'
            ]
        ]);

        $this->assertEquals(200, $res->code());
        $this->assertEquals([
            [
                '_id' => $id,
                'name' => 'Snowflake',
                'autocomplete' => []
            ],
            [
                'error' => 'document/not_found',
                'message' => 'A document with the _id \'undefined\' couldn\'t be found.'
            ]

        ], $res->json());


        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function invalid_json_on_batch_write()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $client->createIndex($index);

        $res = $client->batchRead(index: $index, body: [
            'Hmm'
        ]);

        $this->assertEquals(400, $res->code());

        $client->deleteIndex($index);
    }
}
