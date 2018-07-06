<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\PhpDocumentor;

use PHPUnit\Framework\TestCase;
use Zalas\Injector\PhpDocumentor\ReflectionExtractor;
use Zalas\Injector\Service\Exception\MissingServiceIdException;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\Property;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\ChildInjectionExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\DuplicatedInjectExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\DuplicatedVarExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\FieldInjectionExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\FieldsImportedWithTraitExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\Foo\Foo;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\MissingTypeExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\OverridePrivatePropertyExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\OverrideProtectedPropertyExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\OverridePublicPropertyExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\PropertyVisibilityExample;
use Zalas\Injector\Tests\PhpDocumentor\Fixtures\RedefinePropertiesExample;

class ReflectionExtractorTest extends TestCase
{
    /**
     * @var ReflectionExtractor
     */
    private $servicePropertyExtractor;

    protected function setUp()
    {
        $this->servicePropertyExtractor = new ReflectionExtractor();
    }

    public function test_it_is_a_property_extractor()
    {
        $this->assertInstanceOf(Extractor::class, $this->servicePropertyExtractor);
    }

    public function test_it_extracts_service_definitions_from_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(FieldInjectionExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithServiceIdNoVar', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithVarNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithVarAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_extracts_service_definitions_from_trait_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(FieldsImportedWithTraitExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithServiceIdNoVar', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithVarNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithVarAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_ignores_a_duplicated_type()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(DuplicatedVarExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(1, $serviceProperties);
        $this->assertEquals(new Property(DuplicatedVarExample::class, 'fooWithDuplicatedVar', Foo::class), $serviceProperties[0]);
    }

    public function test_it_ignores_a_duplicated_inject()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(DuplicatedInjectExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(1, $serviceProperties);
        $this->assertEquals(new Property(DuplicatedInjectExample::class, 'fooWithDuplicatedInject', 'foo.bar'), $serviceProperties[0]);
    }

    public function test_it_throws_missing_service_id_exception_if_there_is_no_service_id_nor_type()
    {
        $this->expectException(MissingServiceIdException::class);

        $this->servicePropertyExtractor->extract(MissingTypeExample::class);
    }

    public function test_it_extracts_service_definitions_from_parent_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(ChildInjectionExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithServiceIdNoVar', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithVarNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithVarAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_extracts_service_definitions_from_redefined_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(RedefinePropertiesExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'foo', 'foo'), $serviceProperties[0]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'bar', 'bar'), $serviceProperties[1]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'baz', 'baz'), $serviceProperties[2]);
    }

    public function test_it_extracts_service_definitions_from_overriden_public_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(OverridePublicPropertyExample::class);

        $this->assertCount(4, $serviceProperties);
        $this->assertEquals(new Property(OverridePublicPropertyExample::class, 'foo', 'foo.overridden'), $serviceProperties[0]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'foo', 'foo'), $serviceProperties[1]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'bar', 'bar'), $serviceProperties[2]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'baz', 'baz'), $serviceProperties[3]);
    }

    public function test_it_extracts_service_definitions_from_overriden_protected_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(OverrideProtectedPropertyExample::class);

        $this->assertCount(4, $serviceProperties);
        $this->assertEquals(new Property(OverrideProtectedPropertyExample::class, 'bar', 'bar.overridden'), $serviceProperties[0]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'foo', 'foo'), $serviceProperties[1]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'bar', 'bar'), $serviceProperties[2]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'baz', 'baz'), $serviceProperties[3]);
    }

    public function test_it_extracts_service_definitions_from_overriden_private_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(OverridePrivatePropertyExample::class);

        $this->assertCount(4, $serviceProperties);
        $this->assertEquals(new Property(OverridePrivatePropertyExample::class, 'baz', 'baz.overridden'), $serviceProperties[0]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'foo', 'foo'), $serviceProperties[1]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'bar', 'bar'), $serviceProperties[2]);
        $this->assertEquals(new Property(PropertyVisibilityExample::class, 'baz', 'baz'), $serviceProperties[3]);
    }
}
