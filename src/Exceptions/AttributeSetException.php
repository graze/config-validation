<?php

/*
 * This file is based on KeySetException of Respect/Validation.
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
