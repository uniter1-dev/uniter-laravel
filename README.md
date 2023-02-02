## Short instruction
composer require uniter1/uniter1-laravel
php artisan uniter1:register {email} {password}

// edit .env file
// UNITER1_ACCESS_TOKEN= token from your mail
// UNITER1_PROJECT_DIRECTORY= path to project root

php artisan uniter1:generate {filePath}

## Installation

You can install the package via composer:

```bash
composer require uniter1/uniter1-laravel
```
## Testing
To run tests in package, you need to add
"PhpUniter\\PackageLaravel\\Tests\\": "vendor/uniter1/uniter1-laravel/tests"
to  "autoload-dev": "psr-4" section of your composer.json

## Usage

### User registration:
```php
php artisan uniter1:register {email} {password}
```
User will be registered, and access token will be sent to your email. You need to put that token to yours .env file as UNITER1_ACCESS_TOKEN. After that you can send test generation queries.

### Package settings:

First, you need to set config values to you .env file:

```bash
UNITER1_ACCESS_TOKEN=***
```
Access token sent after registration to your email.

Other options you may remain as defaults. Check that unit tests directory (defaults tests/Unit) exists and is writable.

```bash
UNITER1_BASE_URL=https://uniter1.tech
```
This is web address for our service. Use https://uniter1.tech

```bash
UNITER1_OBFUSCATE=true
```
Obfuscation option. Set it to false if you need no obfuscation for you code.
```bash
UNITER1_UNIT_TEST_BASE_CLASS=PHPUnit\Framework\TestCase
```
Base framework for yours test class.
```bash
UNITER1_UNIT_TESTS_DIRECTORY=tests/Unit
```
Base directory to save generated test classes. Check that directory exists and is writable.

```bash
UNITER1_INSPECTOR_MODE=true
```
if true, private and protected methods will be tested. If false, none.

### Test generation
```bash
php artisan uniter1:generate {filePath} [{--overwrite-one-method=yours_method_name}]
```
Your class you want to test will be read from {filePath}, obfuscated if you did not turn obfuscation off, sent to our service. There will be created some phpunit test file to test different variants of yours class methods. The result will be deobfuscated and saved to UNITER1_UNIT_TESTS_DIRECTORY - to some nested folder according to class namespace.

Open it, read it and use to test yours class.

Generated class, possibly, will not be completed test - read comments before each generated test method.

overwrite-one-method option allows you to replace only those methods that test one of the methods of the source class in the previously generated test class during generation. Be careful when using it - it is necessary that the previously generated test lies at the same address, and also that no changes are made to the class under test, except for the method that we specified with this option.

### Testing

You find package tests in package folder tests.

### Security

If you discover any security related issues, please email support@uniter1.tech instead of using the issue tracker.

## Credits

-   [PhpUniter](https://github.com/uniter1)

## v1.9
