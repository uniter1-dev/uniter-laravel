<?php

namespace Uniter1\UniterLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Uniter1\UniterRequester\Application\PhpParser\RequesterParser;

class LinesReplaceTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public function testFindNamespace(): void
    {
        $rp = new RequesterParser();
        $text = "\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12";
        $replaceOffsetLength = ['r1'=>[2, 2], 'r2'=>[5, 1], 'r3' =>[6, 0], 'r4' => [10, 4]];
        $replacers = ['r1'=>"21\n\n22\n23", 'r2'=>"\n51\n52\n", 'r3' =>'', 'r4' => "101\n102"];
        $replaced = $rp::multiplePositionsReplace($text, $replaceOffsetLength, $replacers);
        self::assertEquals($replaced, "\n1\n21\n\n22\n23\n4\n\n51\n52\n\n6\n7\n8\n9\n101\n102");
    }
}
