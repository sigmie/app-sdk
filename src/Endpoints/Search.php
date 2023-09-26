<?php

declare(strict_types=1);

namespace Sigmie\Application\Endpoints;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Http\Contracts\JSONResponse;
use Sigmie\Http\JSONRequest;

trait Search
{
    public function search(string $index, array $body): JSONResponse
    {
        $req = new JSONRequest('POST', new Uri("/v1/search/{$index}"), $body);

        return $this->http->request($req);
    }
}
