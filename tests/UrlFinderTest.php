<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\UrlFinder;

class UrlFinderTest extends TestCase
{

	public function test()
	{
		
		$finder = UrlFinder::create('<img src="img/test.png">', 'http://localhost/test/index.html');
		
		$this->assertCount(1, $finder->find());
	}
}

