<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class OverridePublicPropertyExample extends PropertyVisibilityExample
{
    /** @inject bar */
    public $foo;
}
