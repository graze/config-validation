<?php

namespace Graze\ConfigValidation;

/**
 * Validate is a Facade / factory to wrap Array and Object methods to help with construction
 *
 * @package Graze\ConfigValidation
 */
class Validate
{
    /**
     * @param bool $allowUnassigned
     *
     * @return ObjectValidator
     */
    public static function object($allowUnassigned = true)
    {
        return new ObjectValidator($allowUnassigned);
    }

    /**
     * @param bool $allowUnassigned
     *
     * @return ArrayValidator
     */
    public static function arr($allowUnassigned = true)
    {
        return new ArrayValidator($allowUnassigned);
    }
}
