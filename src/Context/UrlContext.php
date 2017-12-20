<?php

namespace TS\Web\UrlFinder\Context;


use TS\Web\UrlBuilder\Url;

interface UrlContext
{

	/**
	 * Make the URL absolute to the document base URL.
	 *
	 * @return self
	 */
	function makeAbsolute();

	/**
	 * Make the path of the URL absolute to the document base URL.
	 *
	 * @return self
	 */
	function makeAbsolutePath();

	/**
	 * The original URL, made absolute to the document base URL.
	 *
	 * @return Url
	 */
	function getAbsoluteUrl();

	/**
	 * The original URL as it was found in the document.
	 *
	 * @return Url
	 */
	function getOriginalUrl();

	/**
	 *
	 * @return ElementContext
	 */
	function getElementContext();

	/**
	 *
	 * @return DocumentContext
	 */
	function getDocumentContext();

	/**
	 *
	 * @return bool
	 */
	function hasChanged();

	/**
	 */
	function revertChanges();
}

