<?php

declare(strict_types=1);

namespace Sigmie\Application\Endpoints;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Application\Request;
use Sigmie\Http\Contracts\JSONResponse;
use Sigmie\Http\JSONRequest;

trait Index
{
    public function updateIndex(string $name, array $body = []): JSONResponse
    {
        $req = new JSONRequest('PUT', new Uri("/v1/index/{$name}"), $body);

        return $this->http->request($req);
    }

    public function createIndex(string $name): JSONResponse
    {
        $req = new JSONRequest('POST', new Uri("/v1/index/{$name}"));

        return $this->http->request($req);
    }

    public function deleteIndex(string $name): JSONResponse
    {
        $req = new JSONRequest('DELETE', new Uri("/v1/index/{$name}"));

        return $this->http->request($req);
    }

    public function clearIndex(string $name): JSONResponse
    {
        $req = new JSONRequest('POST', new Uri("/v1/index/{$name}/clear"));

        return $this->http->request($req);
    }
}
