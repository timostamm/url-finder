<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Context\DocumentContext;
use TS\Web\UrlFinder\Context\FoundUrl;
use TS\Web\UrlFinder\Mock\MockElementContext;
use TS\Web\UrlFinder\Context\ElementContext;

class FoundUrlTest extends TestCase
{

	public function testUrl()
	{
		$this->assertEquals('../assets/style.css', $this->url->__toString());
	}

	public function testGetAbsoluteUrl()
	{
		$this->assertEquals('http://domain.com/assets/style.css', $this->url->getAbsoluteUrl()
			->__toString());
	}

	public function testGetOriginalUrl()
	{
		$this->assertEquals('../assets/style.css', $this->url->getOriginalUrl()
			->__toString());
	}

	public function testGetDocumentElement()
	{
		$this->assertEquals($this->eleContext, $this->url->getElementContext());
	}

	public function testGetDocumentBaseUrl()
	{
		$this->assertEquals($this->base, $this->url->getDocumentContext()
			->getUrl());
	}

	public function testHasChanged()
	{
		$this->assertFalse($this->url->hasChanged());
		$this->url->path->set('/new-path');
		$this->assertTrue($this->url->hasChanged());
	}

	public function testRevertChanges()
	{
		$this->url->path->set('/new-path');
		$this->assertTrue($this->url->hasChanged());
		$this->url->revertChanges();
		$this->assertFalse($this->url->hasChanged());
	}

	public function testAbsoluteUrlImmutable()
	{
		$abs = $this->url->getAbsoluteUrl();
		$abs->path->set('/new-path');
		$this->assertFalse($this->url->getAbsoluteUrl()
			->equals($abs));
	}

	public function testOriginalUrlImmutable()
	{
		$ori = $this->url->getOriginalUrl();
		$ori->path->set('/new-path');
		$this->assertFalse($this->url->getOriginalUrl()
			->equals($ori));
	}

	public function testMakeAbsolute()
	{
		$this->url->makeAbsolute();
		$this->assertEquals('http://domain.com/assets/style.css', $this->url->__toString());
	
	}

	public function testMakeAbsolutePath()
	{
		$this->url->makeAbsolutePath();
		$this->assertEquals('/assets/style.css', $this->url->__toString());
	}

	/**
	 *
	 * @var FoundUrl
	 */
	private $url;

	/**
	 *
	 * @var Url
	 */
	private $base;

	/**
	 *
	 * @var DocumentContext
	 */
	private $docContext;

	/**
	 *
	 * @var ElementContext
	 */
	private $eleContext;

	/**
	 * @before
	 */
	public function setup(): void
	{
		$this->base = new Url('http://domain.com/catalog/products.html');
		$this->docContext = new DocumentContext($this->base, null, null);
		$this->eleContext = new MockElementContext();
		$this->url = FoundUrl::create('../assets/style.css', $this->eleContext, $this->docContext);
	}

	/**
	 * @after
	 */
	public function teardown(): void
	{
		$this->url = null;
	}

}
