<?php

namespace TS\Web\UrlFinder;


use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Element\Html\ImageSource;
use TS\Web\UrlFinder\Element\Html\ScriptSource;
use TS\Web\UrlFinder\Element\Html\StyleAttributeUrlNotation;
use TS\Web\UrlFinder\Element\Html\StyleTagUrlNotation;
use TS\Web\UrlFinder\Element\Html\StylesheetLink;


class HtmlUrlFinder extends BaseUrlFinder
{

	public function supportsDocument($document)
	{
		if (! is_string($document)) {
			return false;
		}
		$c = trim($document);
		if (strpos($c, '<!DOCTYPE html>') === 0) {
			return true;
		}
		if (strpos($c, '<') === 0) {
			return true;
		}
		return false;
	}

	protected function parseDocumentUrls($document, Url $documentUrl)
	{
		foreach (ImageSource::find($document) as $ctx) {
			
			$this->addParsedUrl($ctx->getUrl(), $ctx);
		
		}
		foreach (ScriptSource::find($document) as $ctx) {
			
			$this->addParsedUrl($ctx->getUrl(), $ctx);
		
		}
		foreach (StylesheetLink::find($document) as $ctx) {
			
			$this->addParsedUrl($ctx->getUrl(), $ctx);
		
		}
		foreach (StyleAttributeUrlNotation::find($document) as $ctx) {
			
			$this->addParsedUrl($ctx->getUrl(), $ctx);
		
		}
		foreach (StyleTagUrlNotation::find($document) as $ctx) {
			
			$this->addParsedUrl($ctx->getUrl(), $ctx);
		
		}
	}

}

