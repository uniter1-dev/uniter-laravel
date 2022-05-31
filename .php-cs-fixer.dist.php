<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'bootstrap',
        'docker',
        'public',
        'resources',
        'storage',
        'vendor',
        'node_modules',
        'packages/php-uniter/php-uniter-assistant/vendor',
        'packages/php-uniter/php-uniter-laravel/vendor'
    ])
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'binary_operator_spaces' => ['operators' => ['=>' => 'align']]
    ])
    ->setFinder($finder)
;
