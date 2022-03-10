<?php

namespace PhpUniter\PackageLaravel\Tests\Unit\Application\Obfuscator\Entity;

use PHPUnit\Framework\TestCase;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;

class ObfuscatedClassTest extends TestCase
{
    /**
     * @dataProvider getObfuscatedFileBody
     */
    public function testGetObfuscated($input, $expected)
    {
        $localFile = new LocalFile('', $input);

        $keys = 'o_name';
        $keyGenerator = function () use ($keys) {
            static $i = 0;

            return $keys.($i++);
        };

        $obfuscatedClassObject = new ObfuscatedClass(
            $localFile,
            $keyGenerator
        );
        $obfuscated = $obfuscatedClassObject->getObfuscatedFileBody();
        $this->assertEquals(trim($expected), trim($obfuscated));

        $deObfuscated = $obfuscatedClassObject->deObfuscate($obfuscated);
        $this->assertEquals($input, $deObfuscated);
    }

    public function getObfuscatedFileBody()
    {
        return [
            [
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.php.input'),
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.php.expected'),
            ],
        ];
    }
}
