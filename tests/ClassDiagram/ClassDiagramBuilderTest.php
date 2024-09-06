<?php
declare(strict_types=1);

namespace Tasuku43\Tests\MermaidClassDiagram\ClassDiagram;

use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\ClassDiagram;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\ClassDiagramBuilder;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\AbstractClass_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Class_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Interface_;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\NodeParser;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship\Composition;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship\Dependency;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship\Inheritance;
use Tasuku43\MermaidClassDiagram\ClassDiagramRenderer\Node\Relationship\Realization;

class ClassDiagramBuilderTest extends TestCase
{
    public function testBuild_forDir(): void
    {
        $expectedDiagram = new ClassDiagram();
        $namespace = __NAMESPACE__ . '\\data';

        $someClassA        = new Class_('SomeClassA', $namespace);
        $someClassB        = new Class_('SomeClassB', $namespace);
        $someClassC        = new Class_('SomeClassC', $namespace);
        $someClassD        = new Class_('SomeClassD', $namespace);
        $someClassE        = new Class_('SomeClassE', $namespace);
        $someAbstractClass = new AbstractClass_('SomeAbstractClass', $namespace);
        $someInterface     = new Interface_('SomeInterface', $namespace);

        $someClassA->extends($someAbstractClass);
        $someClassA->composition($someClassB);
        $someClassA->composition($someClassC);
        $someAbstractClass->implements($someInterface);

        $expectedDiagram
            ->addNode($someAbstractClass)
            ->addNode($someClassA)
            ->addNode($someClassB)
            ->addNode($someClassC)
            ->addNode($someClassD)
            ->addNode($someClassE)
            ->addNode($someInterface);

        $expectedDiagram->addRelationships(new Realization($someAbstractClass, $someInterface))
            ->addRelationships(new Inheritance($someClassA, $someAbstractClass))
            ->addRelationships(new Composition($someClassA, $someClassB))
            ->addRelationships(new Composition($someClassA, $someClassC))
            ->addRelationships(new Dependency($someClassA, $someClassD))
            ->addRelationships(new Dependency($someClassE, $someClassB))
            ->addRelationships(new Dependency($someClassE, $someClassC))
            ->addRelationships(new Composition($someClassE, $someClassA));

        $builder = new ClassDiagramBuilder(new NodeParser(
            (new ParserFactory)->create(ParserFactory::PREFER_PHP7),
            new NodeFinder()
        ));

        self::assertSame($expectedDiagram->render(), $builder->build(__DIR__ . '/data/')->render());
    }

    public function testBuild_forFilePath(): void
    {
        $expectedDiagram = new ClassDiagram();
        $namespace = __NAMESPACE__ . '\\data';

        $someClass                = new Class_('SomeClassA', $namespace);
        $defaultCompositionClass1 = new Class_('SomeClassB', $namespace);
        $defaultCompositionClass2 = new Class_('SomeClassC', $namespace);
        $defaultDependencyClass = new Class_('SomeClassD', $namespace);
        $defaultExtendsClass      = new Class_('SomeAbstractClass', $namespace);
        $someClass->extends($defaultExtendsClass);
        $someClass->composition($defaultCompositionClass1);
        $someClass->composition($defaultCompositionClass2);

        $expectedDiagram
            ->addNode($someClass)
            ->addRelationships(new Inheritance($someClass, $defaultExtendsClass))
            ->addRelationships(new Composition($someClass, $defaultCompositionClass1))
            ->addRelationships(new Composition($someClass, $defaultCompositionClass2))
            ->addRelationships(new Dependency($someClass, $defaultDependencyClass));

        $builder = new ClassDiagramBuilder(new NodeParser(
            (new ParserFactory)->create(ParserFactory::PREFER_PHP7),
            new NodeFinder()
        ));

        self::assertEquals($expectedDiagram->render(), $builder->build(__DIR__ . '/data/SomeClassA.php')->render());
    }
}
