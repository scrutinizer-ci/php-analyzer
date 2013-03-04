<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ThisType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionTypeBuilder;
use Scrutinizer\PhpAnalyzer\Model\Clazz;

class UnionTypeBuilderTest extends BaseTypeTest
{
    public function testAllType()
    {
        $this->assertUnion('*', 'all');
        $this->assertUnion('*', 'number', 'all');
        $this->assertUnion('*', 'all', 'number');
        $this->assertUnion('*', 'all', 'integer', 'none');
    }

    public function testEmptyUnion()
    {
        $this->assertUnion('NoType');
        $this->assertUnion('NoType', 'none', 'none');
    }

    public function testUnionTypes()
    {
        $this->assertUnion('*', 'all', 'string|object');
        $this->assertUnion('object|string', 'object', 'string|object');
        $this->assertUnion('string|object', 'string|object', 'object');
        $this->assertUnion('object|string|integer|double', 'object|string', 'number');
    }

    public function testUnknownTypes()
    {
        $this->assertUnion('?', 'unknown');
        $this->assertUnion('?', 'unknown', 'unknown');
        $this->assertUnion('?', 'unknown', $this->registry->getClassOrCreate('FooBar'));
        $this->assertUnion('?', 'unknown', 'null');
        $this->assertUnion('?', 'unknown', 'array|null');
    }

    public function testObjectTypes()
    {
        $class = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo');
        $class->setNormalized(true);
        $this->registry->registerClass($class);

        $this->assertUnion('object<Foo>', new NamedType($this->registry, 'Foo'), $class);

        $named = new NamedType($this->registry, 'Foo');
        $named->setReferencedType($class);
        $this->assertUnion('object<Foo>', $named, $class);
    }

    public function testNullableThisType()
    {
        $class = new Clazz('Foo');
        $class->setNormalized(true);
        $this->registry->registerClass($class);

        $builder = new UnionTypeBuilder($this->registry);
        $this->assertCount(0, $builder->getAlternates());

        $builder->addAlternate($this->registry->getNativeType('null'));
        $this->assertCount(1, $builder->getAlternates());

        $builder->addAlternate(new ThisType($this->registry, $class));
        $this->assertCount(2, $builder->getAlternates());

        $type = $builder->build();
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType', $type);
    }

    private function assertUnion($expected)
    {
        $builder = new UnionTypeBuilder($this->registry);

        $types = func_get_args();
        $types = array_splice($types, 1);

        foreach ($types as $type) {
            $type = $this->resolveType($type);
            $builder->addAlternate($type);
        }

        $this->assertEquals($expected, (string) $builder->build());
    }
}