<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace RectorPrefix20220202\Nette\Neon\Node;

use RectorPrefix20220202\Nette\Neon\Entity;
use RectorPrefix20220202\Nette\Neon\Node;
/** @internal */
final class EntityNode extends \RectorPrefix20220202\Nette\Neon\Node
{
    /** @var Node */
    public $value;
    /** @var ArrayItemNode[] */
    public $attributes = [];
    public function __construct(\RectorPrefix20220202\Nette\Neon\Node $value, array $attributes, int $startPos = null, int $endPos = null)
    {
        $this->value = $value;
        $this->attributes = $attributes;
        $this->startPos = $startPos;
        $this->endPos = $endPos ?? $startPos;
    }
    public function toValue() : \RectorPrefix20220202\Nette\Neon\Entity
    {
        return new \RectorPrefix20220202\Nette\Neon\Entity($this->value->toValue(), \RectorPrefix20220202\Nette\Neon\Node\ArrayItemNode::itemsToArray($this->attributes));
    }
    public function toString() : string
    {
        return $this->value->toString() . '(' . ($this->attributes ? \RectorPrefix20220202\Nette\Neon\Node\ArrayItemNode::itemsToInlineString($this->attributes) : '') . ')';
    }
    public function getSubNodes() : array
    {
        $res = [&$this->value];
        foreach ($this->attributes as &$item) {
            $res[] =& $item;
        }
        return $res;
    }
}
