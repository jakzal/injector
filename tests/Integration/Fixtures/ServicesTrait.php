<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Integration\Fixtures;

use Zalas\Injector\Tests\Integration\Fixtures\Services\Service3;

trait ServicesTrait
{
    /**
     * @var Service3
     * @inject
     */
    private $service3;
}
