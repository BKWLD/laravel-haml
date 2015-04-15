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

		// Determine the cache dir
		$cache_dir = storage_path($this->version() == 5 ? '/framework/views' : '/views');

		// Bind the package-configred MtHaml instance
		$this->app->singleton('laravel-haml.mthaml', function($app) {
			$config = $this->getConfig();
			return new MtHaml\Environment($config['mthaml']['environment'], 
				$config['mthaml']['options'], 
				$config['mthaml']['filters']);
		});

		// Bind the Haml compiler
		$this->app->singleton('Bkwld\LaravelHaml\HamlCompiler', function($app) use ($cache_dir) {
			return new HamlCompiler($app['laravel-haml.mthaml'], $app['files'], $cache_dir);
		});

		// Bind the Haml Blade compiler
		$this->app->singleton('Bkwld\LaravelHaml\HamlBladeCompiler', function($app) use ($cache_dir) {
			return new HamlBladeCompiler($app['laravel-haml.mthaml'], $app['files'], $cache_dir);
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

		// Register compilers
		$this->registerHamlCompiler();
		$this->registerHamlBladeCompiler();
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
	 * Register the regular haml compiler
	 *
	 * @return void
	 */
	public function registerHamlCompiler() {

		// Add resolver
		$this->app['view.engine.resolver']->register('haml', function() {
			return new CompilerEngine($this->app['Bkwld\LaravelHaml\HamlCompiler']);
		});

		// Add extensions
		$this->app['view']->addExtension('haml', 'haml');
		$this->app['view']->addExtension('haml.php', 'haml');
	}

	/**
	 * Register the blade compiler compiler
	 *
	 * @return void
	 */
	public function registerHamlBladeCompiler() {

		// Add resolver
		$this->app['view.engine.resolver']->register('haml.blade', function() {
			return new CompilerEngine($this->app['Bkwld\LaravelHaml\HamlBladeCompiler']);
		});

		// Add extensions
		$this->app['view']->addExtension('haml.blade', 'haml.blade');
		$this->app['view']->addExtension('haml.blade.php', 'haml.blade');
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
		return array(
			'Bkwld\LaravelHaml\HamlCompiler', 
			'Bkwld\LaravelHaml\HamlBladeCompiler',
			'laravel-haml.mthaml',
		);
	}

}