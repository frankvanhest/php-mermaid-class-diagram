<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Connector;

use PhpParser\Node;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Interface_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Node as ClassDiagramNode;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Nodes;

class RealizationConnector extends Connector
{
    public function connect(Nodes $nodes): void
    {
        $node = $nodes->findByFqn($this->nodeFqn);

        foreach ($this->toConnectNodeFqns as $toConnectNodeFqn) {
            $parts = explode('\\', $toConnectNodeFqn);
            $className = end($parts);
            $namespace = implode('\\', array_slice($parts, 0, -1));
            $node->implements(
                $nodes->findByFqn($toConnectNodeFqn) ?? new Interface_($className, $namespace)
            );
        }
    }

    public static function parse(
        Node\Stmt\Interface_|Node\Stmt\Class_ $classLike,
        ClassDiagramNode                      $classDiagramNode,
    ): self
    {
        $implementsNodeNames = [];

        if (property_exists($classLike, 'implements') && $classLike->implements !== []) {
            $implementsNodeNames = array_map('strval', $classLike->implements);
        }

        return new RealizationConnector($classDiagramNode->nodeFqn(), $implementsNodeNames);
    }
}
