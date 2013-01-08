<?php

namespace Morgan;

use DOMDocument;
use DOMElement;

class Transformer
{
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
        $transformer = function(DOMElement $element) use ($html) {
            $dom = new DOMDocument();
            $dom->loadHTML($html);

            $node = $dom->createDocumentFragment();
            $node->appendXML($html);

            $element->nodeValue = '';
            $element->appendChild(
                $element->ownerDocument->importNode(
                    $node,
                    $deepClone = true
                )
            );
        };

        return $html ? $transformer : Transformer::noop();
    }

    /**
     * Returns transformer which does nothing
     *
     * @return Callable
     */
    public static function noop()
    {
        return function() {};
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
            Element::apply($element, $transformers);
        };
    }

    /**
     * Return transformer to add a class to an element
     *
     * @param string $name
     *
     * @return Callable
     */
    public static function addClass($name)
    {
        return function(DOMElement $element) use ($name) {
            $classes = Element::withoutClass($element, $name);

            array_push($classes, $name);

            Element::setClasses($element, $classes);
        };
    }

    /**
     * Return transformer to remove a class from an element
     *
     * @param string $name
     *
     * @return Callable
     */
    public static function removeClass($name)
    {
        return function(DOMElement $element) use ($name) {
            Element::setClasses(
                $element,
                Element::withoutClass($element, $name)
            );
        };
    }
}
