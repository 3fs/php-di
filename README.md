# trifs/DI

trifs/DI is a simple Dependency Injection Container for PHP, based on awesome [Pimple](http://pimple.sensiolabs.org/) internals. It is, however, behaving as an object, not as an array. It also provides less features, namely protecting parameters, modifying services after creation and fetching the service creation function.

[![Build Status](https://travis-ci.org/3fs/php-di.svg?branch=master)](https://travis-ci.org/3fs/php-di)


## Installation

To include trifs/DI in your project, add it to your `composer.json`:

```javascript
{
    "require": {
        "trifs/di": "~1.0"
    }
}
```


## Usage

Creating a container is as simple as instantiating it:

```php
use trifs\DI;

$container = new Container();
```

As many other dependency injection containers, trifs\DI is able to manage two different kind of data; services and parameters.

### Defining parameters

```php
$container->cookie_name = 'SESSION_ID';
$container->session_storage_class = 'SessionStorage';
```

### Defining services

A service is an object that does something as part of a larger system, for example: database connection, session handler, etc.

Services are defined by anonymous functions that return an instance of an object:

```php
$container->session_storage = function (Container $container) {
    return new $container->session_storage_class($container->cookie_name);
};

$container->session = function (Container $container) {
    return new Session($c['session_storage']);
};
```

Notice that the anonymous function has access to the current container instance, allowing references to other services or parameters.

As objects are only created when you get them, the order of the definitions does not matter, and there is no performance penalty.

Using the defined services is also very easy:

```php
// get the session object
$session = $container->session;

// the above call is roughly equivalent to the following code:
// $storage = new SessionStorage('SESSION_ID');
// $session = new Session($storage);
```

### Extending a container

If you use the same libraries over and over, you might want to reuse some services from one project to the other; package your services into a provider by implementing `trifs\DI\ServiceProviderInterface`:

```php
use trifs\DI\Container;
use trifs\DI\ServiceProviderInterface;

class FooProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // register services and parameters
    }
}
```

And to register it:

```php
$container->register(new FooProvider());
```

### Defining factory services

By default, each time you get a service, trifs\DI returns the same instance of it. If you want a different instance to be returned for all calls, wrap your anonymous function with the `factory()` method:

```php
$container->session = $container->factory(function (Container $container) {
    return new Session($container->session_storage);
});
```
