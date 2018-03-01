<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Bar;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Foo;

class DuplicatedVarExample
{
    /**
     * @var Foo
     * @var Bar
     * @inject
     */
    private $fooWithDuplicatedVar;
}