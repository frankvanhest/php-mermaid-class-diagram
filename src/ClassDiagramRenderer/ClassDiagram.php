<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer;

use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Node;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Nodes;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship\Relationship;

class ClassDiagram
{
    private Nodes $nodes;

    /**
     * @var Relationship[]
     */
    private array $relationships = [];

    public function __construct()
    {
        $this->nodes = new Nodes();
    }

    public function addNode(Node $node): self
    {
        $this->nodes->add($node);

        return $this;
    }

    public function addRelationships(Relationship ...$relationships): self
    {
        $this->relationships = [...$this->relationships, ...$relationships];

        return $this;
    }

    public function render(): string
    {
        $this->nodes->sort();
        Relationship::sortRelationships($this->relationships);

        $output = "classDiagram\n";

        foreach ($this->nodes->allNodesSortedByNamespace() as $namespace => $nodes) {
            $output .= "    namespace $namespace {\n";
            foreach ($nodes->getAllNodes() as $node) {
                $output .= "    " . $node->render() . "\n";
            }
            $output .= "    }\n";
        }
        $output .= "\n";

        foreach ($this->relationships as $relationship) {
            $output .= "    " . $relationship->render() . "\n";
        }

        return $output;
    }
}
