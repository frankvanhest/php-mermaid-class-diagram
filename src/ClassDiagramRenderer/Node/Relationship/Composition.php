<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship;

class Composition extends Relationship
{
    public function render(): string
    {
        return sprintf(self::FORMAT, $this->from->nodeFqn(), '*--', $this->to->nodeFqn(), 'composition');
    }
}
