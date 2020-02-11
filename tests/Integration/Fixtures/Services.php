<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service1;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service2;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service3;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service4;

class Services extends ServicesBase
{
    use ServicesTrait;

    /**
     * @inject
     */
    private Service1 $service1;

    /**
     * @inject foo.service2
     */
    private Service2 $service2;

    public function getService1(): ?Service1
    {
        return $this->service1;
    }

    public function getService2(): ?Service2
    {
        return $this->service2;
    }

    public function getService3(): ?Service3
    {
        return $this->service3;
    }

    public function getService4(): ?Service4
    {
        return $this->service4;
    }
}
