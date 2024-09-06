<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node;

class Nodes
{
    /**
     * @var Node[]
     */
    private array $nodes;

    public function __construct()
    {
        $this->nodes = [];
    }

    public static function empty(): self
    {
        return new self();
    }

    public function add(Node $node): self
    {
        $this->nodes[$node->nodeFqn()] = $node;
        return $this;
    }

    public function findByFqn(string $nodeFqn): ?Node
    {
        return $this->nodes[$nodeFqn] ?? null;
    }

    /**
     * @return Node[]
     */
    public function getAllNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return array<string, Nodes>
     */
    public function allNodesSortedByNamespace(): array
    {
        $sortedNodes = [];
        foreach ($this->nodes as $node) {
            if (!isset($sortedNodes[$node->nodeNamespace()])) {
                $sortedNodes[$node->nodeNamespace()] = new Nodes();
            }
            $sortedNodes[$node->nodeNamespace()]->add($node);
        }

        return $sortedNodes;
    }

    public function sort(): void
    {
        Node::sortNodes($this->nodes);
    }
}
