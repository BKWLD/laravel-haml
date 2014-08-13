# Laravel Haml

A small package that adds support for compiling Haml templates to Laravel via [MtHaml](https://github.com/arnaud-lb/MtHaml).



## Installation

1. Add it to your composer.json (`"bkwld/laravel-haml": "~2.0"`) and do a composer install.

2. Add the service provider to your app.php config file providers: `'Bkwld\LaravelHaml\ServiceProvider',`



## Configuration

You can set [MtHaml](https://github.com/arnaud-lb/MtHaml) environment, options, and filters manually.  To do so, publish the config file with `php artisan config:publish bkwld/laravel-haml` and edit it at /app/config/packages/bkwld/laravel-haml/config.php.  For instance, to turn off auto-escaping:

	'mthaml' => array(
		'environment' => 'php',
		'options' => array(
			'enable_escaper' => false,
		),
		'filters' => array(),
	), 



## Usage

Laravel-Haml registers the ".haml.php" extension with Laravel and forwards compile requests on to MtHaml.  It compiles your Haml templates in the same way as Blade templates; the compiled template is put in app/storage/views.  Thus, you don't suffer compile times on every page load.

In other words, just put your Haml files in the regular app/views directory and name them like "app/views/home/whatever.haml.php".  You reference them in Laravel like normal: `View::make('home.whatever')`.

The Haml view files can work side-by-side with regular PHP views.



## Release notes

- 2.0 - Integrated better into Laravel as a registered template compiler.
- 1.0 - Implemented using the pattern described in the MtHaml README where you expliclity call compile for each template.
