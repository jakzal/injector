<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Reflection\Fixtures;

class OverridePublicPropertyExample extends PropertyVisibilityExample
{
    /** @inject foo.overridden */
    public $foo;
}
