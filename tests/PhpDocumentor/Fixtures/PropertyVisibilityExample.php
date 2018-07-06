<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class PropertyVisibilityExample
{
    /** @inject foo */
    public $foo;

    /** @inject bar */
    protected $bar;

    /** @inject baz */
    private $baz;
}
