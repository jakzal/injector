<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

use Zalas\Injector\Service\Exception\MissingServiceIdException;

interface Extractor
{
    /**
     * Extracts all the class properties that require a service to be injected.
     *
     * An exception should be thrown if the service id cannot be determined.
     *
     * @param string $class
     * @return Property[]
     *
     * @throws MissingServiceIdException
     */
    public function extract(string $class): array;
}
