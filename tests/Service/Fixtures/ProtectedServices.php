<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Service\Fixtures;

class ProtectedServices
{
    /**
     * @var Service1
     */
    protected $service1;

    public function getService1(): ?Service1
    {
        return $this->service1;
    }
}
