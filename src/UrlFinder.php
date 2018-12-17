<?php

namespace TS\Web\UrlFinder;


use TS\Web\UrlFinder\Exception\DocumentException;
use TS\Web\UrlBuilder\Url;


class UrlFinder
{

	/**
	 *
	 * @param string $file
	 * @param string|Url|NULL $url
	 * @throws \InvalidArgumentException
	 * @throws DocumentException
	 * @return BaseUrlFinder
	 */
	public static function read($file, $url)
	{
		if (! is_file($file)) {
			throw new \InvalidArgumentException();
		}
		$document = file_get_contents($file);
		return self::create($document, $url);
	}

	/**
	 *
	 * @param mixed $document
	 * @param string|Url|NULL $url
	 * @throws DocumentException
	 * @return BaseUrlFinder
	 */
	public static function create($document, $url)
	{
		foreach (self::$finders as $factory) {
		    /** @var BaseUrlFinder $finder */
			$finder = $factory();
			if ($finder->supportsDocument($document)) {
				$finder->setDocument($document, $url);
				return $finder;
			}
		}
		throw new DocumentException('Unsupported document.');
	}

	/** @var array */
	private static $finders = [];

	public static function registerFactory(callable $factory)
	{
		array_unshift(self::$finders, $factory);
	}

	public static function unregisterAllFactories()
	{
		self::$finders = [];
	}

}

UrlFinder::registerFactory(function () {
	return new HtmlUrlFinder();
});

UrlFinder::registerFactory(function () {
	return new CssUrlFinder();
});
		
		
		