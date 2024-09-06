<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship;

class Inheritance extends Relationship
{
    public function render(): string
    {
        return sprintf(self::FORMAT, $this->to->nodeFqn(), '<|--', $this->from->nodeFqn(), 'inheritance');
    }
}
