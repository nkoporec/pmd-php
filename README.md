# A PHP adapter for [PMD](https://github.com/nkoporec/pmd)

A PHP adapter for the PMD debugger.

## Requirements

A working [PMD](https://github.com/nkoporec/pmd) server.

## Installation

You can install the package via composer:

```bash
composer require nkoporec/pmd-php
```

## Configuration

By default it will try to connect to 127.0.0.1:6969, if the PMD debugger is running on different port, then you can create a `pmd.yaml` file in the project root directory and it should look something like this (this is an example of how to set it up if you use docker containers)

```yaml
url: "host.docker.internal"
port: "6969"
```


## Usage

```php
pmd("Hello world!");
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [nkoporec](https://github.com/nkoporec)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
