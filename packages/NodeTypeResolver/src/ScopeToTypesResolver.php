<?php declare(strict_types=1);

namespace Rector\NodeTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;
use Rector\Node\Attribute;

final class ScopeToTypesResolver
{
    /**
     * @var NodeTypeResolver
     */
    private $nodeTypeResolver;

    public function __construct(NodeTypeResolver $nodeTypeResolver)
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    /**
     * @return string[]
     */
    public function resolveScopeToTypes(Node $node): array
    {
        // @todo check via exception
        /** @var Scope $nodeScope */
        $nodeScope = $node->getAttribute(Attribute::SCOPE);

        // awww :(
        $types = [];
        if ($node instanceof Variable && $node->name === 'this') {
            $types[] = $nodeScope->getClassReflection()->getName();
            $types = array_merge($types, $nodeScope->getClassReflection()->getParentClassesNames());

            foreach ($nodeScope->getClassReflection()->getInterfaces() as $classReflection) {
                $types[] = $classReflection->getName();
            }

            return $types;
        }

        if (! $node instanceof Expr) {
            return $this->resolveNonExprNodeToTypes($node);
        }

        $types = [];

        $type = $nodeScope->getType($node);
        if ($type instanceof ObjectType) {
            $types[] = $type->getClassName();
        }

        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $type) {
                if ($type instanceof ObjectType) {
                    $types[] = $type->getClassName();
                }
            }
        }

        if ($type instanceof IntersectionType) {
            foreach ($type->getTypes() as $type) {
                if ($type instanceof ObjectType) {
                    $types[] = $type->getClassName();
                }
            }
        }

        return $types;
    }

    /**
     * @return string[]
     */
    private function resolveNonExprNodeToTypes(Node $node): array
    {
        return $this->nodeTypeResolver->resolve($node);
    }
}
