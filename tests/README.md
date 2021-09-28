# Testing Laravelayers

First, you need to install [Laravel Framework](https://laravel.com/) using [Composer](https://getcomposer.org/):

```php
composer create-project --prefer-dist laravel/laravel $HOME/sites/laravel
```

> Note that the installation uses the `$HOME/sites/laravel` directory.

Second, clone the Laravelayers into an adjacent directory:

```php
git clone https://github.com/laravelayers/laravelayers.git
```

> Note that the cloning uses the `$HOME/sites/laravelayers` directory.

Third, add the path to install Laravelayers to the `composer.json` file in the Laravel directory:

```php
composer config repositories.laravelayers path ../laravelayers/laravelayers

/*
    "repositories": {
        "laravelayers": {
            "type": "path",
            "url": "../laravelayers/laravelayers"
        }
    }
*/
```

> Note that you first need to navigate to the directory where you installed Laravel, for example, `cd $HOME/sites/laravel`.

Fourth, install Laravelayers by adding a dependency using Composer:

```php
composer require laravelayers/laravelayers
```

Fifth, copy the files from the `vendor/laravelayers/laravelayers/database/Factories/Laravelayers` directory to the `database/Factories/Laravelayers`:

```php
cp -R vendor/laravelayers/laravelayers/database/Factories/Laravelayers database/Factories/Laravelayers
```

Sixth, run the tests:

```php
vendor/bin/phpunit vendor/laravelayers/laravelayers/tests
```