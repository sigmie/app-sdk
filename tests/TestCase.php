<?php

declare(strict_types=1);

namespace Sigmie\Application;

use Dotenv\Dotenv;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/..');
        $dotenv->load();
    }
}
