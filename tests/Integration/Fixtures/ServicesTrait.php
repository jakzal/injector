<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service3;

trait ServicesTrait
{
    /**
     * @inject
     */
    private Service3 $service3;
}
