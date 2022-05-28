<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

class NamespaceGenerator
{
    private string $testsNamespace;
    private string $testsDirectory;

    private PathCorrector $pathCorrector;

    public function __construct(string $testsNamespace, string $testsDirectory)
    {
        $this->testsNamespace = $testsNamespace;

        $this->pathCorrector = new PathCorrector();
        $this->testsDirectory = $testsDirectory;
    }

    public function fetch(string $code): string
    {
        return self::addNamespace($code, $this->make($code));
    }

    public function makeNamespace(string $srcNamespace): string
    {
        $path = $this->pathCorrector::normaliseBackSlashes($this->testsNamespace.'\\'.$srcNamespace);

        return 'namespace '.$path.';';
    }

    private function make($code): string
    {
        $srcNamespace = self::findNamespace($code);
        $path = $this->pathCorrector::normaliseBackSlashes($this->testsNamespace.'\\'.$srcNamespace);

        return 'namespace '.$path.';';
    }

    public function makePathToTest($namespace): string
    {
        return $this->testsDirectory.'/'.$this->pathCorrector::toSlashes($namespace);
    }

    public static function addNamespace($code, $namespace): string
    {
        $replace = '<?php'."\n".$namespace."\n";

        return str_replace("<?php\n", $replace, $code);
    }

    public static function findNamespace(string $classText): string
    {
        if (preg_match('/(?<=namespace\s)([^;]+)/', $classText, $matches)) {
            return $matches[0];
        }

        return '';
    }
}
