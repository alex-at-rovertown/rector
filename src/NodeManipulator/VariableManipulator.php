<?php

declare (strict_types=1);
namespace Rector\Core\NodeManipulator;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Comparing\NodeComparator;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\ReadWrite\Guard\VariableToConstantGuard;
use RectorPrefix20220202\Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
final class VariableManipulator
{
    /**
     * @readonly
     * @var \Rector\Core\NodeManipulator\ArrayManipulator
     */
    private $arrayManipulator;
    /**
     * @readonly
     * @var \Rector\Core\NodeManipulator\AssignManipulator
     */
    private $assignManipulator;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\BetterNodeFinder
     */
    private $betterNodeFinder;
    /**
     * @readonly
     * @var \Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser
     */
    private $simpleCallableNodeTraverser;
    /**
     * @readonly
     * @var \Rector\NodeNameResolver\NodeNameResolver
     */
    private $nodeNameResolver;
    /**
     * @readonly
     * @var \Rector\ReadWrite\Guard\VariableToConstantGuard
     */
    private $variableToConstantGuard;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Comparing\NodeComparator
     */
    private $nodeComparator;
    public function __construct(\Rector\Core\NodeManipulator\ArrayManipulator $arrayManipulator, \Rector\Core\NodeManipulator\AssignManipulator $assignManipulator, \Rector\Core\PhpParser\Node\BetterNodeFinder $betterNodeFinder, \RectorPrefix20220202\Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser $simpleCallableNodeTraverser, \Rector\NodeNameResolver\NodeNameResolver $nodeNameResolver, \Rector\ReadWrite\Guard\VariableToConstantGuard $variableToConstantGuard, \Rector\Core\PhpParser\Comparing\NodeComparator $nodeComparator)
    {
        $this->arrayManipulator = $arrayManipulator;
        $this->assignManipulator = $assignManipulator;
        $this->betterNodeFinder = $betterNodeFinder;
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
        $this->nodeNameResolver = $nodeNameResolver;
        $this->variableToConstantGuard = $variableToConstantGuard;
        $this->nodeComparator = $nodeComparator;
    }
    /**
     * @return Assign[]
     */
    public function collectScalarOrArrayAssignsOfVariable(\PhpParser\Node\Stmt\ClassMethod $classMethod) : array
    {
        $assignsOfArrayToVariable = [];
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable((array) $classMethod->getStmts(), function (\PhpParser\Node $node) use(&$assignsOfArrayToVariable) {
            if (!$node instanceof \PhpParser\Node\Expr\Assign) {
                return null;
            }
            if (!$node->var instanceof \PhpParser\Node\Expr\Variable) {
                return null;
            }
            if (!$node->expr instanceof \PhpParser\Node\Expr\Array_ && !$node->expr instanceof \PhpParser\Node\Scalar) {
                return null;
            }
            if ($this->hasEncapsedStringPart($node->expr)) {
                return null;
            }
            if ($node->expr instanceof \PhpParser\Node\Expr\Array_ && !$this->arrayManipulator->isArrayOnlyScalarValues($node->expr)) {
                return null;
            }
            if ($this->isTestCaseExpectedVariable($node->var)) {
                return null;
            }
            $assignsOfArrayToVariable[] = $node;
        });
        return $assignsOfArrayToVariable;
    }
    /**
     * @param Assign[] $assignsOfArrayToVariable
     * @return Assign[]
     */
    public function filterOutChangedVariables(array $assignsOfArrayToVariable, \PhpParser\Node\Stmt\ClassMethod $classMethod) : array
    {
        return \array_filter($assignsOfArrayToVariable, function (\PhpParser\Node\Expr\Assign $assign) use($classMethod) : bool {
            return $this->isReadOnlyVariable($classMethod, $assign);
        });
    }
    private function hasEncapsedStringPart(\PhpParser\Node\Expr $expr) : bool
    {
        return (bool) $this->betterNodeFinder->findFirst($expr, function (\PhpParser\Node $subNode) : bool {
            return $subNode instanceof \PhpParser\Node\Scalar\Encapsed || $subNode instanceof \PhpParser\Node\Scalar\EncapsedStringPart;
        });
    }
    private function isTestCaseExpectedVariable(\PhpParser\Node\Expr\Variable $variable) : bool
    {
        $classLike = $this->betterNodeFinder->findParentType($variable, \PhpParser\Node\Stmt\ClassLike::class);
        if (!$classLike instanceof \PhpParser\Node\Stmt\ClassLike) {
            return \false;
        }
        $className = (string) $this->nodeNameResolver->getName($classLike);
        if (\substr_compare($className, 'Test', -\strlen('Test')) !== 0) {
            return \false;
        }
        return $this->nodeNameResolver->isName($variable, 'expect*');
    }
    /**
     * Inspiration
     * @see \Rector\Core\NodeManipulator\PropertyManipulator::isPropertyUsedInReadContext()
     */
    private function isReadOnlyVariable(\PhpParser\Node\Stmt\ClassMethod $classMethod, \PhpParser\Node\Expr\Assign $assign) : bool
    {
        if (!$assign->var instanceof \PhpParser\Node\Expr\Variable) {
            return \false;
        }
        $variable = $assign->var;
        $variableUsages = $this->collectVariableUsages($classMethod, $variable, $assign);
        foreach ($variableUsages as $variableUsage) {
            $parent = $variableUsage->getAttribute(\Rector\NodeTypeResolver\Node\AttributeKey::PARENT_NODE);
            if ($parent instanceof \PhpParser\Node\Arg && !$this->variableToConstantGuard->isReadArg($parent)) {
                return \false;
            }
            if (!$this->assignManipulator->isLeftPartOfAssign($variableUsage)) {
                continue;
            }
            return \false;
        }
        return \true;
    }
    /**
     * @return Variable[]
     */
    private function collectVariableUsages(\PhpParser\Node\Stmt\ClassMethod $classMethod, \PhpParser\Node\Expr\Variable $variable, \PhpParser\Node\Expr\Assign $assign) : array
    {
        return $this->betterNodeFinder->find((array) $classMethod->getStmts(), function (\PhpParser\Node $node) use($variable, $assign) : bool {
            if (!$node instanceof \PhpParser\Node\Expr\Variable) {
                return \false;
            }
            // skip initialization
            $parentNode = $node->getAttribute(\Rector\NodeTypeResolver\Node\AttributeKey::PARENT_NODE);
            if ($parentNode === $assign) {
                return \false;
            }
            return $this->nodeComparator->areNodesEqual($node, $variable);
        });
    }
}
