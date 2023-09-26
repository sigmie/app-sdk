<?php

declare(strict_types=1);

namespace Sigmie\Application;

use Sigmie\Application\Endpoints;
use Sigmie\Http\Contracts\JSONClient as JSONClientInterface;
use Sigmie\Http\JSONClient;

class Client
{
    use Endpoints\Index;
    use Endpoints\Document;
    use Endpoints\Batch;
    use Endpoints\Search;

    public JSONClientInterface $http;

    public int $timeout = 30;

    public function __construct(
        protected string $applicationId,
        protected string $apiKey,
        null|JSONClientInterface $http = null
    ) {
        $this->http = $http ?: JSONClient::createWithHeaders([
            "https://{$applicationId}-a.sigmie.app",
            "https://{$applicationId}-b.sigmie.app",
            "https://{$applicationId}-c.sigmie.app",
        ], [
            'X-Sigmie-Application' => $this->applicationId,
            'X-Sigmie-API-Key' => $this->apiKey
        ], [
            'connect_timeout' => $this->timeout
        ]);
    }

    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }
}
