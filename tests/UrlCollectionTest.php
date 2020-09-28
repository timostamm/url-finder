<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Context\DocumentContext;
use TS\Web\UrlFinder\Context\FoundUrl;
use TS\Web\UrlFinder\Context\UrlCollection;
use TS\Web\UrlFinder\Mock\MockElementContext;

class UrlCollectionTest extends TestCase
{

	public function testCount()
	{
		$this->assertCount(9, $this->collection);
	}

	public function testFluid()
	{
		$c = $this->collection->onlyHttp()
			->matchHost('domain.*')
			->matchPath('/catalog/*')
			->matchFilename('*custom*')
			->matchFilenameNot('*.js');
		$this->assertCount(1, $c);
	}

	public function testFind()
	{
		$this->assertCount(9, $this->collection->find('*'));
		$this->assertCount(3, $this->collection->find('*.css'));
		$this->assertCount(6, $this->collection->find('*.js'));
		$this->assertCount(0, $this->collection->find('/catalog/*'));
		$this->assertCount(3, $this->collection->find('*/catalog/*'));
		$this->assertCount(1, $this->collection->find('*/catalog/*.css'));
	}

	public function testMatchFile()
	{
		$this->assertCount(2, $this->collection->matchFilename('script.js'));
		$this->assertCount(6, $this->collection->matchFilename('*.js'));
		
		// should always be equivalent to find('*/<file-pattern>')
		$this->assertCount(2, $this->collection->find('*/script.js'));
		$this->assertCount(6, $this->collection->find('*/*.js'));
	}

	public function testMatchFileNot()
	{
		$this->assertCount(6, $this->collection->matchFilenameNot('*.css'));
	}

	public function testMatchPath()
	{
		$this->assertCount(3, $this->collection->matchPath('/catalog/*'));
		$this->assertCount(1, $this->collection->matchPath('/catalog/*.css'));
	}

	public function testMatchPathNot()
	{
		$this->assertCount(6, $this->collection->matchPathNot('*.css'));
	}

	public function testMatchHost()
	{
		$this->assertCount(6, $this->collection->matchHost('domain.com'));
		$this->assertCount(3, $this->collection->matchHost('cdn.com'));
	}

	public function testMatchHostNot()
	{
		$this->assertCount(3, $this->collection->matchHostNot('domain.com'));
	}

	public function testMatchScheme()
	{
		$this->assertCount(8, $this->collection->matchScheme('http'));
		$this->assertCount(1, $this->collection->matchScheme('https'));
		$this->assertCount(9, $this->collection->matchScheme('http*'));
	}

	public function testMatchSchemeNot()
	{
		$this->assertCount(1, $this->collection->matchSchemeNot('http'));
	}

	public function testOnlyHttp()
	{
		$this->assertCount(8, $this->collection->onlyHttp());
	}

	public function testOnlyHttps()
	{
		$this->assertCount(1, $this->collection->onlyHttps());
	}

	/**
	 *
	 * @var \TS\Web\UrlFinder\Context\UrlCollection
	 */
	private $collection;

	/**
	 * @before
	 */
	public function setup(): void
	{
		$base = new Url('http://domain.com/catalog/products.html');
		$doc = new DocumentContext($base, null, null);
		$ele = new MockElementContext();
		$items = [
			FoundUrl::create('/assets/style.css', $ele, $doc),
			FoundUrl::create('../assets/style.css', $ele, $doc),
			FoundUrl::create('./script.js', $ele, $doc),
			FoundUrl::create('other-custom-products-module.js', $ele, $doc),
			FoundUrl::create('styles/custom-products-module.css', $ele, $doc),
			FoundUrl::create('http://domain.com/assets/script.js', $ele, $doc),
			FoundUrl::create('https://cdn.com/jquery.js', $ele, $doc),
			FoundUrl::create('http://cdn.com/lib.js', $ele, $doc),
			FoundUrl::create('//cdn.com/angular.js', $ele, $doc)
		];
		$this->collection = new UrlCollection($items);
	}

	/**
	 * @after
	 */
	public function teardown(): void
	{
		$this->collection = null;
	}

}

