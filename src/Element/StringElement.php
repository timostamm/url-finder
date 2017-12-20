<?php

namespace TS\Web\UrlFinder\Element;


use TS\Web\UrlFinder\Context\ElementContext;
use OutOfRangeException;
use InvalidArgumentException;


abstract class StringElement implements ElementContext
{

	public static function decode_html_content($str)
	{
		return html_entity_decode($str, ENT_HTML5, 'UTF-8');
	}

	public static function encode_html_content($str)
	{
		return htmlentities($str, ENT_HTML401, 'UTF-8');
	}

	public static function getLineOf($string, $char_pos, $encoding = 'UTF-8'): array
	{
		if (!is_string($string)) {
			throw new InvalidArgumentException();
		}
		if (!is_int($char_pos)) {
			throw new InvalidArgumentException();
		}
		$encoding = is_null($encoding) ? mb_internal_encoding() : $encoding;
		if ($char_pos < 0 || $char_pos >= mb_strlen($string, $encoding)) {
			throw new OutOfRangeException();
		}
		$s = mb_substr($string, 0, $char_pos + 1, $encoding);
		$s = str_replace("\r\n", "X\n", $s);
		$s = str_replace("\r", "\n", $s);
		$line_count = substr_count($s, "\n");
		if ($line_count === 0) {
			$line_no = 1;
			$char_no = $char_pos + 1;
		} else {
			$line_no = $line_count + 1;
			$line_pos = mb_strrpos($s, "\n", null, $encoding) + 1;
			$char_no = ($char_pos - $line_pos) + 1;
		}
		return [
			$line_no,
			$char_no
		];
	}

	private $offset;

	private $length;

	public function __construct($offset, $length)
	{
		$this->offset = $offset;
		$this->length = $length;
	}

	abstract public function encodeUrl($url);

	abstract public function getUrl();

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

