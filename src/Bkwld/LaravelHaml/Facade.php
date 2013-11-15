<?php namespace Bkwld\LaravelHaml;
class Facade extends \Illuminate\Support\Facades\Facade {
	protected static function getFacadeAccessor() { return 'laravel-haml'; }
}