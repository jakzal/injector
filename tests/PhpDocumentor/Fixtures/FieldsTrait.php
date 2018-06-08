<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Foo;

trait FieldsTrait
{
    /**
     * @inject foo.bar
     */
    private $fieldWithServiceIdNoVar;

    /**
     * @var Foo
     * @inject
     */
    private $fieldWithVarNoServiceId;

    /**
     * @var Foo
     * @inject foo.bar
     */
    private $fieldWithVarAndServiceId;

    /**
     * @var Foo
     */
    private $fieldWithNoInject;

    private $fieldWithNoDocBlock;
}
