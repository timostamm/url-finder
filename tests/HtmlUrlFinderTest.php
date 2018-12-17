<?php
use PHPUnit\Framework\TestCase;
use TS\Web\UrlFinder\Context\FoundUrl;
use TS\Web\UrlFinder\HtmlUrlFinder;
use TS\Filesystem\Path;

class HtmlUrlFinderTest extends TestCase
{

    private static $document = <<<HTML
<html>
<body>
	<img src="assets/img/a.jpg" >
	<img width="400" src="assets/img/b.jpg" height="20" >
    <div style="background-image: url(c.jpg);"></div>
    <div style="background-image: url(d.jpg) url('e.jpg');"></div>
	<link rel="stylesheet" type="text/css" href="f.css" />
	<link type="text/css" href="g.css" rel="stylesheet" />
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
    <div foo-style="background-image: url(c.jpg);"></div>
	<script foo-src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
	<img foo-src="assets/img/a.jpg" >
	<link type="text/css" foo-href="g.css" rel="stylesheet" />
	<style>
		.bg-img {
			background-image: url(h.jpg);
		}
	</style>
</body>
</html>
HTML;

    private static $document_wo_links = <<<HTML
<html>
<body>
    <div foo-style="background-image: url(c.jpg);"></div>
	<script foo-src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
	<img foo-src="assets/img/a.jpg" >
	<link type="text/css" foo-href="g.css" rel="stylesheet" />
</body>
</html>
HTML;

    public function testStory()
    {
        $this->finder->setDocumentUrl('http://domain.tld/catalog/products.html');
        
        $style = Path::info('/assets/style/');
        $images = Path::info('/assets/images/');
        $scripts = Path::info('/assets/scripts/');

        $myUrls = $this->finder->find()->matchHost('domain.tld');

        foreach ($myUrls as $url) {
            $url->makeAbsolutePath();
        }
        
        foreach ($myUrls->matchFilename('*.jpg') as $url) {
            $url->replacePath($images->resolve($url->path->filename()));
        }
        
        foreach ($myUrls->matchFilename('*.css') as $url) {
            $url->replacePath($style->resolve($url->path->filename()));
        }
        
        foreach ($myUrls->matchFilename('*.js') as $url) {
            $url->replacePath($scripts->resolve($url->path->filename()));
        }
        
        foreach ($this->finder->find()->matchHost('*google*')->matchFilename('angular*.js') as $url) {
            $url->clearHost()->replacePath($scripts->resolve($url->path->filename()));
        }
        
        $this->assertEquals(
            $this->finder->find()->count(), 
            $this->finder->find()->matchPath('/assets/*/*')->count()
        );
    }

    public function testFindUsesCurrentAbsoluteUrl()
    {
        $this->finder->setDocumentUrl('http://domain.tld/catalog/products.html');
        $images = $this->finder->find('*.jpg');
        foreach ($images as $url) {
            $url->replacePath('../images/' . $url->path->filename());
        }
        $urlsInImagePath = $this->finder->find()->matchPath('/images/*');
        $this->assertEquals($images->count(), $urlsInImagePath->count());
    }

    public function testGetDocument()
    {
        $this->assertEquals(self::$document, $this->finder->getDocument());
    }

    public function testDocumentChange()
    {
        $document = '<img src="img/test.png?x=y">';
        $this->finder->setDocument($document, 'http://localhost/test/index.html');
        foreach ($this->finder->find()->matchFilename('*.png') as $url) {
            $url->replacePath('/assets/' . $url->path->filename());
        }
        $this->assertNotEquals($document, $this->finder->getDocument());
    }

    public function testSimpleFind()
    {
        $this->finder->setDocument('<img src="img/test.png">', 'http://localhost/test/index.html');
        $this->assertCount(1, $this->finder->find());
    }

    public function testAbsoluteUrlCount()
    {
        $urls = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->isAbsolute();
        });
        $this->assertCount(9, $this->finder->find());
        $this->assertCount(1, $urls);
    }

    public function testRelativeUrlCount()
    {
        $urls = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->isRelative();
        });
        $this->assertCount(8, $urls);
    }

    public function testFoundUrlEqualsOriginalUrl()
    {
        $equalCount = $this->finder->find()
            ->where(function (FoundUrl $u) {
            return $u->__toString() === $u->getOriginalUrl()
                ->getUrl();
        })
            ->count();
        $allCount = $this->finder->find()->count();
        $this->assertEquals($allCount, $equalCount);
    }

    public function testWithoutBaseUrl()
    {
        $this->finder->setDocumentUrl(null);
        $relativeUrls = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->isRelative();
        });
        $absoluteUrlsThatAreRelative = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->getAbsoluteUrl()
                ->isRelative();
        });
        $this->assertEquals($relativeUrls->count(), $absoluteUrlsThatAreRelative->count());
    }

    public function testAbsoluteOriginalUrlCount()
    {
        $urls = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->getOriginalUrl()
                ->isAbsolute();
        });
        $this->assertCount(1, $urls);
    }

    public function testRelativeOriginalUrlCount()
    {
        $urls = $this->finder->find()->where(function (FoundUrl $u) {
            return $u->getOriginalUrl()
                ->isRelative();
        });
        $this->assertCount(8, $urls);
    }

    public function testParseResult()
    {
        $this->assertCount(9, $this->finder->find());
        
        $refs = $this->finder->find()->toArray();
        $this->assertEquals('assets/img/a.jpg', $refs[0]->__toString());
        $this->assertEquals('assets/img/b.jpg', $refs[1]->__toString());
        $this->assertEquals('c.jpg', $refs[2]->__toString());
        $this->assertEquals('d.jpg', $refs[3]->__toString());
        $this->assertEquals('e.jpg', $refs[4]->__toString());
        $this->assertEquals('f.css', $refs[5]->__toString());
        $this->assertEquals('g.css', $refs[6]->__toString());
        $this->assertEquals('https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js', $refs[7]->__toString());
        $this->assertEquals('h.jpg', $refs[8]->__toString());
    }

    public function testParserNoMatch()
    {
        $this->assertCount(0, $this->finder->setDocument(self::$document_wo_links, null)
            ->find());
    }

    public function testAttributeEncoding()
    {
        $this->finder->setDocument('<img src="foo?x=y&amp;a=b" >', null);
        $url = $this->finder->find()->first();
        $this->assertEquals('foo?x=y&a=b', $url->__toString());
    }
    
    public function testOFfset()
    {
    		$url = $this->finder->setDocument('<img src="foo">', null)->find()->first();
	    	$this->assertEquals(10, $url->getElementContext()->getOffset());
    }
    
    public function testDescribe()
    {
    		$url = $this->finder->setDocument('<img src="foo">', null)->find()->first();
        $this->assertEquals('<img src="…">', $url->getElementContext()->describe());
    }

    /**
     *
     * @var HtmlUrlFinder
     */
    private $finder;

    /**
     * @before
     */
    public function setupSomeFixtures()
    {
        $this->finder = new HtmlUrlFinder();
        $this->finder->setDocument(self::$document, null);
        $this->finder->setDocumentUrl('http://domain.tld/catalog/products.html');
    }

    /**
     * @before
     */
    public function setupSomeOtherFixtures()
    {
        $this->finder = null;
    }
}

