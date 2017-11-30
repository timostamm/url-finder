<?php

namespace TS\Web\UrlFinder\Mock;


use TS\Web\UrlFinder\Context\ElementContext;


class MockElementContext implements ElementContext
{

	public function describe()
	{
		return MockElementContext::class;
	}

}
