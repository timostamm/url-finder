<?php

namespace TS\Web\UrlFinder\Mock;


use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Context\DocumentContext;


class MockDocumentContext implements DocumentContext
{

	private $url;

	public function __construct(Url $url)
	{
		$this->url = $url;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \TS\Web\UrlFinder\Context\DocumentContext::getDocumentUrl()
	 */
	public function getDocumentUrl()
	{
		return $this->url;
	}
}
