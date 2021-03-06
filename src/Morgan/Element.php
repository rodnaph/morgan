<?php

namespace Morgan;

use DOMDocument;
use DOMElement;

class Element
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
     * Returns all the classes from the specified element without
     * the given class $name
     *
     * @param DOMElement $element
     * @param array $classes
     *
     * @return array
     */
    public static function withoutClasses(DOMElement $element, array $classes)
    {
        return array_diff(Element::classesFor($element), $classes);
    }

    /**
     * Set the specified classes on the element
     *
     * @param DOMElement $element
     * @param array $classes
     */
    public static function setClasses(DOMElement $element, array $classes)
    {
        $element->setAttribute(
            'class',
            implode(' ', $classes)
        );
    }

    /**
     * Return a new DOMDocumentFragment for the specified HTML
     *
     * @param string $html
     *
     * @return DOMDocumentFragment
     */
    public static function forHtmlIn($html, DOMDocument $target)
    {
        $newElement = $target->createDocumentFragment();
        $newElement->appendXML($html);

        return $newElement;
    }

    /**
     * Return an array of classes for a particular element
     *
     * @param DOMElement $element
     *
     * @return array
     */
    protected static function classesFor(DOMElement $element)
    {
        $classString = $element->getAttribute('class');
        $classes = preg_split('/\s+/', $classString);

        return array_filter(array_unique($classes));
    }
}
