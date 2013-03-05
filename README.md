# PHP Analyzer

The PHP Analyzer performs static analysis on your source code to help you find errors and bugs, and also has built-in
heuristics to automatically fix some of them.

Learn more in the [documentation](https://scrutinizer-ci.com/docs/tools/php/php-analyzer/).

## Installation

PHPAnalyzer can be installed via composer very easily:

```
composer create-project scrutinizer/php-analyzer:dev-master
```

Please note that PHP Analyzer cannot be installed as a dependency of your root project currently, but
must be installed as a standalone project.

## Usage

### Via The Command Line

```
php bin/phpalizer run some-dir
```

The CLI is especially useful to analyze smaller libraries, or for testing purposes.

### Via a Continuous Build System Like scrutinizer-ci.com

For bigger libraries with dependencies, it is recommended to use a dedicated build system. You can set-up such a build
system on your own architecture, or use a hosted service like [scrutinizer-ci.com](https://scrutinizer-ci.com).

