<?php

namespace Graze\ConfigValidation;

use Graze\DataStructure\Container\FlatContainer;
use InvalidArgumentException;

class Definition extends FlatContainer
{
    /**
     * @param string     $key
     * @param mixed|Leaf $value Only accept leafs
     *
     * @return \Graze\DataStructure\Container\ContainerInterface
     */
    public function set($key, $value)
    {
        if (!$value instanceof Leaf) {
            throw new InvalidArgumentException('value must be a type of: leaf');
        }
        return parent::set($key, $value);
    }

    /**
     * @param string     $key
     * @param mixed|Leaf $value Only accept Leaf objects
     *
     * @return \Graze\DataStructure\Container\Container
     */
    public function add($key, $value)
    {
        if (!$value instanceof Leaf) {
            throw new InvalidArgumentException('value must be a type of: leaf');
        }
        return parent::add($key, $value);
    }
}
