<?php

namespace Sigmie\Application\Enums;

enum BatchWriteAction: string
{
    case Upsert = 'upsert';

    case Patch = 'patch';

    case Delete = 'delete';
}
