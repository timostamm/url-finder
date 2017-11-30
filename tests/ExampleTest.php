<?php
use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\UrlFinder;

class ExampleTest extends TestCase
{

	private static $html = <<<HTML
<html>
<body>
  <img src="http://domain.tld/img/a.jpg" >
  <div style="background-image: url(./c.jpg);"></div>
  <style>
    .bg-img { background-image: url(../images/h.jpg); }
  </style>
  <script src="https://cdn.tld/angular.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/styles/f.css" />
  ...
HTML;

	private static $result = <<<HTML
<html>
<body>
  <img src="/images/a.jpg" >
  <div style="background-image: url(/images/c.jpg);"></div>
  <style>
    .bg-img { background-image: url(/images/h.jpg); }
  </style>
  <script src="https://cdn.tld/angular.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/styles/f.css" />
  ...
HTML;

	public function testStory()
	{
		$documentUrl = 'http://domain.tld/products/all.html';
		$finder = UrlFinder::create(self::$html, $documentUrl);
		
		foreach ($finder->find('*domain.tld/*.jpg') as $url) {
			$newpath = '/images/' . $url->path->filename();
			$url
				->replacePath($newpath)
				->makeAbsolute()
				->clearHost();
		}
		
		$doc = $finder->getDocument(); // returns the updated HTML string
		$this->assertEquals(self::$result, $doc);
	}

}

