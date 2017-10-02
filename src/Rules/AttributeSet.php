<?php

namespace Graze\ConfigValidation\Rules;

use Graze\ConfigValidation\Exceptions\AttributeSetException;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\Attribute;
use Respect\Validation\Validatable;

/**
 * Validates attributes in a defined structure.
 */
class AttributeSet extends AllOf
{
    /**
     * @param AllOf $rule
     *
     * @return Validatable
     * @throws ComponentException
     */
    private function filterAllOf(AllOf $rule)
    {
        $rules = $rule->getRules();
        if (count($rules) != 1) {
            throw new ComponentException('AllOf rule must have only one Attribute rule');
        }

        return current($rules);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $rule
     * @param array $arguments
     *
     * @return $this
     * @throws ComponentException
     */
    // @codingStandardsIgnoreLine
    public function addRule($rule, $arguments = [])
    {
        if ($rule instanceof AllOf) {
            $rule = $this->filterAllOf($rule);
        }

        if (!$rule instanceof Attribute) {
            throw new ComponentException('AttributeSet rule accepts only Attribute rules');
        }

        $this->appendRule($rule);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $rules
     *
     * @return $this
     * @throws ComponentException
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            if (is_array($rule)) {
                $this->addRules($rule);
            } else {
                $this->addRule($rule);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $keys = [];
        foreach ($this->getRules() as $attributeRule) {
            $keys[] = $attributeRule->reference;
        }

        return $keys;
    }

    /**
     * @param object $input
     *
     * @return array unknown
     */
    private function checkValidStructure($input)
    {
        $mirror = ($input) ? (array)$input : [];

        foreach ($this->getRules() as $attributeRule) {
            if (array_key_exists($attributeRule->reference, $mirror)) {
                unset($mirror[$attributeRule->reference]);
            }
        }

        return array_keys($mirror);
    }

    /**
     * @param object $input
     *
     * @return bool
     */
    private function hasValidStructure($input)
    {
        $unknown = $this->checkValidStructure($input);

        return count($unknown) === 0;
    }

    /**
     * @param object $input
     *
     * @throws AttributeSetException
     */
    private function checkAttributes($input)
    {
        if (!$this->hasValidStructure($input)) {
            $unknown = $this->checkValidStructure($input);

            $params = [];
            if (count($unknown) !== 0) {
                $params['attributes'] = $unknown;
            }
            $exception = $this->reportError($input, $params);

            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function assert($input)
    {
        $this->checkAttributes($input);

        return parent::assert($input);
    }

    /**
     * {@inheritdoc}
     * @param mixed $input
     *
     * @return bool
     */
    public function check($input)
    {
        $this->checkAttributes($input);

        return parent::check($input);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input)
    {
        if (!$this->hasValidStructure($input)) {
            return false;
        }

        return parent::validate($input);
    }
}
