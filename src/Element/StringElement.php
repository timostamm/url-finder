<?php

namespace TS\Web\UrlFinder\Element;


use TS\Web\UrlFinder\Context\ElementContext;


abstract class StringElement implements ElementContext
{

	private $offset;

	private $length;

	
	
	public static function decode_html_content($str)
	{
		return html_entity_decode($str, ENT_HTML5, 'UTF-8');
	}
	
	public static function encode_html_content($str)
	{
		return htmlentities($str, ENT_HTML401, 'UTF-8');
	}
	
	
	public function __construct($offset, $length)
	{
		$this->offset = $offset;
		$this->length = $length;
	}

	abstract public function encodeUrl($url);

	abstract public function getUrl();
	
	public function describe()
	{
		return sprintf('"%s" at character %s', $this->getUrl(), $this->getOffset() + 1);
	}
	
	/**
	 *
	 * @return int
	 */
	final public function getOffset()
	{
		return $this->offset;
	}

	/**
	 *
	 * @return int
	 */
	final public function getLength()
	{
		return $this->length;
	}

}

