<?php

namespace TS\Web\UrlFinder\Element\Html;


use TS\Web\UrlFinder\Context\ElementContext;
use TS\Web\UrlFinder\Element\StringElement;
use TS\Web\UrlFinder\Element\Css\UrlNotation;


class StyleAttributeUrlNotation extends StringElement implements ElementContext, HtmlTag
{

	const RE_ANY_STYLE_ATTR = '/<[^>]+[^>]* style="([^"]+)"[^>]*>/u';

	public static function find($string)
	{
		$tag_matches = [];
		preg_match_all(self::RE_ANY_STYLE_ATTR, $string, $tag_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach ($tag_matches as $m) {
			$tag_html = $m[0][0];
			$attr_offset = $m[1][1];
			$attr_raw = $m[1][0];
			$style_css = BaseAttributeValue::decode_html_content($attr_raw);
			
			foreach (UrlNotation::find($style_css) as $url) {
				$total_offset = $attr_offset + $url->getOffset();
				$total_length = $url->getLength();
				yield new StyleAttributeUrlNotation($url, $total_offset, $total_length, $style_css, $tag_html);
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
	private $style_css;

	/**
	 *
	 * @var string
	 */
	private $tag_html;

	public function __construct(UrlNotation $url, $total_offset, $total_length, $style_css, $tag_html)
	{
		parent::__construct($total_offset, $total_length);
		$this->url = $url;
		$this->style_css = $style_css;
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
		$tagname = trim(explode(' ', $this->getHtmlTag())[0], '<');
		return sprintf('URL "%s" in style attribute of <%s> tag', $this->getUrl(), $tagname);
	}

	public function getHtmlTag()
	{
		return $this->tag_html;
	}

}


