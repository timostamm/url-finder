<?php

namespace TS\Web\UrlFinder\Context;


use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\BaseUrlFinder;


class DocumentContext
{

	private $url;

	private $document;

	private $finder;

	public function __construct(Url $documentUrl=null, $document, BaseUrlFinder $finder = null)
	{
		$this->url = $documentUrl;
		$this->document = $document;
		$this->finder = $finder;
	}

	/**
	 * Get the URL of the document.
	 * 
	 * @return Url
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 *
	 * @return BaseUrlFinder
	 */
	public function getFinder()
	{
		return $this->finder;
	}
}

