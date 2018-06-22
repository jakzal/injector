<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class PropertyVisibilityExample
{
    /** @inject \Foo */
    public $foo;

    /** @inject \Bar */
    protected $bar;

    /** @inject \Baz */
    private $baz;
}
