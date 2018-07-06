<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\Injector\Factory\DefaultContainerFactory;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\Service\ContainerFactory;
use Zalas\Injector\Service\Exception\AmbiguousInjectionDefinitionException;
use Zalas\Injector\Service\Injector;
use Zalas\Injector\Tests\Integration\Fixtures\Services;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service1;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service2;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service3;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service4;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service5;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service6;
use Zalas\Injector\Tests\Integration\Fixtures\ServicesConflict;

class InjectorTest extends TestCase
{
    public function test_it_injects_services_into_class_properties_with_reflection_extractor()
    {
        $injector = new Injector($this->createContainerFactory(), new DefaultExtractorFactory());

        $services = new Services();

        $injector->inject($services);

        $this->assertInstanceOf(Service1::class, $services->getService1());
        $this->assertInstanceOf(Service2::class, $services->getService2());
        $this->assertInstanceOf(Service3::class, $services->getService3());
        $this->assertInstanceOf(Service4::class, $services->getService4());
        $this->assertInstanceOf(Service5::class, $services->getService5());
        $this->assertInstanceOf(Service6::class, $services->getService6());
    }

    public function test_it_throws_an_exception_if_service_definitions_are_duplicated_in_the_child_class()
    {
        $this->expectException(AmbiguousInjectionDefinitionException::class);

        $injector = new Injector($this->createContainerFactory(), new DefaultExtractorFactory());
        $injector->inject(new ServicesConflict());
    }

    private function createContainerFactory(): ContainerFactory
    {
        return new DefaultContainerFactory(new class implements ContainerInterface {
            public function get($id)
            {
                if (\in_array($id, [Service1::class, Service2::class, Service3::class, Service4::class, Service5::class, Service6::class])) {
                    return new $id();
                }

                if ('foo.service2' === $id) {
                    return new Service2();
                }

                throw new class extends \Exception implements NotFoundExceptionInterface {
                };
            }

            public function has($id)
            {
                return \in_array($id, [Service1::class, Service2::class, Service3::class, Service4::class, Service5::class, Service6::class, 'foo.service2'], true);
            }
        });
    }
}
