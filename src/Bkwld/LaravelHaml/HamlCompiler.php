<?php namespace Bkwld\LaravelHaml;

// Dependencies
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use MtHaml\Environment;

class HamlCompiler extends Compiler implements CompilerInterface {

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
		\Log::info('farts dude');
		$contents = $this->mthaml->compileString($this->files->get($path), $path);
		if (!is_null($this->cachePath)) {
			$this->files->put($this->getCompiledPath($path), $contents);
		}
	}

}