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
namespace trifs\DI\Tests;

use trifs\DI\Container;

/**
 * Unit tests for DI container.
 *
 * @author   David Kuridža <david@3fs.si>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testNativeTypes()
    {
        $values = [
            'boolean' => (bool)rand(0, 1),
            'integer' => rand(1337, 1337),
            'float'   => 1.337,
            'string'  => 'HereBe some string',
            'array'   => [1 => ['a' => 'b']],
            'null'    => null,
        ];

        $container = new Container();

        foreach ($values as $type => $value) {
            $container->{$type} = $value;
            $this->assertSame($value, $container->{$type});
        }
    }

    /**
     * @return void
     */
    public function testClosure()
    {
        $container = new Container();
        $container->service = function () {
            return new \stdClass();
        };

        $this->assertInstanceOf('\stdClass', $container->service);
    }

    /**
     * @return void
     */
    public function testServicesShouldBeSame()
    {
        $container = new Container();
        $container->service = function () {
            return new \stdClass();
        };

        $serviceOne = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceOne);

        $serviceTwo = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceTwo);

        $this->assertSame($serviceOne, $serviceTwo);
    }

    /**
     * @return void
     */
    public function testServicesShouldBeDifferent()
    {
        $container = new Container();
        $container->service = $container->factory(function () {
            return new \stdClass();
        });

        $serviceOne = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceOne);

        $serviceTwo = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionCode    0
     * @expectedExceptionMessage Identifier "waldo" is not defined.
     * @return                   void
     */
    public function testInvalidArgumentRequired()
    {
        $container = new Container();
        $container->waldo;
    }

    /**
     * @return void
     */
    public function testIsset()
    {
        $container = new Container();
        $container->null    = null;
        $container->param   = 'value';
        $container->service = function () {
            return new \stdClass();
        };

        $this->assertTrue(isset($container->service));
        $this->assertTrue(isset($container->param));
        $this->assertTrue(isset($container->null));
        $this->assertFalse(isset($container->not_even_set));
    }

    /**
     * @return void
     */
    public function testUnset()
    {
        $container = new Container();
        $container->null    = null;
        $container->param   = 'value';
        $container->service = function () {
            return new \stdClass();
        };

        unset(
            $container->null,
            $container->param,
            $container->service
        );

        $this->assertFalse(isset($container->service));
        $this->assertFalse(isset($container->param));
        $this->assertFalse(isset($container->null));
    }

    /**
     * @return void
     */
    public function testRegister()
    {
        $container = new Container();
        $container->register(new Fixture\ServiceProvider());

        $this->assertSame(null, $container->null);
        $this->assertSame('value', $container->param);

        $serviceOne = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceOne);
        $serviceTwo = $container->service;
        $this->assertInstanceOf('\stdClass', $serviceTwo);
        $this->assertSame($serviceOne, $serviceTwo);

        $serviceOne = $container->factory;
        $this->assertInstanceOf('\stdClass', $serviceOne);
        $serviceTwo = $container->factory;
        $this->assertInstanceOf('\stdClass', $serviceTwo);
        $this->assertNotSame($serviceOne, $serviceTwo);
    }
}
