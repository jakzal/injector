<?php
declare(strict_types=1);

namespace Zalas\Injector\Service\Exception;

use LogicException;
use Zalas\Injector\Service\Exception;

final class MissingServicePropertyException extends LogicException implements Exception
{
    public function __construct(string $class, string $propertyName)
    {
        parent::__construct(\sprintf('The `%s::%s` property does not exist. ', $class, $propertyName));
    }
}
