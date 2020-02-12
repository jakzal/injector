<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Reflection\Fixtures;

use Zalas\Injector\Tests\Reflection\Fixtures\Foo\Foo;

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
