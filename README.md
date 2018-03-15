# Renames media as required to use with spatie/laravel-medialibrary version 7 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/medialibrary-v7-upgrade-tool.svg?style=flat-square)](https://packagist.org/packages/spatie/medialibrary-v7-upgrade-tool)
[![Build Status](https://img.shields.io/travis/spatie/medialibrary-v7-upgrade-tool/master.svg?style=flat-square)](https://travis-ci.org/spatie/medialibrary-v7-upgrade-tool)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/medialibrary-v7-upgrade-tool.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/medialibrary-v7-upgrade-tool)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/medialibrary-v7-upgrade-tool.svg?style=flat-square)](https://packagist.org/packages/spatie/medialibrary-v7-upgrade-tool)

In version 7 of the [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) all media created with version 6 needs to be renamed with the original name in front of it.
This package adds a command `php artisan upgrade-media {media-location}` that renames your current media in the specified folder.

It will analyse the folder structure and rename where needed.
For example from:

```bash
media 
├── 1
│   ├── conversions
│   │   └── thumb.png
│   └── red-square.png
└── 2
    ├── conversions
    │   └── thumb.png
    └── green-square.png
```

to:

```bash
media 
├── 1
│   ├── conversions
│   │   └── red-square-thumb.png
│   └── red-square.png
└── 2
    ├── conversions
    │   └── green-square-thumb.png
    └── green-square.png
```

## Installation

You can install the package via composer:

```bash
composer require spatie/medialibrary-v7-upgrade-tool
```

## Usage

To convert your media folder user this command:

``` bash
php artisan upgrade-media {media-location}
```

To get a list of the files that would be changed add the `--dry-run` flag:

``` bash
php artisan upgrade-media {media-location} --dry-run
```

To convert your media folder in production add the `--force` flag:

``` bash
php artisan upgrade-media {media-location} --force
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Thomas Verhelst](https://github.com/TVke)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
