<?php

declare(strict_types=1);

namespace PhpUniter\PackageLaravel;

use PhpUniter\PackageLaravel\Infrastructure\Exception\ClassNotFound;

/**
 * Class PhpUnitTestHelper.
 * useful to make All Methods Public.
 */
class PhpUnitTestHelper
{
    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    /**
     * @param string $fullyQualifiedClassName Fully qualified class name with namespace
     *
     * @return string|null Fully qualified proxy class name with namespace or null
     */
    public function makeAllMethodsPublic(string $fullyQualifiedClassName): ?string
    {
        $classNameExploded = explode('\\', $fullyQualifiedClassName);
        $className = array_pop($classNameExploded);

        $proxyClassName = "${className}".uniqid();

        try {
            $proxyClassBody = $this->renderProxyClass($fullyQualifiedClassName, $className, $proxyClassName);

            self::loadClass($proxyClassName, $proxyClassBody);

            $fullyQualifiedProxyClassName = self::getProxyClassName($classNameExploded, $proxyClassName);
        } catch (ClassNotFound $exception) {
            return null;
        }

        return $fullyQualifiedProxyClassName;
    }

    /**
     * @throws ClassNotFound
     */
    private function getClassBody(string $fullyQualifiedClassName): string
    {
        $loader = require $this->projectRoot.'/vendor/autoload.php';

        if ($classFilePath = $loader->findFile($fullyQualifiedClassName)) {
            if ($classBody = file_get_contents($classFilePath)) {
                return $classBody;
            }
        }

        throw new ClassNotFound("Class {$fullyQualifiedClassName} not found or not available by path $classFilePath");
    }

    /**
     * @psalm-suppress UnresolvableInclude
     */
    private static function loadClass(string $proxyFileName, string $proxyClassBody): void
    {
        $fileName = __DIR__."/${proxyFileName}.php";

        file_put_contents($fileName, $proxyClassBody);

        include $fileName;

        unlink($fileName);
    }

    private static function getProxyClassName(array $classNameExploded, string $proxyClassName): string
    {
        $classNameExploded[] = $proxyClassName;

        return implode('\\', $classNameExploded);
    }

    /**
     * @throws ClassNotFound
     */
    private function renderProxyClass(string $fullyQualifiedClassName, string $className, string $proxyClassName): string
    {
        $classBody = $this->getClassBody($fullyQualifiedClassName);

        return preg_replace(
            ["/class\s+${className}/i", '/(|public|private|protected)\s+(static\s+)?function/i'],
            ["class $proxyClassName", 'public $2function'],
            $classBody
        );
    }
}
