<?php namespace Bkwld\LaravelHaml;

// Dependencies
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use MtHaml\Environment;

class HamlBladeCompiler extends BladeCompiler implements CompilerInterface {

	/**
	 * The MtHaml instance.
	 *
	 * @var \MtHaml\Environment
	 */
	protected $mthaml;

	/**
	 * Create a new compiler instance.
	 *
	 * @param  \MtHaml\Environment $mthaml
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $cachePath
	 * @return void
	 */
	public function __construct(Environment $mthaml, Filesystem $files, $cachePath)
	{
		$this->mthaml = $mthaml;
		parent::__construct($files, $cachePath);
	}

	/**
	 * Compile the view at the given path.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function compile($path) {
		$this->footer = array();
		
		if (is_null($this->cachePath)) return;

		// First compile the Haml
		$contents = $this->mthaml->compileString($this->files->get($path), $path);

		// Then the Blade syntax
		$contents = $this->compileString($contents);

		// Save
		$this->files->put($this->getCompiledPath($path), $contents);
	}

}
