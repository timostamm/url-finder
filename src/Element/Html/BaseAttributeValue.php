<?php

namespace TS\Web\UrlFinder\Element\Html;


use TS\Web\UrlFinder\Context\ElementContext;
use TS\Web\UrlFinder\Element\StringElement;


abstract class BaseAttributeValue extends StringElement implements ElementContext, HtmlTag
{

	public static function findTagAttr($re, $string)
	{
		$tag_matches = [];
		preg_match_all($re, $string, $tag_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach ($tag_matches as $match) {
			$tag_html = array_shift($match)[0];
			foreach ($match as $capture) {
				$offset = $capture[1];
				if ($offset == - 1) {
					continue;
				}
				$attr_raw = $capture[0];
				yield [
					$attr_raw,
					$offset,
					$tag_html
				];
			}
		}
	}

	/**
	 *
	 * @var string
	 */
	private $raw_url;

	/**
	 *
	 * @var string
	 */
	private $escapingType;

	/**
	 *
	 * @var string
	 */
	private $tag_html;

	public function __construct($raw_url, $offset, $tag_html)
	{
		parent::__construct($offset, strlen($raw_url));
		$this->raw_url = $raw_url;
		$this->tag_html = $tag_html;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::encodeUrl()
	 */
	public function encodeUrl($url)
	{
		return StringElement::encode_html_content($url);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::getUrl()
	 */
	public function getUrl()
	{
		return StringElement::decode_html_content($this->raw_url);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \TS\Web\UrlFinder\Element\Html\HtmlTag::getHtmlTag()
	 */
	public function getHtmlTag()
	{
		return $this->tag_html;
	}

}


