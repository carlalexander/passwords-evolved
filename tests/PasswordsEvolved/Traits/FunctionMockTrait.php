<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Traits;

use phpmock\phpunit\PHPMock;

/**
 * Adds mocking methods for mocking PHP functions.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
trait FunctionMockTrait
{
    use PHPMock;

    /**
     * Get the namespace of the given class.
     *
     * @param string $className
     *
     * @return string
     */
    protected function getNamespace($className)
    {
        return (new \ReflectionClass($className))->getNamespaceName();
    }
}
