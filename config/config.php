<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'accessToken'         => env('PHP_UNITER_ACCESS_TOKEN'),
    'baseNamespace'       => env('PHP_UNITER_BASE_NAMESPACE', 'Tests\Unit'),
    'basePath'            => env('PHP_UNITER_BASE_PATH', base_path()),
    'baseUrl'             => env('PHP_UNITER_BASE_URL', 'https://uniter1.tech'),
    'helperClass'         => env('PHP_UNITER_HELPER_CLASS', 'PhpUniter\PhpUniterRequester\PhpUnitTestHelper'),
    'generationPath'      => env('PHP_UNITER_GENERATION_PATH', '/api/v1/generator/generate'),
    'obfuscate'           => env('PHP_UNITER_OBFUSCATE', true),
    'preprocess'          => env('PHP_UNITER_PREPROCESS', true),
    'projectDirectory'    => env('PROJECT_DIRECTORY', base_path()),
    'registrationPath'    => env('PHP_UNITER_REGISTRATION_PATH', '/api/v1/registration/access-token'),
    'unitTestBaseClass'   => env('PHP_UNITER_UNIT_TEST_BASE_CLASS', 'PHPUnit\Framework\TestCase'),
    'unitTestsDirectory'  => env('PHP_UNITER_UNIT_TESTS_DIRECTORY', 'storage/tests/Unit'),
];