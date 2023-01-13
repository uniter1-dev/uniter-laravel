<?php

namespace Uniter1\UniterLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class RealFileTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public function testCommand(): void
    {
        $command = $this->artisan('uniter1:generate', []);
        $this->expectException(RuntimeException::class);
        $command->run();
    }
}
