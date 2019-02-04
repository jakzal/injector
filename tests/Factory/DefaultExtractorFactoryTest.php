<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\PhpDocumentor\ReflectionExtractor;
use Zalas\Injector\Service\ExtractorFactory;

class DefaultExtractorFactoryTest extends TestCase
{
    /**
     * @var DefaultExtractorFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new DefaultExtractorFactory();
    }

    public function test_it_is_an_extractor_factory()
    {
        $this->assertInstanceOf(ExtractorFactory::class, $this->factory);
    }

    public function test_it_creates_phpdocumentor_reflection_extractor_by_default()
    {
        $this->assertInstanceOf(ReflectionExtractor::class, $this->factory->create());
    }
}
