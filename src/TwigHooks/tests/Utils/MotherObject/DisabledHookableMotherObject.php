<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\TwigHooks\Utils\MotherObject;

use Sylius\TwigHooks\Hookable\DisabledHookable;

final class DisabledHookableMotherObject
{
    public static function with(array $parameters): DisabledHookable
    {
        if (!array_key_exists('hookName', $parameters)) {
            $parameters['hookName'] = 'some_hook';
        }

        if (!array_key_exists('name', $parameters)) {
            $parameters['name'] = 'some_name';
        }

        return new DisabledHookable(...$parameters);
    }
}
