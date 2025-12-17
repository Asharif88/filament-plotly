# Inspired by https://filamentphp.com/plugins/leandrocfe-apex-charts & https://filamentphp.com/plugins/elemind-echarts this plugin delivers plotly.js integration for Filament.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asharif88/filament-plotly.svg?style=flat-square)](https://packagist.org/packages/asharif88/filament-plotly)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/asharif88/filament-plotly/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/asharif88/filament-plotly/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/asharif88/filament-plotly/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/asharif88/filament-plotly/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/asharif88/filament-plotly.svg?style=flat-square)](https://packagist.org/packages/asharif88/filament-plotly)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require asharif88/filament-plotly
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-plotly-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-plotly-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-plotly-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentPlotly = new Asharif88\FilamentPlotly();
echo $filamentPlotly->echoPhrase('Hello, Ahmad SHARIF!');
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

- [Ahmad SHARIF](https://github.com/Asharif88)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
