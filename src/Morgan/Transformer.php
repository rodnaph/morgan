<?php

namespace Morgan;

use DOMDocument;
use DOMElement;

class Transformer
{
    /**
     * Apply the array of transformers to the specified element
     *
     * @param DOMElement $element
     * @param array $transformers
     */
    public static function apply(DOMElement $element, array $transformers)
    {
        foreach ($transformers as $transformer) {
            $transformer($element);
        }
    }

    /**
     * Return transformer to replace the content of the matched
     * elements with the specifed HTML fragment
     *
     * @param string $html
     *
     * @return Callable
     */
    public static function htmlContent($html)
    {
        return function(DOMElement $element) use ($html) {
            $dom = new DOMDocument();
            $dom->loadHTML($html);

            $owner = $element->ownerDocument;
            $node = $dom->documentElement->cloneNode($deepClone = true);

            $element->nodeValue = '';
            $element->appendChild(
                $owner->importNode($node, $deepClone = true)
            );
        };
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
        return function(DOMElement $element) use ($content) {
            $element->nodeValue .= $content;
        };
    }

    /**
     * Returns transformer to prepent content to elements
     *
     * @param string $content
     *
     * @return Callable
     */
    public static function prepend($content)
    {
        return function(DOMElement $element) use ($content) {
            $element->nodeValue = $content . $element->nodeValue;
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

    /**
     * Allows applying multiple transformers to a single selector
     *
     * @param Callable varargs...
     *
     * @return Callable
     */
    public static function do_()
    {
        $transformers = func_get_args();

        return function(DOMElement $element) use ($transformers) {
            Transformer::apply($element, $transformers);
        };
    }
}
