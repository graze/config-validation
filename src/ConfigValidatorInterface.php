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
