<?php

namespace Graze\ConfigValidation\Test\Unit\Rules;

use Graze\ConfigValidation\Exceptions\AttributeSetException;
use Graze\ConfigValidation\Rules\AttributeSet;
use PHPUnit_Framework_TestCase as TestCase;
use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\AlwaysValid;
use Respect\Validation\Rules\Attribute;

class AttributeSetTest extends TestCase
{
    public function testShouldAcceptAttributeRule()
    {
        $attribute = new Attribute('foo', new AlwaysValid(), false);
        $attributeSet = new AttributeSet($attribute);

        $rules = $attributeSet->getRules();

        $this->assertSame(current($rules), $attribute);
    }

    public function testShouldAcceptAllOfWithOneAttributeRule()
    {
        $attribute = new Attribute('foo', new AlwaysValid(), false);
        $allOf = new AllOf($attribute);
        $attributeSet = new AttributeSet($allOf);

        $rules = $attributeSet->getRules();

        $this->assertSame(current($rules), $attribute);
    }

    /**
     * @expectedException \Respect\Validation\Exceptions\ComponentException
     * @expectedExceptionMessage AllOf rule must have only one Attribute rule
     */
    public function testShouldNotAcceptAllOfWithMoreThanOneAttributeRule()
    {
        $attribute1 = new Attribute('foo', new AlwaysValid(), false);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);
        $allOf = new AllOf($attribute1, $attribute2);

        new AttributeSet($allOf);
    }

    /**
     * @expectedException \Respect\Validation\Exceptions\ComponentException
     * @expectedExceptionMessage AttributeSet rule accepts only Attribute rules
     */
    public function testShouldNotAcceptAllOfWithANonAttributeRule()
    {
        $alwaysValid = new AlwaysValid();
        $allOf = new AllOf($alwaysValid);

        new AttributeSet($allOf);
    }

    /**
     * @expectedException \Respect\Validation\Exceptions\ComponentException
     * @expectedExceptionMessage AttributeSet rule accepts only Attribute rules
     */
    public function testShouldNotAcceptANonAttributeRule()
    {
        $alwaysValid = new AlwaysValid();

        new AttributeSet($alwaysValid);
    }

    public function testShouldReturnAttributes()
    {
        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $this->assertEquals(['foo', 'bar'], $attributeSet->getAttributes());
    }

    public function testShouldValidateAttributesWhenThereAreMissingRequiredAttributes()
    {
        $input = (object)[
            'foo' => 42,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), true);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $this->assertFalse($attributeSet->validate($input));
    }

    public function testShouldValidateAttributesWhenThereAreMissingNonRequiredAttributes()
    {
        $input = (object)[
            'foo' => 42,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $this->assertTrue($attributeSet->validate($input));
    }

    public function testShouldValidateAttributesWhenThereAreMoreAttributes()
    {
        $input = (object)[
            'foo' => 42,
            'bar' => 'String',
            'baz' => false,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), false);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $this->assertFalse($attributeSet->validate($input));
    }

    public function testShouldValidateAttributesWhenEmpty()
    {
        $input = (object)[];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), true);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $this->assertFalse($attributeSet->validate($input));
    }

    /**
     * @expectedException \Respect\Validation\Exceptions\AttributeException
     * @expectedExceptionMessage Attribute foo must be present
     */
    public function testShouldCheckAttributesAndUseChildValidators()
    {
        $input = (object)[];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), true);

        $attributeSet = new AttributeSet($attribute1, $attribute2);
        $attributeSet->check($input);
    }

    public function testShouldAssertGetAttributesInException()
    {
        $input = (object)[
            'foo' => 42,
            'bar' => 'String',
            'baz' => false,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), false);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $raised = false;
        try {
            $attributeSet->assert($input);
        } catch (AttributeSetException $e) {
            $this->assertEquals(
                <<<ERR
- Must not have unknown attributes { "baz" }
ERR
                ,
                $e->getFullMessage()
            );
            $raised = true;
        }
        $this->assertTrue($raised);
    }

    public function testShouldAssertGetRequiredMissingInException()
    {
        $input = (object)[];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $raised = false;
        try {
            $attributeSet->assert($input);
        } catch (AttributeSetException $e) {
            $this->assertEquals(
                <<<ERR
- Attribute foo must be present
ERR
                ,
                $e->getFullMessage()
            );
            $raised = true;
        }
        $this->assertTrue($raised);
    }

    public function testShouldAcceptArrayOfAttributes()
    {
        $input = (object)[
            'foo' => 42,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet([[$attribute1, $attribute2]]);

        $this->assertTrue($attributeSet->validate($input));
    }

    public function testShouldByAbleToAssert()
    {
        $input = (object)[
            'foo' => 42,
        ];

        $attribute1 = new Attribute('foo', new AlwaysValid(), true);
        $attribute2 = new Attribute('bar', new AlwaysValid(), false);

        $attributeSet = new AttributeSet($attribute1, $attribute2);

        $attributeSet->assert($input);
    }
}
