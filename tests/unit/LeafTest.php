<?php

namespace Graze\ConfigValidation\Test\Unit;

use Graze\ConfigValidation\Leaf;
use Graze\ConfigValidation\Test\TestCase;
use Respect\Validation\Validator;

class LeafTest extends TestCase
{
    public function testDefaults()
    {
        $leaf = new Leaf();

        $this->assertFalse($leaf->isRequired());
        $this->assertNull($leaf->getValidator());
        $this->assertNull($leaf->getDefault());
    }

    public function testConstructor()
    {
        $validator = Validator::stringType();
        $leaf = new Leaf(true, $validator, 'text');
        $this->assertTrue($leaf->isRequired());
        $this->assertSame($validator, $leaf->getValidator());
        $this->assertEquals('text', $leaf->getDefault());
    }

    public function testRequiredProperties()
    {
        $leaf = new Leaf();
        $this->assertFalse($leaf->isRequired());
        $this->assertSame($leaf, $leaf->setRequired(true));
        $this->assertTrue($leaf->isRequired());
    }

    public function testValidatorProperties()
    {
        $leaf = new Leaf();
        $this->assertNull($leaf->getValidator());
        $validator = Validator::stringType();
        $this->assertSame($leaf, $leaf->setValidator($validator));
        $this->assertSame($validator, $leaf->getValidator());
    }

    public function testDefaultProperties()
    {
        $leaf = new Leaf();
        $this->assertNull($leaf->getDefault());
        $this->assertSame($leaf, $leaf->setDefault('content'));
        $this->assertEquals('content', $leaf->getDefault());
    }
}
