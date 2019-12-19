<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Bar;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Foo;

class TypedFieldInjectionExample
{
    /**
     * @inject foo.bar
     */
    private $fieldWithServiceIdNoType;

    /**
     * @inject
     */
    private Foo $fieldWithTypeNoServiceId;

    /**
     * @inject foo.bar
     */
    private Foo $fieldWithTypeAndServiceId;

    /**
     * @var Bar
     * @inject
     */
    private Foo $fieldWithConflictingTypeAndVar;

    private Foo $fieldWithNoInject;

    private $fieldWithNoDocBlock;
}
