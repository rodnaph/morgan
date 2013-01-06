<?php

namespace Morgan;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelector;

class Template implements Renderable
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $transformers)
    {
        echo $this->fetch($transformers);
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(array $transformers)
    {
        $dom = $this->getDOMDocument();

        foreach ($transformers as $selector => $transformer) {
            $query = CssSelector::toXPath($selector);
            $xpath = new DOMXPath($dom);
            $nodes = $xpath->query($query);

            foreach ($nodes as $node) {
                $transformer($node);
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Return the DOMDocument to operate transformations on
     *
     * @return DOMDocument
     */
    protected function getDOMDocument()
    {
        $dom = new DOMDocument();
        $dom->loadHTMLFile($this->path);

        return $dom;
    }

    /**
     * Returns a transformer function for setting the content
     * of a matched element.
     *
     * @param string $content
     *
     * @return Callable
     */
    public static function content($content)
    {
        return function(DOMElement $element) use ($content) {
            $element->nodeValue = $content;
        };
    }

    /**
     * Returns a transformer for appending content to matched
     * elements
     *
     * @param string $content
     *
     * @return Callable
     */
    public static function append($content)
    {
        return function(DOMElement $element) use ($content)
        {
            $element->nodeValue .= $content;
        };
    }

    /**
     * Returns a transformer function for setting attributes
     * on matched elements
     *
     * @param string $name
     * @param string $value
     *
     * @return Callable
     */
    public static function setAttr($name, $value)
    {
        return function(DOMElement $element) use ($name, $value) {
            $element->setAttribute($name, $value);
        };
    }

    /**
     * Returns a transformer to remove an attribute from the
     * matched element
     *
     * @param string $name
     *
     * @return Callable
     */
    public static function removeAttr($name)
    {
        return function(DOMElement $element) use ($name) {
            $element->removeAttribute($name);
        };
    }
}
