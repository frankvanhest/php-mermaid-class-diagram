<?php
declare(strict_types=1);

namespace Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Connector;

use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Nodes;

abstract class Connector
{
    /**
     * @param string   $nodeFqn
     * @param string[] $toConnectNodeFqns
     */
    public function __construct(protected string $nodeFqn, protected array $toConnectNodeFqns)
    {
    }

    abstract public function connect(Nodes $nodes): void;
}
