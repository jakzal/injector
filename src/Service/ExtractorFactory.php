<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

/**
 * Creates an service property extractor.
 */
interface ExtractorFactory
{
    public function create(): Extractor;
}
