<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service\Fixtures;

class ChildServices extends Services
{

    /**
     * @var Service2Custom
     */
    protected $service2;
    /**
     * @var Service1Custom
     */
    private $service1;

    public function getChildService1(): ?Service1Custom
    {
        return $this->service1;
    }

    public function getChildService2(): ?Service2Custom
    {
        return $this->service2;
    }
}
