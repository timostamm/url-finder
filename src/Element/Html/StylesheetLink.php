<?php

namespace TS\Web\UrlFinder\Element\Html;


class StylesheetLink extends BaseAttributeValue
{

	const RE_LINK_STYLESHEET_HREF = '/<link[^>]* (?:rel="stylesheet"[^>]*href="([^"]+)"|href="([^"]+)"[^>]*rel="stylesheet")[^>]*>/u';

	public static function find($string)
	{
		$tag_matches = self::findTagAttr(self::RE_LINK_STYLESHEET_HREF, $string);
		foreach ($tag_matches as list ($raw_url, $offset, $tag_html)) {
			yield new StylesheetLink($raw_url, $offset, $tag_html);
		}
	}

	public function describe()
	{
		return sprintf('<link rel="stylesheet" href="…">');
	}

}


