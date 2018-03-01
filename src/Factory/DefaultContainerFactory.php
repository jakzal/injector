<?php
declare(strict_types=1);

namespace Zalas\Injector\Factory;

use Psr\Container\ContainerInterface;
use Zalas\Injector\Service\ContainerFactory;

final class DefaultContainerFactory implements ContainerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(): ContainerInterface
    {
        return $this->container;
    }
}
