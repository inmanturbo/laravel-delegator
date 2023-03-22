<?php

use Inmanturbo\Delegator\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function tempFile(string $fileName): string
{
    return __DIR__ . "/temp/{$fileName}";
}