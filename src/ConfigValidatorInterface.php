<?php

namespace Graze\ConfigValidation;

use Graze\ConfigValidation\Exceptions\ConfigValidationFailedException;
use Respect\Validation\Validatable;

interface ConfigValidatorInterface
{
    /**
     * The specified path is a required field for the
     *
     * @param string      $path
     * @param Validatable $validator
     *
     * @return mixed
     */
    public function required($path, Validatable $validator = null);

    /**
     * @param string      $path
     * @param Validatable $validator
     * @param mixed       $default
     *
     * @return mixed
     */
    public function optional($path, Validatable $validator = null, $default = null);

    /**
     * @param string $name
     *
     * @return Validatable
     */
    public function getValidator($name = '');

    /**
     * Populate an object with all optional properties
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function populate($item);

    /**
     * @param mixed $item
     *
     * @return mixed
     * @throws ConfigValidationFailedException
     */
    public function validate($item);

    /**
     * Determine if the provided object is valid or not
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function isValid($item);

    /**
     * @param bool $allowUnspecified
     *
     * @return $this
     */
    public function setAllowUnspecified($allowUnspecified);

    /**
     * @return bool
     */
    public function isAllowUnspecified();

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator($separator);

    /**
     * @return string
     */
    public function getSeparator();
}
