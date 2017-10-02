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
