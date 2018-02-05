# V8Js ModuleLoader

[![Build Status](https://travis-ci.org/chenos/v8js-module-loader.svg?branch=master)](https://travis-ci.org/chenos/v8js-module-loader) [![Coverage Status](https://coveralls.io/repos/github/chenos/v8js-module-loader/badge.svg?branch=master)](https://coveralls.io/github/chenos/v8js-module-loader?branch=master)

## Requirements

- PHP 7.0+
- V8Js extension 2.0+

## Installation

```sh
composer require chenos/v8js-module-loader
```

## Dependents

- [chenos/execjs](https://github.com/chenos/execjs)

## Testing

```
make test
```

## Example

```php
make example
```

Access http://127.0.0.1:8888

## Usage

```php
use Chenos\V8Js\ModuleLoader\ModuleLoader;

// entry directory
$loader = new ModuleLoader(__DIR__);

$loader->setExtensions('.js', '.json');

// array
$loader->addOverride(['vue' => 'vue/dist/vue.runtime.common.js']);

// key, value
$loader->addOverride('vue', 'vue/dist/vue.runtime.common.js');

// v8js version > 2.1.0+
$loader->addOverride(['fn' => function (...$args) {}]);
$loader->addOverride('obj', new stdClass());

$loader->addVendorDir(__DIR__.'/node_modules', __DIR__.'/bower_components');

$v8 = new V8Js();

$v8->setModuleNormaliser([$loader, 'normaliseIdentifier']);
$v8->setModuleLoader([$loader, 'loadModule']);
```
