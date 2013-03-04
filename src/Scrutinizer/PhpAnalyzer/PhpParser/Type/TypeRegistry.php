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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

class TypeRegistry
{
    private static $allTypes = array('object', 'integer', 'double', 'string', 'null', 'array', 'boolean', 'resource');

    const NATIVE_UNKNOWN = 'unknown';
    const NATIVE_UNKNOWN_CHECKED = 'unknown_checked';
    const NATIVE_ALL = 'all';

    const NATIVE_BOOLEAN = 'boolean';
    const NATIVE_BOOLEAN_FALSE = 'false';
    const NATIVE_DOUBLE = 'double';
    const NATIVE_INTEGER = 'integer';
    const NATIVE_NONE = 'none';
    const NATIVE_NULL = 'null';
    const NATIVE_STRING = 'string';
    const NATIVE_ARRAY = 'array'; // The element type is the UNKNOWN type.
    const NATIVE_OBJECT = 'object';
    const NATIVE_CALLABLE = 'callable';
    const NATIVE_RESOURCE = 'resource';

    /** Composites */
    const NATIVE_OBJECT_NUMBER_STRING_BOOLEAN = 'object_number_string_boolean';
    const NATIVE_NUMBER = 'number';
    const NATIVE_NUMERIC = 'numeric';
    const NATIVE_SCALAR = 'scalar';
    const NATIVE_GENERIC_ARRAY_KEY = 'generic_array_key';
    const NATIVE_GENERIC_ARRAY_VALUE = 'generic_array_value';

    const LOOKUP_NO_CACHE   = 1;
    const LOOKUP_BOTH       = 2;
    const LOOKUP_CACHE_ONLY = 3;

    private $typeProvider;
    private $typeParser;

    private $nativeTypes;

    /**
     * Specialized array type.
     *
     * This is for more specialized array types where we know the type of the
     * elements, or of the keys.
     */
    private $arrayTypes = array();

    /**
     * Holds this types so that we do not need to create new objects for them.
     *
     * @var array<ThisType>
     */
    private $thisTypes = array();

    private $cachedClasses = array();
    private $cachedFunctions = array();
    private $cachedConstants = array();
    private $cachedImplementingClasses = array();

    private $namedTypes = array();

    private $classes = array();
    private $functions = array();
    private $constants = array();

    private $cacheDisabled = false;

    /**
     * Determines the lookup mode to use.
     *
     * This is mostly here for BC reasons.
     *
     * @param integer|boolean $mode
     * @return integer
     */
    private static function getLookupMode($mode)
    {
        if (false === $mode) {
            return self::LOOKUP_NO_CACHE;
        }

        if (true === $mode) {
            return self::LOOKUP_BOTH;
        }

        return $mode;
    }

    public function __construct(TypeProviderInterface $provider = null)
    {
        $this->typeProvider = $provider ?: new NullTypeProvider();
        $this->typeParser = new TypeParser($this);

        $this->nativeTypes = array(
            self::NATIVE_UNKNOWN => new UnknownType($this, false),
            self::NATIVE_UNKNOWN_CHECKED => new UnknownType($this, true),
            self::NATIVE_ALL => new AllType($this),
            self::NATIVE_BOOLEAN => new BooleanType($this),
            self::NATIVE_BOOLEAN_FALSE => new FalseType($this),
            self::NATIVE_DOUBLE => new DoubleType($this),
            self::NATIVE_INTEGER => new IntegerType($this),
            self::NATIVE_NONE => new NoType($this),
            self::NATIVE_NULL => new NullType($this),
            self::NATIVE_STRING => new StringType($this),
            self::NATIVE_OBJECT => new NoObjectType($this),
            self::NATIVE_CALLABLE => new CallableType($this),
            self::NATIVE_RESOURCE => new ResourceType($this),
        );

        // This needs to be separate since it creates a union of integer|string for the
        // key type. Both of these types are otherwise not available.
        $this->nativeTypes[self::NATIVE_GENERIC_ARRAY_KEY] = $this->createUnionType(array('integer', 'string'));
        $this->nativeTypes[self::NATIVE_GENERIC_ARRAY_VALUE] = new UnknownType($this);
        $this->nativeTypes[self::NATIVE_ARRAY] = new ArrayType($this);

        // create composite types (needs to be separate from the map above)
        $this->nativeTypes = array_merge($this->nativeTypes, array(
            self::NATIVE_OBJECT_NUMBER_STRING_BOOLEAN => $this->createUnionType(array('object', 'double', 'integer', 'string', 'boolean')),
            self::NATIVE_NUMBER => $this->createUnionType(array('integer', 'double')),
            self::NATIVE_NUMERIC => $this->createUnionType(array('integer', 'double', 'string')),
            self::NATIVE_SCALAR => $this->createUnionType(array('integer', 'double', 'string', 'boolean')),
        ));
    }

    /**
     * Disables the cache.
     *
     * This prevents the registry from loading anything from the type provider,
     * but will instead force it to generate placeholders where possible.
     *
     * @param boolean $bool
     */
    public function setCacheDisabled($bool)
    {
        $this->cacheDisabled = (boolean) $bool;
    }

    public function setRootPackageVersion(\Scrutinizer\PhpAnalyzer\Model\PackageVersion $packageVersion)
    {
        $versions = array($packageVersion);

        $addDeps = function(\Scrutinizer\PhpAnalyzer\Model\PackageVersion $v) use (&$versions, &$addDeps) {
            foreach ($v->getDependencies() as $dep) {
                if (in_array($dep, $versions, true)) {
                    continue;
                }

                $versions[] = $dep;
                $addDeps($dep);
            }
        };
        $addDeps($packageVersion);

        $this->setPackageVersions($versions);
    }

    public function setPackageVersions(array $packageVersions)
    {
        if (!$this->typeProvider instanceof PackageAwareTypeProviderInterface) {
            return;
        }

        // TODO: Clear cached classes/functions/constants. Right now, we consider
        //       the registry to not be re-usable.
        $this->typeProvider->setPackageVersions($packageVersions);
    }

    public function getTypeProvider()
    {
        return $this->typeProvider;
    }

    public function registerClass(MethodContainer $class)
    {
        $class->setTypeRegistry($this);
        $this->classes[strtolower($class->getName())] = $class;
    }

    public function registerConstant(GlobalConstant $constant)
    {
        $this->constants[$constant->getName()] = $constant;
    }

    public function hasConstant($name, $cacheLookup = true)
    {
        return null !== $this->getConstant($name, $cacheLookup);
    }

    public function getConstant($name, $cacheLookup = true)
    {
        if (isset($this->constants[$name])) {
            return $this->constants[$name];
        }

        if (!$cacheLookup) {
            return null;
        }

        if (isset($this->cachedConstants[$name])) {
            if (false === $this->cachedConstants[$name]) {
                return null;
            }

            return $this->cachedConstants[$name];
        }

        if ($this->cacheDisabled) {
            return null;
        }

        $this->tryLoadingConstant($name);

        return false === $this->cachedConstants[$name] ? null : $this->cachedConstants[$name];
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function registerFunction(GlobalFunction $function)
    {
        $this->functions[strtolower($function->getName())] = $function;
    }

    public function hasFunction($name, $cacheLookup = true)
    {
        return null !== $this->getFunction($name, $cacheLookup);
    }

    /**
     * Returns an array of classes that implement the given interface.
     *
     * @param string|InterfaceC $name
     *
     * @return array<Clazz>
     */
    public function getImplementingClasses($interfaceOrName)
    {
        $name = $interfaceOrName instanceof \Scrutinizer\PhpAnalyzer\Model\InterfaceC
                    ? $interfaceOrName->getName() : $interfaceOrName;

        if ( ! isset($this->cachedImplementingClasses[$name])) {


            $this->cachedImplementingClasses[$name] = array_merge(
                $this->findImplementingClasses($name),
                $this->typeProvider->getImplementingClasses($name));
            $this->resolveNamedTypes();
        }

        return $this->cachedImplementingClasses[$name];
    }

    /**
     * @param \PHPParser_Node_Stmt_Function|\PHPParser_Node_Stmt_ClassMethod $node
     * @param integer $lookupMode
     * @return GlobalFunction|null
     */
    public function getFunctionByNode(\PHPParser_Node $node, $lookupMode = self::LOOKUP_BOTH)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Function) {
            return $this->getFunction(implode("\\", $node->namespacedName->parts), $lookupMode);
        } else if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $classNode = $node->getAttribute('parent')->getAttribute('parent');
            $class = $this->getClassByNode($classNode, $lookupMode);
            $method = $class->getMethod($node->name);

            return $method;
        }

        throw new \LogicException('The previous ifs were exhaustive.');
    }

    /**
     * @param string $name
     * @param integer $lookupMode
     * @return GlobalFunction|null
     */
    public function getFunction($name, $lookupMode = self::LOOKUP_BOTH)
    {
        $name = strtolower($name);
        $lookupMode = self::getLookupMode($lookupMode);

        // PHP does check for the function name in the global namespace if the
        // namespaced function is not available. This emulates this behavior.
        $globalFunction = (false !== $pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : null;

        if (self::LOOKUP_CACHE_ONLY !== $lookupMode && isset($this->functions[$name])) {
            return $this->functions[$name];
        }

        if (self::LOOKUP_NO_CACHE === $lookupMode) {
            if ($globalFunction) {
                return $this->getFunction($globalFunction, $lookupMode);
            }

            return null;
        }

        if (isset($this->cachedFunctions[$name])) {
            if (false === $this->cachedFunctions[$name]) {
                if ($globalFunction) {
                    return $this->getFunction($globalFunction, $lookupMode);
                }

                return null;
            }

            return $this->cachedFunctions[$name];
        }

        if ($this->cacheDisabled) {
            return null;
        }

        $this->tryLoadingFunction($name);
        if (false === $this->cachedFunctions[$name]) {
            if ($globalFunction) {
                return $this->getFunction($globalFunction, $lookupMode);
            }

            return null;
        }

        return $this->cachedFunctions[$name];
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Returns an ARRAY type with the given element/key type combination.
     *
     * This function caches created types, and does only create new instances
     * when necessary.
     *
     * @param PhpType $elementType
     * @param PhpType $keyType
     * @param array<integer|string,PhpType> $itemTypes
     *
     * @return ArrayType
     */
    public function getArrayType(PhpType $elementType = null, PhpType $keyType = null, array $itemTypes = array())
    {
        if (empty($itemTypes)) {
            if (null === $elementType && null === $keyType) {
                return $this->nativeTypes[self::NATIVE_ARRAY];
            }

            foreach ($this->arrayTypes as $type) {
                if ($type->getElementType() === $elementType
                        && $type->getKeyType() === $keyType) {
                    return $type;
                }
            }

            return $this->arrayTypes[] = new ArrayType($this, $elementType, $keyType);
        }

        // If we have item types, we do not safe the array type in the global array
        // as we always have to create new instances anyway.
        return new ArrayType($this, $elementType, $keyType, $itemTypes);
    }

    public function getThisType(PhpType $objType)
    {
        switch (true) {
            case $objType instanceof ProxyObjectType:
                $className = $objType->getReferenceName();
                break;

            case $objType instanceof MethodContainer:
                $className = $objType->getName();
                break;

            default:
                throw new \LogicException(sprintf('The previous CASES were exhaustive. Unknown type "%s".', get_class($objType)));
        }

        $loweredClassName = strtolower($className);
        if ( ! isset($this->thisTypes[$loweredClassName])) {
            $this->thisTypes[$loweredClassName] = new ThisType($this, $this->getClassOrCreate($className));
        }

        return $this->thisTypes[$loweredClassName];
    }

    public function createNullableType(PhpType $type)
    {
        return $this->createUnionType(array($type, $this->getNativeType('null')));
    }

    /**
     * Creates a new union with the given types.
     *
     * If strings are passed, they are assumed to be native types.
     *
     * @param array $types
     *
     * @return UnionType
     */
    public function createUnionType(array $types)
    {
        $builder = new UnionTypeBuilder($this);
        foreach ($types as $type) {
            if ( ! $type instanceof PhpType) {
                $nativeType = $this->getNativeType($type);

                if (null === $nativeType) {
                    throw new \InvalidArgumentException(sprintf('There is no native type named "%s".', $type));
                }

                $type = $nativeType;
            }

            assert($type instanceof PhpType);

            $builder->addAlternate($type);
        }

        return $builder->build();
    }

    /**
     * Creates a type which consists of all types except the ones that were
     * specifically excluded.
     *
     * @param array $excludedTypes
     * @throws \LogicException
     * @return PhpType
     */
    public function createAllTypeExcept(array $excludedTypes)
    {
        $allTypes = self::$allTypes;
        foreach ($excludedTypes as $excludedType) {
            if (false === $index = array_search($excludedType, $allTypes, true)) {
                throw new \LogicException(sprintf('Could not find "%s" in all types.', $excludedType));
            }

            unset($allTypes[$index]);
        }

        return $this->createUnionType($allTypes);
    }

    /**
     * Returns a simple, native type.
     *
     * @param string $name
     *
     * @return null|PhpType
     */
    public function getNativeType($name)
    {
        return isset($this->nativeTypes[$name]) ? $this->nativeTypes[$name] : null;
    }

    public function hasClass($name, $cacheLookup = true)
    {
        return null !== $this->getClass($name, $cacheLookup);
    }

    public function getClassByNode(\PHPParser_Node $node, $lookupMode = self::LOOKUP_BOTH)
    {
        if (!$node instanceof \PHPParser_Node_Stmt_Class
                && !$node instanceof \PHPParser_Node_Stmt_Interface
                && !$node instanceof \PHPParser_Node_Stmt_Trait) {
            throw new \InvalidArgumentException('The node class "'.get_class($node).'" is not permissible.');
        }

        return $this->getClass(implode("\\", $node->namespacedName->parts), $lookupMode);
    }

    public function getFetchedPropertyByNode(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if ((null === $type = $node->class->getAttribute('type'))
                        || ! is_string($node->name)
                        || (null === $objType = $type->toMaybeObjectType())
                        || ( ! $objType->isClass() && ! $objType->isTrait())
                        || ! $objType->hasProperty($node->name)) {
                    return null;
                }

                return $objType->getProperty($node->name);

            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
                if ((null === $type = $node->var->getAttribute('type'))
                        || ! is_string($node->name)
                        || (null === $objType = $type->toMaybeObjectType())
                        || ( ! $objType->isClass() && ! $objType->isTrait())
                        || ! $objType->hasProperty($node->name)) {
                    return null;
                }

                return $objType->getProperty($node->name);

            default:
                throw new \LogicException(sprintf('The node "%s" is not resolvable to a fetched property.', get_class($node)));
        }
    }

    /**
     * @param \PHPParser_Node $node
     * @param int $lookupMode
     *
     * @return \Scrutinizer\PhpAnalyzer\Model\GlobalFunction|\Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface|null
     */
    public function getCalledFunctionByNode(\PHPParser_Node $node, $lookupMode = self::LOOKUP_BOTH)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_FuncCall:
                if ( ! $node->name instanceof \PHPParser_Node_Name) {
                    return null;
                }

                return $this->getFunction(implode("\\", $node->name->parts));

            case $node instanceof \PHPParser_Node_Expr_MethodCall:
                if ( ! is_string($node->name)) {
                    return null;
                }

                if (null === $objType = $node->var->getAttribute('type')) {
                    return null;
                }

                if (null === $objType = $objType->toMaybeObjectType()) {
                    return null;
                }

                return $objType->getMethod($node->name);

            case $node instanceof \PHPParser_Node_Expr_StaticCall:
                if ( ! is_string($node->name)) {
                    return null;
                }

                if (null === $objType = $node->class->getAttribute('type')) {
                    return null;
                }

                if (null === $objType = $objType->toMaybeObjectType()) {
                    return null;
                }

                return $objType->getMethod($node->name);

            case $node instanceof \PHPParser_Node_Expr_New:
                if (null === $objType = $node->getAttribute('type')) {
                    return null;
                }

                if (null === $objType = $objType->toMaybeObjectType()) {
                    return null;
                }

                if ($objType->hasMethod('__construct')) {
                    return $objType->getMethod('__construct');
                }

                // PHP4-style constructors are supported in non-namespaced code.
                return false === strpos($objType->getName(), '\\')
                            ? $objType->getMethod($objType->getName()) : null;

            default:
                throw new \InvalidArgumentException('The node class "'.get_class($node).'" is not resolvable to a function/method.');
        }
    }

    /**
     * Returns the given class if it exists.
     *
     * @param string $name
     * @param integer $cacheLookup
     *
     * @return MethodContainer|null
     */
    public function getClass($name, $lookupMode = self::LOOKUP_BOTH)
    {
        $lowerName = strtolower($name);
        $lookupMode = self::getLookupMode($lookupMode);

        if (self::LOOKUP_CACHE_ONLY !== $lookupMode && isset($this->classes[$lowerName])) {
            return $this->classes[$lowerName];
        }

        if (self::LOOKUP_NO_CACHE === $lookupMode) {
            return null;
        }

        if (isset($this->cachedClasses[$lowerName])) {
            if (false === $this->cachedClasses[$lowerName]) {
                return null;
            }

            return $this->cachedClasses[$lowerName];
        }

        // If the cache is disabled, do not try to load anything from it. If we
        // have already loaded something, we will however allow this to be returned.
        if ($this->cacheDisabled) {
            return null;
        }

        $this->tryLoadingClass($lowerName);

        return false === $this->cachedClasses[$lowerName] ? null : $this->cachedClasses[$lowerName];
    }

    public function getClassOrCreate($name, $lookupMode = self::LOOKUP_BOTH)
    {
        if ($class = $this->getClass($name, $lookupMode)) {
            return $class;
        }

        return $this->getNamedType($name);
    }

    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Returns the class a method was called on.
     *
     * @param \PHPParser_Node $node
     *
     * @return null|PhpType
     */
    public function getCalledClassByNode(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
                if (null === $objType = $node->var->getAttribute('type')) {
                    return null;
                }

                return $objType->restrictByNotNull()->toMaybeObjectType();

            case $node instanceof \PHPParser_Node_Expr_StaticCall:
                if (null === $objType = $node->class->getAttribute('type')) {
                    return null;
                }

                return $objType->restrictByNotNull()->toMaybeObjectType();

            case $node instanceof \PHPParser_Node_Expr_New:
                if (null === $objType = $node->getAttribute('type')) {
                    return null;
                }

                return $objType->restrictByNotNull()->toMaybeObjectType();

            default:
                throw new \InvalidArgumentException('The node class "'.get_class($node).'" is not resolvable to a function/method.');
        }
    }

    /**
     * Resolves a type to an instance of PhpType.
     *
     * @param string|PhpType $type
     *
     * @return PhpType
     */
    public function resolveType($type, $lookupMode = self::LOOKUP_BOTH)
    {
        if ($type instanceof PhpType) {
            return $type;
        }

        return $this->typeParser->parseType($type, $lookupMode);
    }

    /**
     * Tries to resolve all registered named types to actual classes.
     *
     * This is typically called after all types of a package have been scanned
     * in order to populate the AST with correct type information. Also, it is
     * called after new type information has been loaded from the database.
     */
    public function resolveNamedTypes()
    {
        $toLookup = array();
        foreach ($this->namedTypes as $className => $namedType) {
            assert($namedType instanceof NamedType);

            if ($namedType->isResolved()) {
                unset($this->namedTypes[$className]);
                continue;
            }

            if (null !== $class = $this->getClass($className, self::LOOKUP_NO_CACHE)) {
                $namedType->setReferencedType($class);
                unset($this->namedTypes[$className]);
                continue;
            }

            $toLookup[$className] = true;
        }

        $lookedUp = array();
        while ($toLookup) {
            $classes = $this->typeProvider->loadClasses(array_keys($toLookup));
            foreach ($classes as $className => $class) {
                $this->namedTypes[$className]->setReferencedType($class);
                unset($this->namedTypes[$className]);
            }

            // The loadClasses call from above might have resulted in new NamedTypes
            // which we now need to also lookup.
            $lookedUp = array_merge($lookedUp, $toLookup);
            $toLookup = array_diff_key($this->namedTypes, $lookedUp);
        }
    }

    private function findImplementingClasses($name)
    {
        $classes = array();
        foreach ($this->classes as $class) {
            if ( ! $class instanceof \Scrutinizer\PhpAnalyzer\Model\Clazz) {
                continue;
            }

            if ( ! $class->isImplementing($name)) {
                continue;
            }

            $classes[] = $class;
        }

        return $classes;
    }

    private function getNamedType($name)
    {
        $lowerName = strtolower($name);
        if (isset($this->namedTypes[$lowerName])) {
            return $this->namedTypes[$lowerName];
        }

        return $this->namedTypes[$lowerName] = new NamedType($this, $name);
    }

    private function tryLoadingClass($name)
    {
        if ($class = $this->typeProvider->loadClass($name)) {
            $class->setTypeRegistry($this);
            $this->cachedClasses[$name] = $class;
            $this->resolveNamedTypes();
        } else {
            $this->cachedClasses[$name] = false;
        }
    }

    private function tryLoadingFunction($name)
    {
        if ($function = $this->typeProvider->loadFunction($name)) {
            $this->cachedFunctions[$name] = $function;
            $this->resolveNamedTypes();
        } else {
            $this->cachedFunctions[$name] = false;
        }
    }

    private function tryLoadingConstant($name)
    {
        if ($constant = $this->typeProvider->loadConstant($name)) {
            $this->cachedConstants[$name] = $constant;
            $this->resolveNamedTypes();
        } else {
            $this->cachedConstants[$name] = false;
        }
    }
}