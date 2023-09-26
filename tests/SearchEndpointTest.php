<?php

declare(strict_types=1);

namespace Sigmie\Application;

class SearchEndpointTest extends TestCase
{
    /**
     * @test
     */
    public function search_endpoint()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $client->createIndex($index);

        $client->upsertDocument($index, ['title' => 'Scooby']);

        sleep(1);

        $res = $client->search($index, []);

        $this->assertEquals(200, $res->code());

        $json = $res->json();

        $this->assertArrayHasKey('hits', $json);
        $this->assertNotEmpty($json['hits']);

        $this->assertArrayHasKey('processing_time_ms', $json);

        $this->assertArrayHasKey('total', $json);
        $this->assertEquals(1, $json['total']);

        $this->assertArrayHasKey('per_page', $json);
        $this->assertEquals(10, $json['per_page']);

        $this->assertArrayHasKey('page', $json);
        $this->assertEquals(1, $json['page']);

        $this->assertArrayHasKey('query', $json);
        $this->assertEquals("", $json['query']);

        $this->assertArrayHasKey('autocomplete', $json);
        $this->assertEquals([""], $json['autocomplete']);

        $this->assertArrayHasKey('params', $json);
        $this->assertEquals("", $json['params']);

        $this->assertArrayHasKey('index', $json);
        $this->assertEquals($index, $json['index']);

        $this->assertArrayHasKey('filters', $json);
        $this->assertEquals("", $json['filters']);

        $this->assertArrayHasKey('facets', $json);
        $this->assertEquals(['all' => []], $json['facets']);

        $this->assertArrayHasKey('sort', $json);
        $this->assertEquals("", $json['sort']);

        $this->assertArrayHasKey('errors', $json);
        $this->assertEmpty($json['errors']);

        $client->deleteIndex($index);
    }

    /**
     * @test
     */
    public function search_filter_facets_sort_results()
    {
        $client = new Client(
            applicationId: getenv('SIGMIE_APPLICATION_ID'),
            apiKey: getenv('SIGMIE_API_KEY')
        );

        $index = uniqid();

        $client->createIndex($index);

        $res = $client->search($index, [
            "filters" => "title:'Pikaboo'",
            "sort" => '_score title:asc',
            "facets" => "demo -j'\"2\"3'l23489**@_#!_!",
            'per_page' => -10,
            'page' => -120,
        ]);

        $this->assertEquals(200, $res->code());

        $json = $res->json();
$this->assertEquals(1, $json['page']);
        $this->assertEquals([], $json['facets']);
        $this->assertEquals("_score title:asc", $json['sort']);

        $this->assertEquals("title:'Pikaboo'", $json['filters']);
        $this->assertNotEmpty($json['errors']);

        $client->deleteIndex($index);
    }
}
