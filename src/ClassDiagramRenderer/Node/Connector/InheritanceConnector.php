<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Connector;

use PhpParser\Node;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Class_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Interface_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Node as ClassDiagramNode;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Nodes;

class InheritanceConnector extends Connector
{
    public function connect(Nodes $nodes): void
    {
        $node = $nodes->findByFqn($this->nodeFqn);

        foreach ($this->toConnectNodeFqns as $toConnectNodeFqn) {
            $node->extends(
                $nodes->findByFqn($toConnectNodeFqn) ?? $this->createDefaultExtendsNode($node, $toConnectNodeFqn)
            );
        }
    }

    private function createDefaultExtendsNode(ClassDiagramNode $extended, string $extendsNodeName): ClassDiagramNode
    {
        $parts = explode('\\', $extendsNodeName);
        $className = end($parts);
        $namespace = implode('\\', array_slice($parts, 0, -1));
        return match (true) {
            $extended instanceof Interface_ => new Interface_($className, $namespace),
            default => new Class_($className, $namespace),
        };
    }

    public static function parse(
        Node\Stmt\Interface_|Node\Stmt\Class_ $classLike,
        ClassDiagramNode                      $classDiagramNode,
    ): self
    {
        $extendsNodeNames = [];

        if ($classLike->extends !== null) {
            $extendsNodeNames = is_array($classLike->extends)
                ? array_map('strval', $classLike->extends)
                : [(string)$classLike->extends];
        }

        return new InheritanceConnector($classDiagramNode->nodeFqn(), $extendsNodeNames);
    }
}
