<?php namespace Bkwld\LaravelHaml;

class Haml {

	/**
	 * Inject dependencies
	 * 
	 * @param MtHaml\Environment $mthaml
	 * @param string $dir The views directory
	 * @param string $env The current environment
	 */
	public function __construct($mthaml, $dir, $env) {
		$this->mthaml = $mthaml;
		$this->dir = $dir;
		$this->env = $env;
	}

	/**
	 * Compile a haml file into a Laravel PHP view if called from a "local"
	 * environment.  It will save the rendered template in the same directory
	 * as the haml view, with the same name.
	 * 
	 * This is based largely on the example file from mthaml:
	 * https://github.com/arnaud-lb/MtHaml/blob/master/examples/example-php.php
	 * 
	 * @param string $haml_view The relative path (in the Laravel style) to the PHP
	 *        file containg the HAML.  Ex: 'home.index'.  The suffix is assumed to
	 *        be "haml".  Ex: '/app/views/home/index.haml'.
	 * @return void
	 */
	public function compile($haml_view) {

		// Only compile while on a local environment
		if ($this->env != 'local') return;

		// Get haml template code
		$view = $this->dir.DIRECTORY_SEPARATOR.str_replace('.', DIRECTORY_SEPARATOR, $haml_view);
		$haml_file = $view.'.haml';
		$php_file = $view.'.php';
		if (!file_exists($haml_file)) throw new Exception('HAML file does not exist at '.$haml_file);
		$haml_code = file_get_contents($haml_file);

		// No need to compile if already compiled and up to date
		if (file_exists($php_file) && filemtime($php_file) == filemtime($haml_file)) return;

		// Compile haml to php
		$php_code = $this->mthaml->compileString($haml_code, $haml_file);

		// Save php file which will get used by Laravel as a view
		$tmp = tempnam(dirname($haml_file), basename($haml_file));
		file_put_contents($tmp, $php_code);
		rename($tmp, $php_file);
		touch($php_file, filemtime($haml_file));

	}

}