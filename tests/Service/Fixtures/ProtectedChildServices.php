<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service\Fixtures;

class ProtectedChildServices extends Services
{
    /**
     * @var Service1Custom
     */
    protected $service1;

    public function getChildService1(): ?Service1Custom
    {
        return $this->service1;
    }
}
