<?php

declare(strict_types=1);

namespace TPG\PHPayfast;

use TPG\PHPayfast\Exceptions\ValidationException;
use TPG\Yerp\Validator as BaseValidator;

class Validator
{
    public static function validate(object $class)
    {
        $validated = (new BaseValidator($class))->validate();

        if ($validated->failed()) {

            $messages = array_values(array_map(fn (array $m) => implode(',', $m), $validated->messages()));

            throw new ValidationException($messages[0]);
        }
    }
}
