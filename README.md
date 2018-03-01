# Injector

Injects services from a [PSR-11 dependency injection container](https://github.com/php-fig/container) into objects.
Service information is read from tagged class properties, but extension points are provided to read them from any source.

## Installation

```bash
composer require zalas/injector
```

## Usage

```php
class Foo
{
    /**
     * @var Service1
     * @inject
     */
    private $service1;

    /**
     * @Service2
     * @inject foo.service2
     */
    private $service2;

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
