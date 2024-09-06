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

class DependencyConnector extends Connector
{
    public function connect(Nodes $nodes): void
    {
        $node = $nodes->findByFqn($this->nodeFqn);

        foreach ($this->toConnectNodeFqns as $toConnectNodeFqn) {
            $parts = explode('\\', $toConnectNodeFqn);
            $className = end($parts);
            $namespace = implode('\\', array_slice($parts, 0, -1));
            $node->depend($nodes->findByFqn($toConnectNodeFqn) ?? new Class_($className, $namespace));
        }
    }

    public static function parse(
        NodeFinder                            $nodeFinder,
        Node\Stmt\Interface_|Node\Stmt\Class_ $classLike,
        ClassDiagramNode                      $classDiagramNode,
    ): self
    {
        $dependencyNodeNames = [];

        // from method parameters and return types
        foreach ($classLike->getMethods() as $method) {
            foreach ($method->getParams() as $param) {
                // If `visibility` is not specified, flags is 0
                if ($param->type instanceof Name && $param->flags === 0) {
                    $dependencyNodeNames[] = (string)$param->type;
                }
            }

            if ($method->returnType instanceof Name) {
                $parts                 = $method->returnType->getParts();
                $returnTypeName = end($parts);
                if ($returnTypeName !== 'self') {
                    $dependencyNodeNames[] = (string)$method->returnType;
                }
            }
        }

        foreach ($nodeFinder->findInstanceOf($classLike, Node\Expr\New_::class) as $newStmt) {
            assert($newStmt instanceof Node\Expr\New_);
            if ($newStmt->class instanceof Name) {
                $dependencyNodeNames[] = (string)$newStmt->class;
            }
        }

        // remove duplicates
        $dependencyNodeNames = array_unique($dependencyNodeNames);

        return new DependencyConnector($classDiagramNode->nodeFqn(), $dependencyNodeNames);
    }
}
