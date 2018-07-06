<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service4;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service5;
use Zalas\Injector\Tests\Integration\Fixtures\Services\Service6;

class ServicesBase
{
    /**
     * @var Service4
     * @inject
     */
    protected $service4;

    /**
     * @var Service5
     * @inject
     */
    protected $service5;

    /**
     * @var Service6
     * @inject
     */
    private $service6;

    public function getService5(): ?Service5
    {
        return $this->service5;
    }

    public function getService6(): ?Service6
    {
        return $this->service6;
    }
}
