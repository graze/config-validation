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

use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;

trait ValidatorBuilderTrait
{
    /** @var string */
    protected $separator = '.';

    /**
     * @var array [path][here]['required' => bool, 'validator' => Validator, 'default' => mixed]
     */
    protected $validators = [];

    /**
     * @var bool
     */
    protected $dirty = true;

    /**
     * @var Validatable[]
     */
    protected $validator = [];

    /**
     * @param string           $path
     * @param bool             $required
     * @param Validatable|null $validator
     * @param mixed|null       $default
     *
     * @return $this
     */
    protected function addValidator($path, $required, Validatable $validator = null, $default = null)
    {
        $parent = &$this->validators;
        foreach (explode($this->separator, $path) as $node) {
            if (!isset($parent[$node])) {
                $parent[$node] = [];
            }
            $parent = &$parent[$node];
        }

        $parent['required'] = $required;
        $parent['validator'] = $validator;
        $parent['default'] = $default;

        $this->dirty = true;

        return $this;
    }

    /**
     * @param string      $path
     * @param Validatable $validator
     *
     * @return $this
     */
    public function required($path, Validatable $validator = null)
    {
        $this->addValidator($path, true, $validator);
        return $this;
    }

    /**
     * @param string      $path
     * @param Validatable $validator
     * @param mixed|null  $default
     *
     * @return $this
     */
    public function optional($path, Validatable $validator = null, $default = null)
    {
        if (!is_null($validator) && is_null($default)) {
            $validator = v::oneOf($validator, v::nullType());
        }

        $this->addValidator($path, false, $validator, $default);
        return $this;
    }

    /**
     * @param string $name
     *
     * @return Validatable
     */
    public function getValidator($name = '')
    {
        if ($this->dirty || !isset($this->validator[$name]) || $this->validator[$name] == null) {
            $this->validator[$name] = $this->buildValidator($this->validators, $name);
            $this->dirty = false;
        }

        return $this->validator[$name];
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param array  $definition
     * @param string $namePrefix
     *
     * @return Validatable
     */
    protected abstract function buildValidator(array $definition, $namePrefix = '');

    /**
     * Traverse the definition to see if any children are required
     *
     * @param array $definition
     *
     * @return bool
     */
    protected function hasMandatoryItem(array $definition)
    {
        foreach ($definition as $key => $node) {
            if (isset($node['required'])) {
                if ($node['required']) {
                    return true;
                }
            } else {
                if ($this->hasMandatoryItem($node)) {
                    return true;
                }
            }
        }

        return false;
    }
}
