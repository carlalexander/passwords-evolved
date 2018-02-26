<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\DependencyInjection;

/**
 * The plugin's dependency injection container.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Container implements \ArrayAccess
{
    /**
     * Flag that checks if the container is locked or not.
     *
     * @var bool
     */
    private $locked;

    /**
     * Values stored inside the container.
     *
     * @var array
     */
    private $values;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->locked = false;
        $this->values = $values;
    }

    /**
     * Configure the container using the given container configuration object(s).
     *
     * @param mixed $configurations
     */
    public function configure($configurations)
    {
        if (!is_array($configurations)) {
            $configurations = array($configurations);
        }

        foreach ($configurations as $configuration) {
            $this->modify($configuration);
        }
    }

    /**
     * Checks if the container is locked or not.
     *
     * @return bool
     */
    public function is_locked()
    {
        return $this->locked;
    }

    /**
     * Locks the container so that it can't be modified.
     */
    public function lock()
    {
        $this->locked = true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        if (!array_key_exists($key, $this->values)) {
            throw new \InvalidArgumentException(sprintf('Container doesn\'t have a value stored for the "%s" key.', $key));
        } elseif (!$this->is_locked()) {
            $this->lock();
        }

        return $this->values[$key] instanceof \Closure ? $this->values[$key]($this) : $this->values[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        if ($this->locked) {
            throw new \RuntimeException('Container is locked and cannot be modified.');
        }

        $this->values[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        if ($this->locked) {
            throw new \RuntimeException('Container is locked and cannot be modified.');
        }

        unset($this->values[$key]);
    }

    /**
     * Creates a closure used for creating a service using the given callable.
     *
     * @param callable $callable
     *
     * @return callable
     */
    public function service($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

        return function (Container $container) use ($callable) {
            static $object;

            if (null === $object) {
                $object = $callable($container);
            }

            return $object;
        };
    }

    /**
     * Modify the container using the given container configuration object.
     *
     * @param mixed $configuration
     */
    private function modify($configuration)
    {
        if (is_string($configuration)) {
            $configuration = new $configuration();
        }

        if (!$configuration instanceof ContainerConfigurationInterface) {
            throw new \InvalidArgumentException('Configuration object must implement the "ContainerConfigurationInterface".');
        }

        $configuration->modify($this);
    }
}
