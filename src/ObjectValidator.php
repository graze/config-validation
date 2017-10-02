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

use Exception;
use Graze\ConfigValidation\Exceptions\ConfigValidationFailedException;
use Graze\ConfigValidation\Rules\AttributeSet;
use Respect\Validation\Rules\Attribute;
use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;
use stdClass;
use Throwable;

class ObjectValidator implements ConfigValidatorInterface
{
    use ValidatorBuilderTrait;

    const SEPARATOR = '->';

    /**
     * @var bool
     */
    protected $allowUnspecified = true;

    /**
     * @param bool $allowUnspecified Allow unspecified params (turning this off will fail if an attribute exists that
     *                               it is not expecting)
     */
    public function __construct($allowUnspecified = true)
    {
        $this->allowUnspecified = $allowUnspecified;
        $this->separator = static::SEPARATOR;
    }

    /**
     * @param array  $definition
     * @param string $namePrefix
     *
     * @return Validatable
     */
    private function buildValidator(array $definition, $namePrefix = '')
    {
        $validators = [];
        foreach ($definition as $key => $node) {
            if (isset($node['required'])) {
                $validators[] = (new Attribute(
                    $key,
                    (isset($node['validator']) ? $node['validator'] : null),
                    $node['required']
                ))->setName($namePrefix . $this->separator. $key);
            } else {
                $validators[] = (new Attribute(
                    $key,
                    $this->buildValidator($node, $namePrefix . $this->separator . $key),
                    $this->hasMandatoryItem($node)
                ));
            }
        }
        if ($this->allowUnspecified) {
            return v::allOf(v::objectType(), ...$validators)->setName($namePrefix);
        } else {
            return (new AttributeSet(...$validators))->setName($namePrefix);
        }
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    public function populate($item)
    {
        return $this->populateObjectItems($item, $this->validators);
    }

    /**
     * @param object $object
     * @param array  $definition
     *
     * @return object
     */
    private function populateObjectItems($object, array $definition)
    {
        $output = clone $object;
        foreach ($definition as $key => $node) {
            if (isset($node['required'])) {
                if ((!$node['required']) && (!isset($output->{$key}))) {
                    $output->{$key} = $node['default'];
                }
            } else {
                if (!isset($output->{$key})) {
                    $output->{$key} = new stdClass();
                }
                $output->{$key} = $this->populateObjectItems($output->{$key}, $node);
            }
        }
        return $output;
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     * @throws ConfigValidationFailedException
     */
    public function validate($item)
    {
        if (!$item) {
            $item = new \stdClass();
        }
        $validator = $this->getValidator();

        try {
            $validator->assert($item);
            return $this->populate($item);
        } catch (Exception $e) {
            throw new ConfigValidationFailedException(get_class($this), '', $e);
        }
    }

    /**
     * Determine if the provided object is valid or not
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function isValid($item)
    {
        if (!$item) {
            $item = new \stdClass();
        }
        $validator = $this->getValidator();

        return $validator->validate($item);
    }

    /**
     * @param bool $allowUnspecified
     *
     * @return $this
     */
    public function setAllowUnspecified($allowUnspecified)
    {
        $this->allowUnspecified = $allowUnspecified;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowUnspecified()
    {
        return $this->allowUnspecified;
    }
}
