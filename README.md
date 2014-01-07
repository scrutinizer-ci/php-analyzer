# PHP Analyzer

The PHP Analyzer performs static analysis on your source code to help you find errors and bugs, and also has built-in
heuristics to automatically fix some of them.

Learn more in the [documentation](https://scrutinizer-ci.com/docs/tools/php/php-analyzer/).

## Installation

Please note that PHP Analyzer itself needs a PHP 5.4 runtime; this  requirement only applies to the system that is
running PHP Analyzer, not the code that is analyzed.

### Standalone

PHPAnalyzer can be installed via composer very easily:

```
composer create-project scrutinizer/php-analyzer:dev-master
```

### Embedded

PHPAnalyzer can also be embedded in an existing project:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/schmittjoh/PHP-Parser"
        }
    ],
    "require-dev": {
        "scrutinizer/php-analyzer": "*@dev",
        "scrutinizer/utils": "*@dev",
        "jms/php-manipulator": "*@dev",
        "nikic/php-parser": "*@dev"
    }
}
```

## Usage

### Via The Command Line

```
php bin/phpalizer run some-dir
```

The CLI is especially useful to analyze smaller libraries, or for testing purposes.

### Via a Continuous Build System Like scrutinizer-ci.com

For bigger libraries with dependencies, it is recommended to use a dedicated build system. You can set-up such a build
system on your own architecture, or use a hosted service like [scrutinizer-ci.com](https://scrutinizer-ci.com).

