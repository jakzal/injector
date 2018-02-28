<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

use Psr\Container\ContainerInterface;

/**
 * Creates a service container.
 */
interface ContainerFactory
{
    public function create(): ContainerInterface;
}
