<?php

declare(strict_types=1);

namespace Sigmie\Application;

use PHP_CodeSniffer\Tokenizers\JS;
use Sigmie\Application\Enums\BatchWriteAction;

class BatchWriteEndpointsTest extends TestCase
{
    /**
     * @test
     */
    public function write_empty()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $client->createIndex($index);

        $res = $client->batchWrite($index, []);

        $this->assertEquals(200, $res->code());
        $this->assertEmpty($res->json());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_write_null_actions()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => null,
                'body' => [
                    'name' => 'Snowflake'
                ]
            ]
        ]);

        $this->assertEquals(200, $res->code());
        $this->assertEquals([
            [
                "error" => "batch_action/unknown",
                "message" => "Unknown batch action 'null'."
            ]
        ], $res->json());

        $client->deleteIndex($index);
    }


    /**
     * @test
     */
    public function batch_create()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake'
                ]
            ]
        ]);

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_create_empty_body()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => []
            ]
        ]);

        $this->assertEquals('document/invalid', $res->json('0.error'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_create_no_body()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
            ]
        ]);

        $this->assertEquals('document/invalid', $res->json('0.error'));

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'foo' => ['bar']
            ]
        ]);

        $this->assertEquals('document/invalid', $res->json('0.error'));

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

        $res = $client->batchWrite(index: $index, body: [
            'Hmm'
        ]);

        $this->assertEquals(400, $res->code());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_upsert()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                '_id' => $id,
                'body' => [
                    'name' => 'Snowflake 123'
                ]
            ]
        ]);

        $this->assertEquals('updated', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake 123',
            'autocomplete' => []
        ], $res->json());


        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_delete()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Delete,
                '_id' => $id
            ]
        ]);

        $this->assertEquals('deleted', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals(404, $res->code());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_delete_without_id()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');


        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Delete
            ]
        ]);

        $this->assertEquals('batch_delete/id_required', $res->json('0.error'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_patch()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Patch,
                '_id' => $id,
                'body' => [
                    'name' => 'Prince'
                ]
            ]
        ]);

        $this->assertEquals('updated', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals(200, $res->code());
        $this->assertArrayHasKey('description', $res->json());

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_patch_without_id()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Patch,
                'body' => []
            ]
        ]);

        $this->assertEquals('batch_patch/id_required', $res->json('0.error'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_patch_with_empty_body()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Patch,
                '_id' => $id,
                'body' => []
            ]
        ]);

        $this->assertEquals('noop', $res->json('0.result'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function batch_patch_without_body()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ]
        ]);

        $id = $res->json('0._id');

        $this->assertNotEmpty($res->json('0._id'));
        $this->assertEquals('created', $res->json('0.result'));

        $res = $client->getDocument($index, $id);

        $this->assertEquals([
            '_id' => $id,
            'name' => 'Snowflake',
            'description' => 'Prince',
            'autocomplete' => []
        ], $res->json());

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Patch,
                '_id' => $id,
            ]
        ]);

        $this->assertEquals('noop', $res->json('0.result'));

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function multiple_actions()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $res = $client->batchWrite($index, [
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ],
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ],
            [
                'action' => BatchWriteAction::Patch,
            ],
            [
                'action' => BatchWriteAction::Delete,
            ],
            [
                'action' => BatchWriteAction::Upsert,
                'body' => [
                    'name' => 'Snowflake',
                    'description' => 'Prince'
                ]
            ],
        ]);

        $this->assertEquals('created', $res->json('0.result'));
        $this->assertEquals('created', $res->json('1.result'));
        $this->assertArrayHasKey('error', $res->json('2'));
        $this->assertArrayHasKey('error', $res->json('3'));
        $this->assertEquals('created', $res->json('4.result'));

        $client->deleteIndex($index);
    }
}
