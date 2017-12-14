<?php

namespace Graze\ConfigValidation\Test\Unit;

use Graze\ConfigValidation\Definition;
use Graze\ConfigValidation\Leaf;
use Graze\ConfigValidation\Test\TestCase;
use Graze\DataStructure\Container\FlatContainer;

class DefinitionTest extends TestCase
{
    /** @var Definition */
    private $definition;

    public function setUp()
    {
        $this->definition = new Definition();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(FlatContainer::class, $this->definition);
    }

    public function testAcceptsAddingLeaf()
    {
        $leaf = new Leaf();
        $this->definition->add('some.key', $leaf);

        $this->assertSame($leaf, $this->definition->get('some.key'));
    }

    public function testAcceptsSettingLeaf()
    {
        $leaf = new Leaf();
        $this->definition->set('some.key', $leaf);

        $this->assertSame($leaf, $this->definition->get('some.key'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDoesNotAcceptNonLeaves()
    {
        $this->definition->add('some.key', []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDoesNotAcceptSettingNonLeaves()
    {
        $this->definition->set('some.key', []);
    }
}
