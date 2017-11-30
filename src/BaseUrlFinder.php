<?php

namespace TS\Web\UrlFinder;


use TS\Web\UrlBuilder\Url;
use TS\Web\UrlFinder\Exception\DocumentException;
use TS\Web\UrlBuilder\InvalidUrlException;
use TS\Web\UrlFinder\Element\StringElement;
use TS\Web\UrlFinder\Context\DocumentContext;
use TS\Web\UrlFinder\Context\UrlCollection;
use TS\Web\UrlFinder\Context\FoundUrl;
use TS\Web\UrlFinder\Context\ElementContext;


abstract class BaseUrlFinder implements DocumentContext
{

	/**
	 *
	 * @var Url
	 */
	protected $documentUrl;

	/**
	 *
	 * @var string
	 */
	protected $document;

	/**
	 *
	 * @var bool
	 */
	private $isParsed = false;

	/**
	 *
	 * @var UrlCollection
	 */
	private $collection;

	/**
	 *
	 * @var FoundUrl[]
	 */
	private $parsingUrls;

	public function __construct()
	{}

	/**
	 * Set the document.
	 *
	 * @param mixed $document
	 * @param string|Url|NULL $url
	 * @throws \InvalidArgumentException
	 * @throws DocumentException
	 * @return self
	 */
	public function setDocument($document, $url)
	{
		if (is_null($document)) {
			throw new \InvalidArgumentException('Document is NULL.');
		}
		if (! $this->supportsDocument($document)) {
			throw new DocumentException('Document is not supported.');
		}
		$this->setDocumentUrl($url);
		$this->document = $document;
		$this->isParsed = false;
		return $this;
	}

	/**
	 * Set the URL of the document.
	 *
	 * @param string|Url|NULL $url
	 * @throws \InvalidArgumentException
	 * @throws InvalidUrlException
	 * @return self
	 */
	public final function setDocumentUrl($url)
	{
		if (is_null($url)) {
			$this->documentUrl = new Url();
		} else if ($url instanceof Url) {
			$this->documentUrl = $url;
		} else if (is_string($url)) {
			$this->documentUrl = new Url($url);
		} else {
			throw new \InvalidArgumentException('Unexpected type of url.');
		}
		if (! $this->documentUrl->isEmpty() && ! $this->documentUrl->isValid()) {
			throw new InvalidUrlException(sprintf('The document URL "%s" is invalid.', $this->documentUrl));
		}
		$this->isParsed = false;
		return $this;
	}

	/**
	 * Get the URL of the document.
	 *
	 * @see DocumentContext::getDocumentUrl()
	 */
	public final function getDocumentUrl()
	{
		return $this->documentUrl;
	}

	/**
	 * Get the document - with updated URLs if any URLs have changed.
	 *
	 * @throws InvalidUrlException
	 * @return mixed
	 */
	public final function getDocument()
	{
		$invalid = $this->find()->whereNot(function (FoundUrl $url) {
			return $url->isEmpty() || $url->isValid();
		});
		if (count($invalid) > 0) {
			throw new InvalidUrlException('Some URLs are invalid.');
		}
		return $this->replaceDocumentUrls($this->document, $this->find());
	}

	/**
	 * Find all URLs matching the pattern.
	 *
	 * If a document URL was set, all relative URLs are resolved to
	 * absolute URLs before comparison.
	 *
	 * This means that the URL '../scripts/jquery.js' in the document
	 * 'http://domain.tld/products/' will be matched by
	 * find('http://domain.tld/*')
	 *
	 * @param string $pattern
	 *        	A shell wildcard pattern, see http://php.net/manual/en/function.fnmatch.php
	 * @param int $opt
	 *        	By default we match by the orginal URL.
	 * @return UrlCollection
	 */
	public final function find($pattern = '*')
	{
		if (! $this->isParsed) {
			$this->parseDocument();
		}
		return $this->collection->find($pattern);
	}

	/**
	 * Implement this method and return true if the given document
	 * is supported.
	 *
	 * @param mixed $document
	 */
	abstract public function supportsDocument($document);

	/**
	 * Implement this method to parse the document.
	 * Register the URLs via addParsedUrl().
	 *
	 * @param mixed $document
	 * @param Url $documentUrl
	 */
	abstract protected function parseDocumentUrls($document, Url $documentUrl);

	/**
	 * Use this method to add parsed URLs from parseDocumentUrls.
	 *
	 * @param string $url
	 * @param ElementContext $context
	 * @throws \LogicException
	 */
	final protected function addParsedUrl($url, ElementContext $context)
	{
		if (is_null($this->parsingUrls)) {
			throw new \LogicException('');
		}
		$item = FoundUrl::create($url, $context, $this);
		$this->parsingUrls[] = $item;
	}

	/**
	 * Sort the parsed URLs by order of occurrence.
	 *
	 * The default implementation handles element contexts of type StringElement.
	 *
	 * @param array $items
	 * @throws \LogicException
	 */
	protected function sortParsedDocumentUrls(array & $items)
	{
		usort($items, function (FoundUrl $a, FoundUrl $b) {
			$a_ctx = $a->getElementContext();
			$b_ctx = $b->getElementContext();
			if (! $a_ctx instanceof StringElement) {
				$msg = sprintf('The default implementation of sortDocumentUrls can only handle subclasses of StringElement. Please override replaceDocumentUrls to handle ElementContext of type %s.', get_class($a_ctx));
				throw new \LogicException($msg);
			}
			if (! $b_ctx instanceof StringElement) {
				$msg = sprintf('The default implementation of sortDocumentUrls can only handle subclasses of StringElement. Please override replaceDocumentUrls to handle ElementContext of type %s.', get_class($b_ctx));
				throw new \LogicException($msg);
			}
			return $a->getElementContext()->getOffset() - $b->getElementContext()->getOffset();
		});
	}

	/**
	 * Replace the given URLs in a copy of the given document.
	 *
	 * The default implementation handles element contexts of type StringElement.
	 *
	 * @param mixed $document
	 * @param UrlCollection $urls
	 * @return mixed
	 */
	protected function replaceDocumentUrls($document, UrlCollection $urls)
	{
		$cursor = 0;
		$content = [];
		$items = $this->find()->toArray();
		usort($items, function (FoundUrl $a, FoundUrl $b) {
			return $a->getElementContext()->getOffset() - $b->getElementContext()->getOffset();
		});
		foreach ($items as $url) {
			$ctx = $url->getElementContext();
			if (! $ctx instanceof StringElement) {
				$msg = sprintf('The default implementation of replaceDocumentUrls can only handle subclasses of StringElement. Please override replaceDocumentUrls to handle ElementContext of type %s.', get_class($ctx));
				throw new \LogicException($msg);
			}
			$content[] = substr($document, $cursor, $ctx->getOffset() - $cursor);
			$content[] = $ctx->encodeUrl($url->__toString());
			$cursor = $ctx->getOffset() + $ctx->getLength();
		}
		$content[] = substr($document, $cursor);
		return join('', $content);
	}

	/**
	 *
	 * @throws DocumentException
	 */
	private function parseDocument()
	{
		if (is_null($this->document)) {
			throw new DocumentException('Trying to parse document, but document is NULL.');
		}
		$this->parsingUrls = [];
		$this->parseDocumentUrls($this->document, $this->documentUrl);
		foreach ($this->parsingUrls as $item) {
			if ($item instanceof FoundUrl === false) {
				throw new \LogicException('All elements returned from parseDocumentUrls() must be FoundUrl.');
			}
		}
		$this->sortParsedDocumentUrls($this->parsingUrls);
		$this->collection = new UrlCollection($this->parsingUrls);
		$this->parsingUrls = null;
		$this->isParsed = true;
	}

}

