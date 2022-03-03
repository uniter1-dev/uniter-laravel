<?php

namespace PhpUniter\PackageLaravel\Tests\Unit\Application\Obfuscator\Entity;

use Mockery;
use PHPUnit\Framework\TestCase;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;

class ObfuscatedClassTest extends TestCase
{
    /**
     * @dataProvider getObfuscatedFileBody
     */
    public function testGetObfuscatedFileBody($input, $expected)
    {
        $localFile = Mockery::mock(LocalFile::class);
        $localFile->shouldReceive('getFileBody')
            ->andReturn($input);

        $keys = ['className'];
        $keyGenerator = function () use ($keys) {
            static $i = 0;

            return $keys[$i++];
        };

        $obfuscatedClassObject = new ObfuscatedClass(
            $localFile,
            $keyGenerator
        );

        $this->assertEquals($expected, $obfuscatedClassObject->getObfuscatedFileBody());
    }

    public function getObfuscatedFileBody()
    {
        return [
            [
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.php.input'),
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.php.obfuscated'),
            ],
        ];
    }
}
