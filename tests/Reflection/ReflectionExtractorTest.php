<?php
declare(strict_types=1);

namespace Zalas\Injector\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use Zalas\Injector\Reflection\ReflectionExtractor;
use Zalas\Injector\Service\Exception\MissingServiceIdException;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\Property;
use Zalas\Injector\Tests\Reflection\Fixtures\ChildInjectionExample;
use Zalas\Injector\Tests\Reflection\Fixtures\DuplicatedInjectExample;
use Zalas\Injector\Tests\Reflection\Fixtures\FieldInjectionExample;
use Zalas\Injector\Tests\Reflection\Fixtures\FieldsImportedWithTraitExample;
use Zalas\Injector\Tests\Reflection\Fixtures\Foo\Foo;
use Zalas\Injector\Tests\Reflection\Fixtures\MissingTypeExample;
use Zalas\Injector\Tests\Reflection\Fixtures\OverridePrivatePropertyExample;
use Zalas\Injector\Tests\Reflection\Fixtures\OverrideProtectedPropertyExample;
use Zalas\Injector\Tests\Reflection\Fixtures\OverridePublicPropertyExample;
use Zalas\Injector\Tests\Reflection\Fixtures\PropertyVisibilityExample;
use Zalas\Injector\Tests\Reflection\Fixtures\RedefinePropertiesExample;

class ReflectionExtractorTest extends TestCase
{
    /**
     * @var ReflectionExtractor
     */
    private $servicePropertyExtractor;

    protected function setUp(): void
    {
        $this->servicePropertyExtractor = new ReflectionExtractor([]);
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
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithServiceIdNoType', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithTypeNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithTypeAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_extracts_service_definitions_from_trait_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(FieldsImportedWithTraitExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithServiceIdNoType', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithTypeNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldsImportedWithTraitExample::class, 'fieldWithTypeAndServiceId', 'foo.bar'), $serviceProperties[2]);
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
        $this->expectExceptionMessageMatches('/The `.*?::fooWithNoServiceIdAndVar` property was configured for service injection, but no service type nor id was given/');

        $this->servicePropertyExtractor->extract(MissingTypeExample::class);
    }

    public function test_it_extracts_service_definitions_from_parent_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(ChildInjectionExample::class);

        $this->assertContainsOnlyInstancesOf(Property::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithServiceIdNoType', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithTypeNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new Property(FieldInjectionExample::class, 'fieldWithTypeAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_does_not_extract_properties_from_ignored_classes()
    {
        $this->servicePropertyExtractor = new ReflectionExtractor([FieldInjectionExample::class]);

        $serviceProperties = $this->servicePropertyExtractor->extract(ChildInjectionExample::class);

        $this->assertCount(0, $serviceProperties);
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
