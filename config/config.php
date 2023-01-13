<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'accessToken'         => env('UNITER1_ACCESS_TOKEN'),
    'baseNamespace'       => env('UNITER1_BASE_NAMESPACE', 'Tests\Unit'),
    'basePath'            => env('UNITER1_BASE_PATH', base_path()),
    'baseUrl'             => env('UNITER1_BASE_URL', 'https://uniter1.tech'),
    'helperClass'         => env('UNITER1_HELPER_CLASS', 'Uniter1\UniterRequester\PhpUnitTestHelper'),
    'generationPath'      => env('UNITER1_GENERATION_PATH', '/api/v1/generator/generate'),
    'obfuscate'           => env('UNITER1_OBFUSCATE', true),
    'projectDirectory'    => env('PROJECT_DIRECTORY', base_path()),
    'registrationPath'    => env('UNITER1_REGISTRATION_PATH', '/api/v1/registration/access-token'),
    'unitTestBaseClass'   => env('UNITER1_UNIT_TEST_BASE_CLASS', 'PHPUnit\Framework\TestCase'),
    'unitTestsDirectory'  => env('UNITER1_UNIT_TESTS_DIRECTORY', 'storage/tests/Unit'),
];
