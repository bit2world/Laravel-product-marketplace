

## Features

This repository is a full-featured e-commerce package:

* Multi vendor, multi channel and multi warehouse
* From one to 1,000,000,000+ items
* Extremly fast down to 20ms
* For multi-tentant e-commerce SaaS solutions with unlimited vendors
* Bundles, vouchers, virtual, configurable, custom and event products
* Subscriptions with recurring payments
* 100+ payment gateways
* Full RTL support (frontend and backend)
* Block/tier pricing out of the box
* Extension for customer/group based prices
* Discount and voucher support
* Flexible basket rule system
* Full-featured admin backend
* Beautiful admin dashboard
* Configurable product data sets
* JSON REST API based on jsonapi.org
* GraphQL API for administration
* Completly modular structure
* Extremely configurable and extensible
* Extension for market places with millions of vendors
* Fully SEO optimized including rich snippets
* Translated to 30+ languages
* AI-based text translation
* Optimized for smart phones and tablets
* Secure and reviewed implementation
* High quality source code


## Alternatives

### Full shop application

If you want to set up a new application or test Aimeos, we recommend the Aimeos
shop distribution. It contains everything for a quick start and you will get a
fully working online shop in less than 5 minutes:


### Headless distribution

If you want to build a single page application (SPA) respectively a progressive web
application (PWA) yourself and don't need the Aimeos HTML frontend, then the Aimeos
headless distribution is the right choice:


## Table of content

- [Supported versions](#supported-versions)
- [Requirements](#requirements)
- [Database](#database)
- [Installation](#installation)
- [Authentication](#authentication)
- [Setup](#setup)
- [Test](#test)
- [Hints](#hints)
- [License](#license)
- [Links](#links)


## Requirements

The Aimeos shop distribution requires:
- Linux/Unix, WAMP/XAMP or MacOS environment
- PHP >= 8.0.11
- MySQL >= 5.7.8, MariaDB >= 10.2.2, PostgreSQL 9.6+, SQL Server 2019+
- Web server (Apache, Nginx or integrated PHP web server for testing)

If required PHP extensions are missing, `composer` will tell you about the missing
dependencies.


## Database

Make sure that you've **created the database** in advance and added the configuration
to the `.env` file in your application directory. Sometimes, using the .env file makes
problems and you will get exceptions that the connection to the database failed. In that
case, add the database credentials to the **resource/db section of your ./config/shop.php**
file too!

If you don't have at least MySQL 5.7.8 or MariaDB 10.2.2 installed, you will probably get an error like

```
Specified key was too long; max key length is 767 bytes
```

To circumvent this problem, drop the new tables if there have been any created and
change the charset/collation setting in `./config/database.php` to these values before
installing Aimeos again:

```php
'connections' => [
    'mysql' => [
        // ...
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        // ...
    ]
]
```

**Caution:** Also make sure that your MySQL server creates *InnoDB* tables by default as *MyISAM*
tables won't work and will result in an foreign key constraint error!


* MySQL, MariaDB (fully)
* PostgreSQL (fully)
* SQL Server (fully)

## Installation

The Aimeos Laravel online shop package is a composer based library. It can be
installed easiest by using [Composer 2.1+](https://getcomposer.org) in the root
directory of your existing Laravel application:

```
wget https://getcomposer.org/download/latest-stable/composer.phar -O composer
php composer require aimeos/aimeos-laravel:~2023.04
```

Then, add these lines to the composer.json of the **Laravel skeleton application**:

```json
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-laravel": "~2023.04",
        ...
    },
    "scripts": {
        "post-update-cmd": [
            "@php artisan migrate",
            "@php artisan vendor:publish --tag=public --force",
            "\\Aimeos\\Shop\\Composer::join"
        ],
        ...
    }
```

Afterwards, install the Aimeos shop package using

`composer update`

In the last step you must now execute these artisan commands to get a working
or updated Aimeos installation:

```bash
php artisan vendor:publish --provider="Aimeos\Shop\ShopServiceProvider"
php artisan migrate
php artisan aimeos:setup --option=setup/default/demo:1
```

In a production environment or if you don't want that the demo data gets
installed, leave out the `--option=setup/default/demo:1` option.

## Authentication

You have to set up one of Laravel's authentication starter kits. Laravel Breeze
is the easiest one but you can also use Jetstream.

### Laravel 9 & 10

```bash
composer require laravel/breeze
php artisan breeze:install
npm install && npm run build
```

Laravel Breeze adds a route for `/profile` to `./routes/web.php` which may overwrite the
`aimeos_shop_account` route. To avoid an exception about a missing `aimeos_shop_account`
route, change the URL for these lines from `./routes/web.php` file from `/profile` to
`/profile/me`:

```php
Route::middleware('auth')->group(function () {
    Route::get('/profile/me', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/me', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/me', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
```

For more information, please follow the Laravel documentation:
* [Laravel 10.x](https://laravel.com/docs/10.x/authentication)
* [Laravel 9.x](https://laravel.com/docs/9.x/authentication)

### Configure authentication

As a last step, you need to extend the `boot()` method of your
`App\Providers\AuthServiceProvider` class and add the lines to define how
authorization for "admin" is checked in `app/Providers/AuthServiceProvider.php`:

```php
    public function boot()
    {
        // Keep the lines before

        \Illuminate\Support\Facades\Gate::define('admin', function($user, $class, $roles) {
            if( isset( $user->superuser ) && $user->superuser ) {
                return true;
            }
            return app( '\Aimeos\Shop\Base\Support' )->checkUserGroup( $user, $roles );
        });
    }
```

### Create account

Test if your authentication setup works before you continue. Create an admin account
for your Laravel application so you will be able to log into the Aimeos admin interface:

```bash
php artisan aimeos:account --super <email>
```

The e-mail address is the user name for login and the account will work for the
frontend too. To protect the new account, the command will ask you for a password.
The same command can create limited accounts by using `--admin`, `--editor` or `--api`
instead of `--super` (access to everything).

## Setup

To reference images correctly, you have to adapt your `.env` file and set the `APP_URL`
to your real URL, e.g.

```
APP_URL=http://127.0.0.1:8000
```

**Caution:** Make sure, Laravel uses the `file` session driver in your `.env` file!
Otherwise, the shopping basket content won't get stored correctly!

```
SESSION_DRIVER=file
```

If your `./public` directory isn't writable by your web server, you have to create these
directories:

```
mkdir public/aimeos public/vendor
chmod 777 public/aimeos public/vendor
```

In a production environment, you should be more specific about the granted permissions!

## Test

Then, you should be able to call the catalog list page in your browser. For a
quick start, you can use the integrated web server. Simply execute this command
in the base directory of your application:

```
php artisan serve
```

### Frontend

Point your browser to the list page of the shop using:

http://127.0.0.1:8000/shop

**Note:** Integrating the Aimeos package adds some routes like `/shop` or `/admin` to your
Laravel installation but the **home page stays untouched!** If you want to add Aimeos to
the home page as well, replace the route for "/" in `./routes/web.php` by this line:

```php
Route::group(['middleware' => ['web']], function () {
    Route::get('/', '\Aimeos\Shop\Controller\CatalogController@homeAction')->name('aimeos_home');
});
```

For multi-vendor setups, read the article about [multiple shops](https://aimeos.org/docs/latest/laravel/customize/#multiple-shops).

### Backend

If you've still started the internal PHP web server (`php artisan serve`)
you should now open this URL in your browser:

http://127.0.0.1:8000/admin

Enter the e-mail address and the password of the newly created user and press "Login".
If you don't get redirected to the admin interface (that depends on the authentication
code you've created according to the Laravel documentation), point your browser to the
`/admin` URL again.

**Caution:** Make sure that you aren't already logged in as a non-admin user! In this
case, login won't work because Laravel requires you to log out first.


## Hints

To simplify development, you should configure to use no content cache. You can
do this in the `config/shop.php` file of your Laravel application by adding
these lines at the bottom:

```php
    'madmin' => [
        'cache' => [
            'manager' => [
                'name' => 'None',
            ],
        ],
    ],
```
## Link
https://join.skype.com/invite/puo9lhuCrnbs

