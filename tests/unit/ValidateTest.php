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

namespace Graze\ConfigValidation\Test\Unit;

use Graze\ConfigValidation\ArrayValidator;
use Graze\ConfigValidation\ConfigValidatorInterface;
use Graze\ConfigValidation\ObjectValidator;
use Graze\ConfigValidation\Test\TestCase;
use Graze\ConfigValidation\Validate;

class ValidateTest extends TestCase
{
    public function testArr()
    {
        $validator = Validate::arr();

        $this->assertInstanceOf(ArrayValidator::class, $validator);
        $this->assertInstanceOf(ConfigValidatorInterface::class, $validator);
    }

    public function testObject()
    {
        $validator = Validate::object();

        $this->assertInstanceOf(ObjectValidator::class, $validator);
        $this->assertInstanceOf(ConfigValidatorInterface::class, $validator);
    }

    public function testArrayUnspecified()
    {
        $validator = Validate::arr(false);

        $this->assertFalse($validator->isAllowUnspecified());
    }

    public function testObjectUnspecified()
    {
        $validator = Validate::object(false);

        $this->assertFalse($validator->isAllowUnspecified());
    }
}
