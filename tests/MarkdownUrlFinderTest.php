<?php

use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\HtmlUrlFinder;
use TS\Web\UrlFinder\MarkdownUrlFinder;


class MarkdownUrlFinderTest extends TestCase
{


    public function testSimpleFind()
    {
        $this->finder->setDocument($this->document, 'http://localhost/test/index.md');

        //foreach ($this->finder->find() as $foundUrl) {
            //print $foundUrl->getUrl() . PHP_EOL;
            //print $foundUrl->getElementContext()->describe() . PHP_EOL;
            //print PHP_EOL;
        //}

        $this->assertCount(3, $this->finder->find());
    }


    /** @var string */
    private $document;

    /** @var HtmlUrlFinder */
    private $finder;

    /**
     * @before
     */
    public function setupSomeFixtures()
    {
        $this->document = file_get_contents(str_replace('.php', '.md', __FILE__));
        $this->finder = new MarkdownUrlFinder();
        $this->finder->setDocument($this->document, null);
        $this->finder->setDocumentUrl('http://domain.tld/catalog/example.md');
    }

}

