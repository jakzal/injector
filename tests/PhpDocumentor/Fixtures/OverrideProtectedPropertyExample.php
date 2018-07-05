<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class OverrideProtectedPropertyExample extends PropertyVisibilityExample
{
    /** @inject bar.overridden */
    protected $bar;
}
