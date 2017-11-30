<?php

namespace TS\Web\UrlFinder\Context;


use TS\Web\UrlBuilder\InvalidUrlException;
use TS\Web\UrlBuilder\Url;


class FoundUrl extends Url implements UrlContext, DocumentContext
{

	/**
	 *
	 * @var Url
	 */
	private $absolute;

	/**
	 *
	 * @var Url
	 */
	private $original;

	/**
	 *
	 * @var DocumentContext
	 */
	private $documentContext;

	/**
	 *
	 * @var ElementContext
	 */
	private $elementContext;

	/**
	 *
	 * @param string $original_url
	 * @param ElementContext $elementContext
	 * @param DocumentContext $documentContext
	 * @return FoundUrl
	 */
	public static function create($original_url, ElementContext $elementContext, DocumentContext $documentContext)
	{
		
		$u = new FoundUrl($original_url);
		$u->documentContext = $documentContext;
		$u->elementContext = $elementContext;
		$u->original = new Url($original_url);
		
		return $u;
	
	}

	public function __construct($url)
	{
		parent::__construct($url);
	}

	/**
	 * Make the URL absolute to the document base URL.
	 *
	 * @param Url|NULL $base
	 * @see UrlContext::makeAbsolute()
	 * @see Url::makeAbsolute()
	 * @return self
	 */
	public function makeAbsolute($base = null)
	{
		if (is_null($base)) {
			
			$base = $this->getDocumentUrl();
			
			if (! $base->isEmpty()) {
				
				if ($base->isAbsolute()) {
					
					parent::makeAbsolute($base);
				
				} else if ($base->path->isAbsolute()) {
					
					parent::makeAbsolutePath($base);
				
				} else {
					$msg = sprintf('Cannot create absolute URL for "%s" because the document URL "%s" does not have an absolute path.', $this, $base);
					throw new InvalidUrlException($msg);
				}
				
				$this->path->normalize();
			}
		
		} else {
			parent::makeAbsolute($base);
		}
		return $this;
	}

	/**
	 * Make the path of the URL absolute to the document base URL.
	 *
	 * @param Url|NULL $base
	 * @see UrlContext::makeAbsolutePath()
	 * @see Url::makeAbsolutePath()
	 * @return self
	 */
	public function makeAbsolutePath($base = null)
	{
		if (is_null($base)) {
			
			$base = $this->getDocumentUrl();
			
			if (! $base->isEmpty()) {
				
				if ($base->path->isAbsolute()) {
					
					parent::makeAbsolutePath($base);
					
					$this->path->normalize();
				
				} else {
					$msg = sprintf('Cannot create absolute URL for "%s" because the document URL "%s" does not have an absolute path.', $this, $base);
					throw new InvalidUrlException($msg);
				}
			}
		
		} else {
			parent::makeAbsolutePath($base);
		}
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see UrlContext::hasChanged()
	 */
	public function hasChanged()
	{
		return ! $this->equals($this->original);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see UrlContext::revertChanges()
	 */
	public function revertChanges()
	{
		$this->replace($this->original);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see DocumentContext::getDocumentUrl()
	 */
	public function getDocumentUrl()
	{
		return $this->documentContext->getDocumentUrl();
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see UrlContext::getElementContext()
	 */
	public function getElementContext()
	{
		return $this->elementContext;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see UrlContext::getAbsoluteUrl()
	 */
	public function getAbsoluteUrl()
	{
		$abs = clone $this;
		
		$base = $this->getDocumentUrl();
		
		if (! $base->isEmpty()) {
			if ($base->isAbsolute()) {
				$abs->makeAbsolute($base);
			} else if ($base->path->isAbsolute()) {
				$abs->makeAbsolutePath($base);
			} else {
				$msg = sprintf('Cannot create absolute URL for "%s" because the document URL "%s" does not have an absolute path.', $this, $base);
				throw new InvalidUrlException($msg);
			}
		}
		
		$abs->path->normalize();
		
		return $abs;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see UrlContext::getOriginal<Url()
	 */
	public function getOriginalUrl()
	{
		return clone $this->original;
	}

}

