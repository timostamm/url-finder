<?php


namespace TS\Web\UrlFinder;

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\Element\Markdown\MdImageNotation;

class MdImageNotationTest extends TestCase
{


    public function testFind()
    {
        $gen = MdImageNotation::find('aaa ![alt text](http://hello.png)');
        $arr = iterator_to_array($gen);
        $this->assertCount(1, $arr);

        /** @var MdImageNotation $l */
        $l = $arr[0];
        $this->assertInstanceOf(MdImageNotation::class, $l);

        $this->assertEquals('alt text', $l->getAltText());
        $this->assertEquals('http://hello.png', $l->getUrl());
    }


    public function testFindIgnoresLinks()
    {
        $gen = MdImageNotation::find('aaa [hello](http://hello)');
        $arr = iterator_to_array($gen);
        $this->assertCount(0, $arr);
    }

}

