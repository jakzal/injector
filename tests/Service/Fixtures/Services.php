<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service\Fixtures;

class Services
{
    private Service1 $service1;

    private Service2 $service2;

    public function getService1(): ?Service1
    {
        return $this->service1;
    }

    public function getService2(): ?Service2
    {
        return $this->service2;
    }
}
