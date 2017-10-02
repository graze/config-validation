<?php

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
