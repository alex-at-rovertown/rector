<?php

declare (strict_types=1);
namespace RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration;

use RectorPrefix20220202\Idiosyncratic\EditorConfig\Exception\InvalidValue;
use RectorPrefix20220202\PHPUnit\Framework\TestCase;
use RuntimeException;
class IndentSizeTest extends \RectorPrefix20220202\PHPUnit\Framework\TestCase
{
    public function testValidValues()
    {
        $declaration = new \RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration\IndentSize('tab');
        $this->assertEquals('indent_size=tab', (string) $declaration);
        $declaration = new \RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration\IndentSize('4');
        $this->assertEquals('indent_size=4', (string) $declaration);
        $this->assertSame(4, $declaration->getValue());
    }
    public function testInvalidValueType()
    {
        $this->expectException(\RectorPrefix20220202\Idiosyncratic\EditorConfig\Exception\InvalidValue::class);
        $declaration = new \RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration\IndentSize('true');
    }
    public function testInvalidValueValue()
    {
        $this->expectException(\RectorPrefix20220202\Idiosyncratic\EditorConfig\Exception\InvalidValue::class);
        $declaration = new \RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration\IndentSize('four');
    }
    public function testInvalidNegativeIntegerValue()
    {
        $this->expectException(\RectorPrefix20220202\Idiosyncratic\EditorConfig\Exception\InvalidValue::class);
        $declaration = new \RectorPrefix20220202\Idiosyncratic\EditorConfig\Declaration\IndentSize('-1');
    }
}
