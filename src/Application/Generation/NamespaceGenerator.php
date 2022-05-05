<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

class NamespaceGenerator
{
    private string $globalNamespace;
    private string $projectDirectory;

    public function __construct(string $globalNamespace, string $projectDirectory)
    {
        $this->globalNamespace = $globalNamespace;
        $this->projectDirectory = $projectDirectory;
    }

    public function fetch($code, $path): string
    {
        return self::addNamespace($code, $this->make($path));
    }

    private function make($path): string
    {
        $path = PathCorrector::normaliseBackSlashes($this->globalNamespace.'\\'.dirname($path));

        return 'namespace '.$path.';';
    }

    public function makeRelative($path): string
    {
        return PathCorrector::findRelativePath($path, $this->projectDirectory);
    }

    private static function addNamespace($code, $namespace): string
    {
        $replace = '<?php'."\n".$namespace."\n";

        return str_replace("<?php\n", $replace, $code);
    }
}
