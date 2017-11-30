<?php

namespace TS\Web\UrlFinder\Element\Html;


class ImageSource extends BaseAttributeValue
{

	const RE_IMG_SRC = '/<img[^>]* src="([^"]+)"[^>]*>/u';

	public static function find($string)
	{
		$tag_matches = self::findTagAttr(self::RE_IMG_SRC, $string, $string);
		foreach ($tag_matches as list ($raw_url, $offset, $tag_html)) {
			yield new ImageSource($raw_url, $offset, $tag_html);
		}
	
	}

}


