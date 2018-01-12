<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\Element\Css\UrlNotation;
use TS\Web\UrlFinder\Element\StringElement;

class UrlNotationTest extends TestCase
{

	private $string = <<<EOT
.style { 
  background-image: url(img.jpg); 
}
EOT;

	public function testCount()
	{
		$this->assertCount(1, $this->found);
	}
	
	public function testDescribe()
	{
		$this->assertEquals('url(…)', $this->first->describe());
	}
	
	public function testOffset()
	{
		$o = $this->first->getOffset();
		$this->assertSame(34, $o);
	}
	
	public function testLineOf()
	{
		$o = $this->first->getOffset();
		list($line, $char) = StringElement::getLineOf($this->string, $o);
		$this->assertSame(2, $line);
		$this->assertSame(25, $char);
	}
	
	/**
	 *
	 * @var Generator
	 */
	private $found;

	/**
	 *
	 * @var UrlNotation
	 */
	private $first;

	protected function setUp()
	{
		$g = UrlNotation::find($this->string);
		$this->found = iterator_to_array($g, false);
		$this->first = isset($this->found[0]) ? $this->found[0] : null;
	}

}

