<?php
declare(strict_types=1);

namespace Zalas\Injector\Factory;

use Zalas\Injector\Reflection\ReflectionExtractor;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\ExtractorFactory;

final class DefaultExtractorFactory implements ExtractorFactory
{
    /**
     * @var string[]
     */
    private $ignoredClasses;

    /**
     * @var string[]
     */
    public function __construct(array $ignoredClasses = [])
    {
        $this->ignoredClasses = $ignoredClasses;
    }

    public function create(): Extractor
    {
        return new ReflectionExtractor($this->ignoredClasses);
    }
}
