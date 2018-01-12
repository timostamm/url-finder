<?php

namespace TS\Web\UrlFinder;


use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Element\Css\UrlNotation;


class CssUrlFinder extends BaseUrlFinder
{

	public function supportsDocument($document)
	{
		if (! is_string($document)) {
			return false;
		}
		$c = trim($document);
		if (strpos($c, '<!DOCTYPE html>') === 0) {
			return false;
		}
		if (strpos($c, '<') === 0) {
			return false;
		}
		return true;
	}

	protected function parseDocumentUrls($document, Url $documentUrl)
	{
		foreach (UrlNotation::find($document) as $item) {
			$this->addParsedUrl($item->getUrl(), $item);
		}
	}

}

