<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Connector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Class_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Node as ClassDiagramNode;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Nodes;

class CompositionConnector extends Connector
{
    public function connect(Nodes $nodes): void
    {
        $node = $nodes->findByFqn($this->nodeFqn);

        foreach ($this->toConnectNodeFqns as $toConnectNodeFqn) {
            $parts = explode('\\', $toConnectNodeFqn);
            $className = end($parts);
            $namespace = implode('\\', array_slice($parts, 0, -1));
            $node->composition($nodes->findByFqn($toConnectNodeFqn) ?? new Class_($className, $namespace));
        }
    }

    public static function parse(
        NodeFinder $nodeFinder,
        Node\Stmt\Interface_|Node\Stmt\Class_ $classLike,
        ClassDiagramNode                      $classDiagramNode,
    ): self
    {
        $propertyNodeNames = [];

        // from constructor
        $construct = $nodeFinder->findFirst($classLike, function (Node $node) {
            return $node instanceof ClassMethod && (string)$node->name === '__construct';
        });
        if ($construct !== null) {
            assert($construct instanceof ClassMethod);
            foreach (array_filter($construct->getParams(), fn(Node\Param $param) => $param->type instanceof Name) as $param) {
                assert($param instanceof Node\Param);

                // If `visibility` is not specified, flags is 0
                if ($param->flags !== 0) {
                    $propertyNodeNames[] = (string)$param->type;
                }
            }
        }

        // from properties
        $propertyNodeNames = array_merge(
            array_map(
                static fn(Property $property): string => (string)$property->type,
                array_filter(
                    $classLike->getProperties(),
                    fn(Property $property) => $property->type instanceof FullyQualified,
                ),
            ),
            $propertyNodeNames,
        );

        return new CompositionConnector($classDiagramNode->nodeFqn(), $propertyNodeNames);
    }
}
