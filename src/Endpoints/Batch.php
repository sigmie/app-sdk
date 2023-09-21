<?php

declare(strict_types=1);

namespace Sigmie\Application\Endpoints;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Http\Contracts\JSONResponse;
use Sigmie\Http\JSONRequest;

trait Batch 
{
    public function batchRead(string $index, array $body): JSONResponse
    {
        $req = new JSONRequest('POST', new Uri("/v1/index/{$index}/batch"), $body);

        return $this->http->request($req);
    }
}
