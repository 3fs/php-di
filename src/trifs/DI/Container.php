<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 3fs d.o.o.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace trifs\DI;

/**
 * DI container main class.
 *
 * @author   David Kuridža <david@3fs.si>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class Container
{

    /**
     * Holds factory objects.
     *
     * @var \SplObjectStorage
     */
    private $factories;

    /**
     * Holds all raw values.
     *
     * @var array
     */
    private $raw   = [];

    /**
     * Holds all set values.
     *
     * @var array
     */
    private $values = [];

    /**
     * Initializes container.
     *
     * @return void
     */
    public function __construct()
    {
        $this->factories = new \SplObjectStorage();
    }

    /**
     * Sets value.
     *
     * @param  string     $name
     * @param  mixed|null $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Returns value.
     *
     * @param  string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (false === array_key_exists($name, $this->values)) {
            throw new \InvalidArgumentException(sprintf(
                'Identifier "%s" is not defined.',
                $name
            ));
        }

        // already set or not an object to mess around
        if (isset($this->raw[$name]) || false === is_object($this->values[$name])) {
            return $this->values[$name];
        }

        // ensure invokation in case of factory
        if ($this->factories->offsetExists($this->values[$name])) {
            return $this->values[$name]($this);
        }

        // ensure it's invoked only once
        $this->raw[$name] = $this->values[$name];
        return $this->values[$name] = $this->values[$name]($this);
    }

    /**
     * Returns a flag indicating whether a property has been set.
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->values);
    }

    /**
     * Unsets all values for matching $name.
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->values)) {
            if (is_object($this->values[$name])) {
                unset($this->factories[$this->values[$name]]);
            }
            unset(
                $this->raw[$name],
                $this->values[$name]
            );
        }
    }

    /**
     * Marks a callable as being a factory service.
     *
     * @param  callable $callable
     * @return \Closure
     */
    public function factory(callable $callable)
    {
        $this->factories->attach($callable);
        return $callable;
    }

    /**
     * Registers a service provider.
     *
     * @param  ServiceProviderInterface $provider
     * @return Container
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        return $this;
    }
}
