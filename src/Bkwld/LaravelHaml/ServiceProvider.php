<?php namespace Bkwld\LaravelHaml;

// Dependencies
use MtHaml;
use Illuminate\View\Engines\CompilerEngine;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Get the major Laravel version number
	 *
	 * @return integer 
	 */
	public function version() {
		$app = $this->app;
		return intval($app::VERSION);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		// Version specific registering
		if ($this->version() == 5) $this->registerLaravel5();

		// Bind the Haml compiler
		$this->app->bindShared('Bkwld\LaravelHaml\HamlCompiler', function($app) {

			// Instantiate MtHaml, the brains of the operation
			$config = $this->getConfig();
			$mthaml = new MtHaml\Environment($config['mthaml']['environment'], $config['mthaml']['options'], $config['mthaml']['filters']);

			// Instantiate our Laravel-style compiler
			$cache = $this->version() == 5 ? storage_path('/framework/views') : $app['path.storage'].'/views';
			return new HamlCompiler($mthaml, $app['files'], $cache);
		});

	}

	/**
	 * Register specific logic for Laravel 5. Merges package config with user config
	 * 
	 * @return void
	 */
	public function registerLaravel5() {
		$this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'laravel-haml');
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {

		// Version specific booting
		switch($this->version()) {
			case 4: $this->bootLaravel4(); break;
			case 5: $this->bootLaravel5(); break;
			default: throw new Exception('Unsupported Laravel version');
		}

		// Add the .haml.php extension and register the Haml compiler with
		// Laravel's view engine resolver
		$app = $this->app;
		$app['view']->addExtension('haml.php', 'haml', function() use ($app) {
			return new CompilerEngine($app['Bkwld\LaravelHaml\HamlCompiler']);
		});
	}

	/**
	 * Boot specific logic for Laravel 4. Tells Laravel about the package for auto 
	 * namespacing of config files
	 * 
	 * @return void
	 */
	public function bootLaravel4() {
		$this->package('bkwld/laravel-haml');
	}

	/**
	 * Boot specific logic for Laravel 5. Registers the config file for publishing 
	 * to app directory
	 * 
	 * @return void
	 */
	public function bootLaravel5() {
		$this->publishes([
			__DIR__.'/../../config/config.php' => config_path('haml.php')
		], 'laravel-haml');
	}

	/**
	 * Get the configuration, which is keyed differently in L5 vs l4
	 *
	 * @return array 
	 */
	public function getConfig() {
		$key = $this->version() == 5 ? 'laravel-haml' : 'laravel-haml::config';
		return $this->app->make('config')->get($key);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('Bkwld\LaravelHaml\HamlCompiler');
	}

}