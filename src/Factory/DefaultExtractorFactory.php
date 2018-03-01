<?php
declare(strict_types=1);

namespace Zalas\Injector\Factory;

use Zalas\Injector\PhpDocumentor\ReflectionExtractor;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\ExtractorFactory;

final class DefaultExtractorFactory implements ExtractorFactory
{
    public function create(): Extractor
    {
        return new ReflectionExtractor();
    }
}
