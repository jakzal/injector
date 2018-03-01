<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\Injector\Factory\DefaultContainerFactory;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\Service\ContainerFactory;
use Zalas\Injector\Service\Injector;
use Zalas\Injector\Tests\Integration\Fixtures\Service1;
use Zalas\Injector\Tests\Integration\Fixtures\Service2;
use Zalas\Injector\Tests\Integration\Fixtures\Services;

class InjectorTest extends TestCase
{
    public function test_it_injects_services_into_class_properties_with_reflection_extractor()
    {
        $injector = new Injector($this->createContainerFactory(), new DefaultExtractorFactory());

        $services = new Services();

        $injector->inject($services);

        $this->assertInstanceOf(Service1::class, $services->getService1());
        $this->assertInstanceOf(Service2::class, $services->getService2());
    }

    private function createContainerFactory(): ContainerFactory
    {
        return new DefaultContainerFactory(new class implements ContainerInterface
        {
            public function get($id)
            {
                if (Service1::class === $id) {
                    return new Service1();
                }

                if ('foo.service2' === $id) {
                    return new Service2();
                }

                throw new class extends \Exception implements NotFoundExceptionInterface
                {
                };
            }

            public function has($id)
            {
                return in_array($id, [Service1::class, 'foo.service2']);
            }
        });
    }
}
