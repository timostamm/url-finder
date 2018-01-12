<?php

namespace TS\Web\UrlFinder\Element\Html;


use TS\Web\UrlFinder\Context\ElementContext;
use TS\Web\UrlFinder\Element\StringElement;
use TS\Web\UrlFinder\Element\Css\UrlNotation;


class StyleTagUrlNotation extends StringElement implements ElementContext, HtmlTag
{

	const RE_STYLE_TAG_CONTENT = <<<REGEX
/<style[^>]*>((?:(?!<\/style)[\S\s])+)<\/style>/u
REGEX;

	public static function find($string)
	{
		$tag_matches = [];
		preg_match_all(self::RE_STYLE_TAG_CONTENT, $string, $tag_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach ($tag_matches as $m) {
			
			$tag_html = $m[0][0];
			$body_raw = $m[1][0];
			$body_offset = $m[1][1];
			$body_css = StringElement::decode_html_content($body_raw);
			
			foreach (UrlNotation::find($body_css) as $url) {
				$total_offset = $body_offset + $url->getOffset();
				$total_length = $url->getLength();
				yield new StyleAttributeUrlNotation($url, $total_offset, $total_length, $body_css, $tag_html);
			}
		}
	}

	/**
	 *
	 * @var UrlNotation
	 */
	private $url;

	/**
	 *
	 * @var string
	 */
	private $body_css;

	/**
	 *
	 * @var string
	 */
	private $tag_html;

	public function __construct(UrlNotation $url, $total_offset, $total_length, $body_css, $tag_html)
	{
		parent::__construct($total_offset, $total_length);
		$this->url = $url;
		$this->body_css = $body_css;
		$this->tag_html = $tag_html;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::encodeUrl()
	 */
	public function encodeUrl($url)
	{
		$cssEncoded = $this->url->encodeUrl($url);
		$htmlEncoded = StringElement::encode_html_content($cssEncoded);
		return $htmlEncoded;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see StringElement::getUrl()
	 */
	public function getUrl()
	{
		return $this->url->getUrl();
	}

	public function describe()
	{
		return sprintf('<style>');
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


