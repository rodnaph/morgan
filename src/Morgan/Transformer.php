<?php

namespace Morgan;

use DOMElement;

class Transformer
{
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
            Template::apply($element, $transformers);
        };
    }
}
