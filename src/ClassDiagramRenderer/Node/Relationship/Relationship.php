<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship;

use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Node;

abstract class Relationship
{
    protected const FORMAT = "%s %s %s: %s";
    public function __construct(protected Node $from, protected Node $to)
    {
    }

    abstract protected function render(): string;

    public static function sortRelationships(array &$relationships): void
    {
        usort($relationships, function (Relationship $a, Relationship $b) {
            $aKey = $a->from->nodeFqn() . ' ' . $a->to->nodeFqn();
            $bKey = $b->from->nodeFqn() . ' ' . $b->to->nodeFqn();
            return strcmp($aKey, $bKey);
        });
    }
}
