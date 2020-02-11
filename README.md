# Injector

[![Build Status](https://travis-ci.org/jakzal/injector.svg?branch=master)](https://travis-ci.org/jakzal/injector)

Injects services from a [PSR-11 dependency injection container](https://github.com/php-fig/container) into objects.
Service information is read from class properties annotated with `@inject`, but extension points are provided
to read them from any source.

Example of a class that default implementation of injector can work with:

```php
class Foo
{
    /**
     * @inject
     */
    private Service1 $service1;

    /**
     * @inject foo.service2
     */
    private Service2 $service2;
}
```

## Why?

The library is useful in situations when we have no control over how objects are instantiated, so we can't use
proper dependency injection. One use case is feeding services from an application container to integration test cases.
Test cases are instantiated by a test framework, so it's not possible to provide additional dependencies during
construction time. However, since test frameworks usually give ways to hook into the initialization process, it's still
possible to provide additional dependencies before test cases are called.

PHPUnit integration is actually implemented in [`zalas/phpunit-injector`](https://packagist.org/packages/zalas/phpunit-injector) (github repository: https://github.com/jakzal/phpunit-injector).

## Installation

```bash
composer require zalas/injector
```

## Usage

Properties should be annotated with `@inject` in order for them to be recognised by the default injector implementation:

```php
class Foo
{
    /**
     * @inject
     */
    private Service1 $service1;

    /**
     * @inject foo.service2
     */
    private Service2 $service2;

    public function hasService1(): bool
    {
        return $this->service1 instanceof Service1;
    }

    public function hasService2(): bool
    {
        return $this->service2 instanceof Service2;
    }
}

class Service1
{
}

class Service2
{
}
```

The injector can be used to feed services into properties of an already instantiated object:

```php
use Zalas\Injector\Service\Injector;
use Zalas\Injector\Factory\DefaultContainerFactory;
use Zalas\Injector\Factory\DefaultExtractorFactory;

$foo = new Foo();
$container = /* create / fetch your container */;
$injector = new Injector(new DefaultContainerFactory($container), new DefaultExtractorFactory());
$injector->inject($foo);

var_dump($foo->hasService1());
// bool(true)
var_dump($foo->hasService2());
// bool(true)
```

## Extension points

`Zalas\Injector\Service\Injector` injects services to objects.
Services are provided by a PSR-11 container (`Psr\Container\ContainerInterface`).
Details of services to inject are read with an extractor (`Zalas\Injector\Service\Extractor`).

Both collaborators are accessed via their factories (`Zalas\Injector\Service\ContainerFactory`
and `Zalas\Injector\Service\ExtractorFactory`).

The injector provides two extension points:

 * container factory - allows to change the way container is created
 * extractor & extractor factory - allow to provide a custom way of extracting definitions of services to inject

### Container factory

The `Zalas\Injector\Factory\DefaultContainerFactory` is a default factory implementation that always returns
an instance of container created externally.

The `Zalas\Injector\Service\ContainerFactory` interface needs to be implemented to provide customised way of creating
the service container to be used with injector.

### Extractor & extractor factory

The default implementation of extractor (`Zalas\Injector\PhpDocumentor\ReflectionExtractor`) leverages
[phpDocumentor's reflection dockblock library](https://github.com/phpDocumentor/ReflectionDocBlock) for annotation parsing.
It uses the `@inject` annotation to read service information.
The annotation accepts service id as an optional value. Otherwise the type is used.
`Zalas\Injector\Factory\DefaultExtractorFactory` creates this default implementation of extractor.

Implement the `Zalas\Injector\Service\Extractor` interface to support your own way of extracting definitions of
services to inject. A factory that implements the `Zalas\Injector\Service\ExtractorFactory` will also need
to be created to instantiate the custom extractor.

## Contributing

Please read the [Contributing guide](CONTRIBUTING.md) to learn about contributing to this project.
Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project you agree to abide by its terms.
