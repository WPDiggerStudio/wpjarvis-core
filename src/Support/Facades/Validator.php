<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static \Illuminate\Contracts\Validation\Validator make(array $data, array $rules, array $messages = [], array $customAttributes = [])
 * @method static \Illuminate\Validation\Factory getFactory()
 *
 * @see \WPJarvis\Core\Validation\Validator
 */
class Validator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'validator';
    }
}

