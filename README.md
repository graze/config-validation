# config-validation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/config-validation.svg?style=flat-square)](https://packagist.org/packages/graze/config-validation)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/config-validation/master.svg?style=flat-square)](https://travis-ci.org/graze/config-validation)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/config-validation.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/config-validation/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/config-validation.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/config-validation)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/config-validation.svg?style=flat-square)](https://packagist.org/packages/graze/config-validation)

Config Validation checks an input against a code defined schema for validation using short notation syntax. 

It then populates optional fields with default data.

## Install

Via Composer

```bash
composer require graze/config-validation
```

## Usage

### Long path definition

```php
use Respect\Validation\Validator as v;

$validator = (new ArrayValidator())
    ->required('key', v::stringType())
    ->optional('parent.child', v::intVal(), 1)
    ->optional('parent.second', v::stringType()->date());
```

### Using children

```php
$validator = Validator::arr()
    ->required('key', v::stringType())
    ->addChild('parent', Validator::arr()
        ->optional('child', v::intVal(), 1)
        ->optional('second', v::stringType()->date()
    );
``` 

### Validating with your validator

```php   
function thing (array $input) {
    return $validator->validate($input);
}

thing(['key' => 'value'])
// ['key' => 'value', 'parent' => ['child' => 1, 'second' => null]]
thing();
// throws new ConfigValidationFailed
thing(['key' => 'input', ['parent' => ['child' => 2]])
// ['key' => 'input', ['parent' => ['child' => 2, 'second' => null]]
thing(['key' => 'input', ['parent' => ['second' => '111']])
// throws new ConfigValidationFailed('expected data for parent.second')
thing(['key' => 'input', ['parent' => ['second' => 
```

### Validating array items with variable keys

```php
$childValidator = Validate::object()
    ->required('key->item', v::intVal()->min(2)->max(4))
    ->optional('key->second', v::stringType(), 'name');
$validator = Validate::object()
    ->required('items', v::arrayVal()->each(
        $childValidator->getValidator()
    ));

function thing ($input) {
    return $validator->validate($input);
}

thing((object) ['items' => [
    (object) ['key' => 3],
    ]]);
// (object) ['items' => [
//     (object) ['key' => (object) ['item' => 3, 'second' => 'name']]
// ]]
```

## Testing

```shell
make build test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
