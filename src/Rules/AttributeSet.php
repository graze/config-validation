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
     * The parent constructor assigns the variadic rules directly to the stack without routing them through
     * addRule(), so we override it to run our Attribute/AllOf filtering (and to accept nested arrays of rules).
     *
     * @param mixed ...$rules
     *
     * @throws ComponentException
     */
    public function __construct(...$rules)
    {
        $this->addRules($rules);
    }

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
     * @param Validatable $rule
     *
     * @return AttributeSet
     * @throws ComponentException
     */
    public function addRule(Validatable $rule): self
    {
        if ($rule instanceof AllOf) {
            $rule = $this->filterAllOf($rule);
        }

        if (!$rule instanceof Attribute) {
            throw new ComponentException('AttributeSet rule accepts only Attribute rules');
        }

        parent::addRule($rule);

        return $this;
    }

    /**
     * @param array $rules
     *
     * @return AttributeSet
     * @throws ComponentException
     */
    public function addRules(array $rules): self
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
            $keys[] = $attributeRule->getReference();
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
            if (array_key_exists($attributeRule->getReference(), $mirror)) {
                unset($mirror[$attributeRule->getReference()]);
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
     */
    public function assert($input): void
    {
        $this->checkAttributes($input);

        parent::assert($input);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $input
     */
    public function check($input): void
    {
        $this->checkAttributes($input);

        parent::check($input);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (!$this->hasValidStructure($input)) {
            return false;
        }

        return parent::validate($input);
    }
}
