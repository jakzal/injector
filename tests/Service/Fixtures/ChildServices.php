<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service\Fixtures;

class ChildServices extends Services
{
    /**
     * @var Service2Custom
     */
    private $service2;

    public function getChildService2(): ?Service2Custom
    {
        return $this->service2;
    }
}
