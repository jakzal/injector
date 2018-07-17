<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

class RedefinePropertiesExample extends PropertyVisibilityExample
{
    public $foo;

    protected $bar;

    private $baz;
}
