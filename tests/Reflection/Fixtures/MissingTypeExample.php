<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Reflection\Fixtures;

class MissingTypeExample
{
    /**
     * @inject
     */
    private $fooWithNoServiceIdAndVar;
}
