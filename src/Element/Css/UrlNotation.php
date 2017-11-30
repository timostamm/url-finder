<?php

namespace TS\Web\UrlFinder\Element\Css;


use TS\Web\UrlFinder\Context\ElementContext;
use TS\Web\UrlFinder\Element\StringElement;


class UrlNotation extends StringElement implements ElementContext
{

	const URL_DOUBLE_QUOTE = <<<REGEX
/url\s?\("([^"]*)"\)/u
REGEX;

	const URL_SINGLE_QUOTE = <<<REGEX
/url\s?\('([^']*)'\)/u
REGEX;

	const URL_NO_QUOTE = <<<REGEX
/url\s?\(([^"'\)]*)\)/u
REGEX;

	public static function find($string)
	{
		foreach (self::url_re(self::URL_DOUBLE_QUOTE, $string) as list ($raw_url, $offset)) {
			yield new UrlNotation($raw_url, $offset, self::URL_DOUBLE_QUOTE);
		}
		foreach (self::url_re(self::URL_SINGLE_QUOTE, $string) as list ($raw_url, $offset)) {
			yield new UrlNotation($raw_url, $offset, self::URL_SINGLE_QUOTE);
		}
		foreach (self::url_re(self::URL_NO_QUOTE, $string) as list ($raw_url, $offset)) {
			yield new UrlNotation($raw_url, $offset, self::URL_NO_QUOTE);
		}
	}

	private static function decode($str, $type)
	{
		switch ($type) {
			case self::URL_DOUBLE_QUOTE:
				return str_replace('\\"', '"', $str);
			case self::URL_SINGLE_QUOTE:
				return str_replace("\\'", "'", $str);
			case self::URL_NO_QUOTE:
				$str = str_replace('\(', '(', $str);
				$str = str_replace('\)', ')', $str);
				return $str;
			default:
				throw new \InvalidArgumentException("type");
		}
	}

	private static function encode($str, $type)
	{
		switch ($type) {
			case self::URL_DOUBLE_QUOTE:
				return str_replace('"', '\\"', $str);
			case self::URL_SINGLE_QUOTE:
				return str_replace("'", "\'", $str);
			case self::URL_NO_QUOTE:
				$str = str_replace('(', '\(', $str);
				$str = str_replace(')', '\)', $str);
				return $str;
			default:
				throw new \InvalidArgumentException("type");
		}
	}

	private static function url_re($re, $string)
	{
		$url_matches = [];
		preg_match_all($re, $string, $url_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach ($url_matches as $match) {
			$raw_url = $match[1][0];
			$offset = $match[1][1];
			yield [
				$raw_url,
				$offset
			];
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

	public function __construct($raw_url, $offset, $escapingType)
	{
		parent::__construct($offset, strlen($raw_url));
		$this->raw_url = $raw_url;
		$this->escapingType = $escapingType;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::encodeUrl()
	 */
	public function encodeUrl($url)
	{
		return self::encode($url, $this->escapingType);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::getUrl()
	 */
	public function getUrl()
	{
		return self::decode($this->raw_url, $this->escapingType);
	}

}


