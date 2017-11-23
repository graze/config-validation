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

namespace Graze\ConfigValidation\Exceptions;

use Respect\Validation\Exceptions\GroupedValidationException;

class AttributeSetException extends GroupedValidationException
{
    const STRUCTURE = 2;

    /**
     * @var array
     */
    public static $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::NONE      => 'All of the required rules must pass for {{name}}',
            self::SOME      => 'These rules must pass for {{name}}',
            self::STRUCTURE => 'Must not have unknown attributes {{attributes}}',
        ],
        self::MODE_NEGATIVE => [
            self::NONE      => 'None of these rules must pass for {{name}}',
            self::SOME      => 'These rules must not pass for {{name}}',
            self::STRUCTURE => 'Must have unknown attributes {{attributes}}',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function chooseTemplate()
    {
        if ($this->getParam('attributes')) {
            return static::STRUCTURE;
        }

        return parent::chooseTemplate();
    }
}
