<?php

namespace TS\Web\UrlFinder\Element\Markdown;


use TS\Web\UrlFinder\Context\ElementContext;
use TS\Web\UrlFinder\Element\StringElement;


class MdImageNotation extends StringElement implements ElementContext
{

    const LINK = <<<REGEX
/!\[([^\[]+)\]\(([^\)]+)\)/u
REGEX;

    public static function find($string)
    {
        preg_match_all(self::LINK, $string, $url_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($url_matches as $match) {
            $raw_url = $match[2][0];
            if (strpos($raw_url, ' ') !== false) {
                throw new \LogicException('Link URL contains whitespace. Titles are not supported right now. ' . $raw_url);
            }
            $offset = $match[2][1];
            $label = $match[1][0];
            yield new MdImageNotation($raw_url, $offset, $label);
        }

    }


    /**
     *
     * @var string
     */
    private $raw_url;


    /**
     *
     * @var string
     */
    private $altText;


    public function __construct($raw_url, $offset, $altText)
    {
        parent::__construct($offset, strlen($raw_url));
        $this->raw_url = $raw_url;
        $this->altText = $altText;
    }

    /**
     *
     * {@inheritdoc}
     * @see StringElement::encodeUrl()
     */
    public function encodeUrl($url)
    {
        return $url;
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     *
     * {@inheritdoc}
     * @see StringElement::getUrl()
     */
    public function getUrl()
    {
        return $this->raw_url;
    }

    public function describe()
    {
        return '![' . $this->altText . '](' . $this->raw_url . ')';
    }

}


