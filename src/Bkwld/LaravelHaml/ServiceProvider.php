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

			// Inject Dependencies
			return new Haml(
				new \MtHaml\Environment('php'),
				$this->app->make('path').'/views',
				$this->app->environment()
			);
		});

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