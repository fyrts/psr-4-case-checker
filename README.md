# PSR-4 Case Checker

Developing a project that uses a PSR-4 autoloader on an environment with a case-insensitive filesystem could cause
issues on deployment, as typos in filenames might not be spotted before they're pushed to case-sensitive production
environments. This little PHP library keeps an eye on the casing of your filenames during development, and throws an
exception when it spots any mismatches.

## Installation

The package is currently only supported in projects that make use of Composer:

```
composer require fyrts/psr-4-case-checker --dev
```

It is discouraged to use the package for production purposes, as it might have a perceptible impact on performance.

## Usage

No configuration or initialization is required, as the package is loaded automatically by Composer.

Any casing inconsistencies in classnames or namespaces will cause the package to throw an exception of type
`PSR4CaseChecker\ClassnameCasingException`.

```php
// The following would trigger an exception
$result = MyClass::method(); // MyClass being stored in myClass.php

// Incorrect namespaces would also trigger an exception
$result = \MyNamespace\MyClass::method(); // MyClass being stored in mynamespace/MyClass.php
```

## License

`fyrts/psr-4-case-checker` is licensed under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.