<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zalas\Injector\Service\Exception\MissingServicePropertyException;
use Zalas\Injector\Service\Property;
use Zalas\Injector\Tests\Service\Fixtures\Foo;

class PropertyTest extends TestCase
{
    public function test_it_exposes_the_class_and_property_names_and_the_service_id()
    {
        $property = new Property(Foo::class, 'foo', 'my.service.id');

        $this->assertSame(Foo::class, $property->getClassName());
        $this->assertSame('foo', $property->getPropertyName());
        $this->assertSame('my.service.id', $property->getServiceId());
    }

    public function test_it_throws_an_exception_if_class_property_does_not_exist()
    {
        $this->expectException(MissingServicePropertyException::class);

        new Property(Foo::class, 'bar', 'my.service.id');
    }
}
