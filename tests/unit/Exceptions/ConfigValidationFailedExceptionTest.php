<?php

namespace Graze\ConfigValidation\Test\Unit\Exceptions;

use Exception;
use Graze\ConfigValidation\Exceptions\ConfigValidationFailedException;
use Graze\ConfigValidation\Test\TestCase;

class ConfigValidationFailedExceptionTest extends TestCase
{
    public function testGenericExceptionHandling()
    {
        $orig = new Exception('test ');

        $exception = new ConfigValidationFailedException(__CLASS__, 'message', $orig);

        $this->assertEquals(
            <<<TEXT
Processor 'Graze\ConfigValidation\Test\Unit\Exceptions\ConfigValidationFailedExceptionTest' failed validation. Check params and options
test message
TEXT
            ,
            $exception->getMessage()
        );
    }
}
