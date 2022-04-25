<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplicationPackageLaravel
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $path = __DIR__.'/../../../../bootstrap/app.php';
        $app = require $path;

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
