# Renames media as required to use with spatie/laravel-medialibrary version 7 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade)
[![Build Status](https://img.shields.io/travis/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade)

In version 7 of the [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) all conversions created with version 6 needs to be renamed with the original name in front of it.
When you download the [executable](https://github.com/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade/raw/master/builds/upgrade-tool.phar), rename it to `upgrade-tool` and then place it in your laravel projects root you can use the command `php upgrade-tool upgrade-media`.
This will rename all conversions made by `spatie/laravel-medialibrary` version 6 to match the new naming convention in version 7.

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
    └── green-circle.png
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
    └── green-circle.png
```

## Installation

You can install the command by downloading the [executable](https://github.com/spatie/laravel-medialibrary-v6-to-v7-filesystem-upgrade/raw/master/builds/upgrade-tool.phar), renaming it to `upgrade-tool` and putting it in you projects root folder.

## Usage

The command can handle any custom path and if you already converted some files by hand it will leave them.

We recommend to do a dry-run first like this:

``` bash
php upgrade-tool upgrade-media --dry-run
```

If the correct files are listed you can use this command to do the actual conversion:

``` bash
php upgrade-tool upgrade-media
```

When you want to use a disk that is not the `default_filesystem` in the `medialibrary.php` config file use:

``` bash
php upgrade-tool upgrade-media s3
```

You can have a more fine tuned location on the specified disk by adding a relative path like this:

``` bash
php upgrade-tool upgrade-media s3 '/media'
```

To convert your media in production, the `--force` flag comes to the rescue:

``` bash
php upgrade-tool upgrade-media --force
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
