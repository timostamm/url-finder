<?php


namespace TS\Web\UrlFinder;

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\Element\Markdown\MdLinkNotation;

class MdLinkNotationTest extends TestCase
{


    public function testFind()
    {
        $gen = MdLinkNotation::find('aaa [hello](http://hello)');
        $arr = iterator_to_array($gen);
        $this->assertCount(1, $arr);

        /** @var MdLinkNotation $l */
        $l = $arr[0];
        $this->assertInstanceOf(MdLinkNotation::class, $l);

        $this->assertEquals('hello', $l->getAltText());
        $this->assertEquals('http://hello', $l->getUrl());
    }


    public function testFindIgnoresImages()
    {
        $gen = MdLinkNotation::find('aaa ![alt text](http://hello.png)');
        $arr = iterator_to_array($gen);
        $this->assertCount(0, $arr);
    }

}

