<?php

declare (strict_types=1);
namespace Rector\Nette\NeonParser;

use RectorPrefix20220202\Nette\Neon\Decoder;
use RectorPrefix20220202\Nette\Neon\Node;
final class NeonParser
{
    /**
     * @var \Nette\Neon\Decoder
     */
    private $decoder;
    public function __construct(\RectorPrefix20220202\Nette\Neon\Decoder $decoder)
    {
        $this->decoder = $decoder;
    }
    public function parseString(string $neonContent) : \RectorPrefix20220202\Nette\Neon\Node
    {
        return $this->decoder->parseToNode($neonContent);
    }
}
