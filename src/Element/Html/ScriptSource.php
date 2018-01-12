<?php

namespace TS\Web\UrlFinder\Element\Html;


class ScriptSource extends BaseAttributeValue
{

	const RE_SCRIPT_SRC = '/<script[^>]* src="([^"]+)"[^>]*>/u';

	public static function find($string)
	{
		$tag_matches = self::findTagAttr(self::RE_SCRIPT_SRC, $string);
		foreach (self::findTagAttr(self::RE_SCRIPT_SRC, $string) as list ($raw_url, $offset, $tag_html)) {
			yield new ScriptSource($raw_url, $offset, $tag_html);
		}
	
	}

	public function describe()
	{
		return sprintf('<script src="…">', $this->getUrl());
	}

}


