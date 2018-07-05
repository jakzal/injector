<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service4;

class ServicesConflict extends ServicesBase
{
    /**
     * @var Service4
     * @inject
     */
    protected $service4;
}
