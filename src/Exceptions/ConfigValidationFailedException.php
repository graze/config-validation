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

use Exception;
use Respect\Validation\Exceptions\NestedValidationException;

class ConfigValidationFailedException extends Exception
{
    /**
     * ConfigValidationFailedException constructor.
     *
     * @param string         $processorClass
     * @param int            $message
     * @param Exception|null $e
     */
    public function __construct($processorClass, $message, Exception $e = null)
    {
        $newMessage = "Processor '$processorClass' failed validation. Check params and options\n";
        if ($e instanceof NestedValidationException) {
            $newMessage .= $this->getNestedMessage($e);
        } else {
            $newMessage .= $e->getMessage();
        }
        $newMessage .= $message;

        parent::__construct($newMessage, 0, $e);
    }

    /**
     * @param NestedValidationException $e
     *
     * @return string
     */
    private function getNestedMessage(NestedValidationException $e)
    {
        $message = [];
        $iterator = $e->getMessages();
        foreach ($iterator as $m) {
            $message[] = $m;
        }

        return implode(PHP_EOL, $message);
    }
}
