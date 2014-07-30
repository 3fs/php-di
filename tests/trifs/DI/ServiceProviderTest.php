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
 * Unit tests for DI service provider.
 *
 * @author   David Kuridža <david@3fs.si>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testProvider()
    {
        $container = new Container();

        $serviceProvider = new Fixture\ServiceProvider();
        $serviceProvider->register($container);

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
