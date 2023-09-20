<?php

declare(strict_types=1);

namespace Sigmie\Application\Endpoints;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Http\Contracts\JSONResponse;
use Sigmie\Http\JSONRequest;

trait Document
{
    public function upsertDocument(string $index, array $body, string $_id = ''): JSONResponse
    {
        $req = new JSONRequest('PUT', new Uri("/v1/index/{$index}/document/{$_id}"), $body);

        return $this->http->request($req);
    }

    public function patchDocument(string $index, array $body, string $_id): JSONResponse
    {
        $req = new JSONRequest('PATCH', new Uri("/v1/index/{$index}/document/{$_id}"), $body);

        return $this->http->request($req);
    }

    public function getDocument(string $index, string $_id): JSONResponse
    {
        $req = new JSONRequest('GET', new Uri("/v1/index/{$index}/document/{$_id}"));

        return $this->http->request($req);
    }

    public function deleteDocument(string $index, string $_id): JSONResponse
    {
        $req = new JSONRequest('DELETE', new Uri("/v1/index/{$index}/document/{$_id}"));

        return $this->http->request($req);
    }
}
