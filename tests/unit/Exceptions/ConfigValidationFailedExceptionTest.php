<?php
/**
 * This file is part of graze/config-validation.
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/config-validation/blob/master/LICENSE.md
 * @link    https://github.com/graze/config-validation
 */

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
