<?php

namespace App\Application\Services;

use Illuminate\Translation\Translator as BaseTranslator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

class DummyTranslator extends BaseTranslator implements TranslatorContract
{
	public function __construct()
	{
		// No translation needed, so we pass an empty loader
		parent::__construct(new ArrayLoader(), 'en');
	}

	// Implement other methods that might be needed
	public function get($key, array $replace = [], $locale = null, $fallback = true)
	{
		return $key;
	}

	public function has($key, $locale = null, $fallback = true)
	{
		return false;
	}
}
