<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor\Fixtures;

use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Foo;

class FieldInjectionExample
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

    private Foo $fieldWithNoInject;

    private $fieldWithNoDocBlock;
}
