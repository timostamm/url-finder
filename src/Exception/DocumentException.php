<?php

namespace TS\Web\UrlFinder\Exception;


class DocumentException extends \DomainException
{

	public function __construct($message)
	{
		parent::__construct($message);
	}

}

