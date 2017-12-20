<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\Element\StringElement;

class StringElementTest extends TestCase
{

	public function testLineOf()
	{
		$s = join("\n", [
			'äbc',
			'def',
			'geh'
		]);
		
		list ($line, $char) = StringElement::getLineOf($s, 0);
		$this->assertSame(1, $line);
		$this->assertSame(1, $char);
		
		list ($line, $char) = StringElement::getLineOf($s, 2);
		$this->assertSame(1, $line);
		$this->assertSame(3, $char);
		
		list ($line, $char) = StringElement::getLineOf($s, 5);
		$this->assertSame(2, $line);
		$this->assertSame(2, $char);
		
		list ($line, $char) = StringElement::getLineOf($s, 0);
		$this->assertSame(1, $line);
		$this->assertSame(1, $char);
	
	}

	/**
	 * @exp
	 */
	public function testLineOfOutOfRange()
	{
		$this->expectException(OutOfRangeException::class);
		list ($line, $char) = StringElement::getLineOf('', 0);
	}

}

