<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Reflection\Fixtures;

class OverridePrivatePropertyExample extends PropertyVisibilityExample
{
    /** @inject baz.overridden */
    private $baz;
}
