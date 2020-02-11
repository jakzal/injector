<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service4;

class ServicesConflict extends ServicesBase
{
    /**
     * @inject
     */
    protected Service4 $service4;
}
