<?php

namespace Graze\ConfigValidation;

use Exception;
use Graze\ConfigValidation\Exceptions\ConfigValidationFailedException;
use Respect\Validation\Rules\Key;
use Respect\Validation\Rules\KeySet;
use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;

/**
 * ArrayValidator takes an input array and validates it against a defined schema
 */
class ArrayValidator implements ConfigValidatorInterface
{
    use ValidatorBuilderTrait;

    const SEPARATOR = '.';

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
                $validators[] = (new Key(
                    $key,
                    (isset($node['validator']) ? $node['validator'] : null),
                    $node['required']
                ))->setName($namePrefix . $this->separator . $key);
            } else {
                $validators[] = (new Key(
                    $key,
                    $this->buildValidator($node, $namePrefix . $this->separator . $key),
                    $this->hasMandatoryItem($node)
                ));
            }
        }
        if ($this->allowUnspecified) {
            return v::allOf(v::arrayType(), ...$validators)->setName($namePrefix);
        } else {
            return (new KeySet(...$validators))->setName($namePrefix);
        }
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    public function populate($item)
    {
        return $this->populateItems($item, $this->validators);
    }

    /**
     * @param array $array
     * @param array $definition
     *
     * @return array
     */
    private function populateItems(array $array, array $definition)
    {
        $output = $array;
        foreach ($definition as $key => $node) {
            if (isset($node['required'])) {
                if ((!$node['required']) && (!isset($output[$key]))) {
                    $output[$key] = $node['default'];
                }
            } else {
                if (!isset($output[$key])) {
                    $output[$key] = [];
                }
                $output[$key] = $this->populateItems($output[$key], $node);
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
            $item = [];
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
     * Determine if the provided item is valid or not
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function isValid($item)
    {
        if (!$item) {
            $item = [];
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
