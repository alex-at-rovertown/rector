<?php

declare (strict_types=1);
namespace Rector\BetterPhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use Rector\BetterPhpDocParser\Contract\BasePhpDocNodeVisitorInterface;
use Rector\BetterPhpDocParser\DataProvider\CurrentTokenIteratorProvider;
use Rector\BetterPhpDocParser\ValueObject\Parser\BetterTokenIterator;
use RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CloningPhpDocNodeVisitor;
use RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeVisitor\ParentConnectingPhpDocNodeVisitor;
/**
 * @see \Rector\Tests\BetterPhpDocParser\PhpDocNodeMapperTest
 */
final class PhpDocNodeMapper
{
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\DataProvider\CurrentTokenIteratorProvider
     */
    private $currentTokenIteratorProvider;
    /**
     * @readonly
     * @var \Symplify\SimplePhpDocParser\PhpDocNodeVisitor\ParentConnectingPhpDocNodeVisitor
     */
    private $parentConnectingPhpDocNodeVisitor;
    /**
     * @readonly
     * @var \Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CloningPhpDocNodeVisitor
     */
    private $cloningPhpDocNodeVisitor;
    /**
     * @var BasePhpDocNodeVisitorInterface[]
     * @readonly
     */
    private $phpDocNodeVisitors;
    /**
     * @param BasePhpDocNodeVisitorInterface[] $phpDocNodeVisitors
     */
    public function __construct(\Rector\BetterPhpDocParser\DataProvider\CurrentTokenIteratorProvider $currentTokenIteratorProvider, \RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeVisitor\ParentConnectingPhpDocNodeVisitor $parentConnectingPhpDocNodeVisitor, \RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CloningPhpDocNodeVisitor $cloningPhpDocNodeVisitor, array $phpDocNodeVisitors)
    {
        $this->currentTokenIteratorProvider = $currentTokenIteratorProvider;
        $this->parentConnectingPhpDocNodeVisitor = $parentConnectingPhpDocNodeVisitor;
        $this->cloningPhpDocNodeVisitor = $cloningPhpDocNodeVisitor;
        $this->phpDocNodeVisitors = $phpDocNodeVisitors;
    }
    public function transform(\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode $phpDocNode, \Rector\BetterPhpDocParser\ValueObject\Parser\BetterTokenIterator $betterTokenIterator) : void
    {
        $this->currentTokenIteratorProvider->setBetterTokenIterator($betterTokenIterator);
        $parentPhpDocNodeTraverser = new \RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeTraverser();
        $parentPhpDocNodeTraverser->addPhpDocNodeVisitor($this->parentConnectingPhpDocNodeVisitor);
        $parentPhpDocNodeTraverser->traverse($phpDocNode);
        $cloningPhpDocNodeTraverser = new \RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeTraverser();
        $cloningPhpDocNodeTraverser->addPhpDocNodeVisitor($this->cloningPhpDocNodeVisitor);
        $cloningPhpDocNodeTraverser->traverse($phpDocNode);
        $phpDocNodeTraverser = new \RectorPrefix20220202\Symplify\SimplePhpDocParser\PhpDocNodeTraverser();
        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeTraverser->addPhpDocNodeVisitor($phpDocNodeVisitor);
        }
        $phpDocNodeTraverser->traverse($phpDocNode);
    }
}
