<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class OverridePrivatePropertyExample extends PropertyVisibilityExample
{
    /** @inject something_else */
    private $baz;
}
