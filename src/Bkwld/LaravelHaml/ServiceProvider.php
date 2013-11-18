<?php namespace Bkwld\LaravelHaml;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		
		// Register the main class which sits behind the HAML facade
		$this->app->singleton('laravel-haml', function($app) {

			// Get config
			$config = $app->make('config')->get('laravel-haml::config');

			// Inject Dependencies
			return new Haml(
				new \MtHaml\Environment($config['mthaml']['environment'], $config['mthaml']['options'], $config['mthaml']['filters']),
				$app->make('path').'/views',
				$app->environment()
			);
		});

	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package('bkwld/laravel-haml');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('laravel-haml');
	}

}