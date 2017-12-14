<?php

namespace Graze\ConfigValidation;

use Respect\Validation\Validatable;

class Leaf
{
    /** @var bool */
    private $required = false;
    /** @var Validatable|null */
    private $validator = null;
    /** @var mixed|null */
    private $default = null;

    /**
     * Leaf constructor.
     *
     * @param bool             $required
     * @param Validatable|null $validator
     * @param mixed|null       $default
     */
    public function __construct($required = false, Validatable $validator = null, $default = null)
    {
        $this->required = $required;
        $this->validator = $validator;
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return Leaf
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return null|Validatable
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param null|Validatable $validator
     *
     * @return Leaf
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed|null $default
     *
     * @return Leaf
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }
}
