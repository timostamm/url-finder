<?php

namespace TS\Web\UrlFinder\Context;


use Countable;
use IteratorAggregate;
use Traversable;


class UrlCollection implements Countable, IteratorAggregate
{

	const PATTERN_ALL = '*';

	/**
	 *
	 * @var FoundUrl[]
	 */
	private $items;

	/**
	 *
	 * @param FoundUrl[] $items
	 */
	public function __construct(array $items)
	{
		$this->items = $items;
	}

	/**
	 *
	 * @param string $pattern
	 *        	A shell wildcard pattern, see http://php.net/manual/en/function.fnmatch.php
	 *
	 * @return static|FoundUrl[]
	 */
	public function find($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return $this;
		}
		return $this->where(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()
				->__toString());
		});
	}

	/**
	 * Matches all URLs whose filename part match the given pattern.
	 *
	 * This is equivalent to find('*\/<file-pattern>')
	 *
	 * @param string $pattern
	 * @return static|FoundUrl[]
	 */
	public function matchFilename($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return $this;
		}
		return $this->where(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->path->filename());
		});
	}

	/**
	 * Matches all URLs whose filename part do NOT match the given pattern.
	 *
	 * @param string $pattern
	 * @return static
	 */
	public function matchFilenameNot($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return new static([]);
		}
		return $this->whereNot(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->path->filename());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static
	 */
	public function matchPath($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return $this;
		}
		return $this->where(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->path->get());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static
	 */
	public function matchPathNot($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return new static([]);
		}
		return $this->whereNot(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->path->get());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static|FoundUrl[]
	 */
	public function matchHost($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return $this;
		}
		return $this->where(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->host->get());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static|FoundUrl[]
	 */
	public function matchHostNot($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return new static([]);
		}
		return $this->whereNot(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->host->get());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static|FoundUrl[]
	 */
	public function matchScheme($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return $this;
		}
		return $this->where(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->scheme->get());
		});
	}

	/**
	 *
	 * @param string $pattern
	 * @return static|FoundUrl[]
	 */
	public function matchSchemeNot($pattern)
	{
		if ($pattern == self::PATTERN_ALL) {
			return new static([]);
		}
		return $this->whereNot(function (FoundUrl $item) use ($pattern) {
			return fnmatch($pattern, $item->getAbsoluteUrl()->scheme->get());
		});
	}

	/**
	 *
	 * @return static|FoundUrl[]
	 */
	public function onlyHttp()
	{
		return $this->where(function (FoundUrl $item) {
			return $item->getAbsoluteUrl()->scheme->get() === 'http';
		});
	}

	/**
	 *
	 * @return static|FoundUrl[]
	 */
	public function onlyHttps()
	{
		return $this->where(function (FoundUrl $item) {
			return $item->getAbsoluteUrl()->scheme->get() === 'https';
		});
	}

	/**
	 *
	 * @param callable $fn
	 * @return static|FoundUrl[]
	 */
	public function where(callable $fn)
	{
		$matches = [];
		foreach ($this->items as $item) {
			$ok = $fn($item);
			if ($ok === true) {
				$matches[] = $item;
			}
		}
		return new static($matches);
	}

	/**
	 *
	 * @param callable $fn
	 * @return static|FoundUrl[]
	 */
	public function whereNot(callable $fn)
	{
		$matches = [];
		foreach ($this->items as $item) {
			$ok = $fn($item);
			if ($ok === true) {
				// pass
			} else {
				$matches[] = $item;
			}
		}
		return new static($matches);
	}

	/**
	 * Get the first item.
	 *
	 * @throws \DomainException
	 * @return FoundUrl
	 */
	public function first()
	{
		if ($this->count() == 0) {
			throw new \DomainException('Cannot get first item, collection is empty.');
		}
		return $this->items[0];
	}

	/**
	 * Get the first item or null if the collection is empty.
	 *
	 * @return FoundUrl|NULL
	 */
	public function firstOrNull()
	{
		if ($this->count() == 0) {
			return null;
		}
		return $this->items[0];
	}

	/**
	 *
	 * @return FoundUrl[]
	 */
	public function toArray()
	{
		return array_values($this->items);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see Countable::count()
	 */
	public function count(): int
	{
		return count($this->items);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator(): Traversable
	{
		return new \ArrayIterator($this->items);
	}

}

