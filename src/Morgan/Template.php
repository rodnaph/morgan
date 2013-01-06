<?php

namespace Morgan;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelector;

class Template extends Transformer
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $selector;

    /**
     * {@inheritDoc}
     */
    public static function render($path, array $transformers, $selector = null)
    {
        echo self::fetch($path, $transformers);
    }

    /**
     * {@inheritDoc}
     */
    public static function fetch($path, array $transformers, $selector = null)
    {
        $t = new Template($path, $selector);

        return $t->html($transformers);
    }

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
     * Perform a CSS selector query on the document and return the
     * matched elements
     *
     * @param DOMDocument $dom
     * @param string $selector
     *
     * @return array
     */
    protected function query(DOMDocument $dom, $selector)
    {
        $query = CssSelector::toXPath($selector);
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query($query);

        return $elements;
    }

    /**
     * Return the DOMDocument to operate transformations on
     *
     * @return DOMDocument
     */
    protected function getDOMDocument()
    {
        $source = new DOMDocument();
        $source->loadHTMLFile($this->path);

        return $this->selector
            ? self::snippet($source, $this->selector)
            : $source;
    }


    /**
     * Return a snippet from the specified source document
     *
     * @param DOMDocument $source
     *
     * @return DOMDocument
     */
    protected static function snippet(DOMDocument $source, $selector)
    {
        $dom = new DOMDocument;
        $elements = $this->query($source, $selector);

        foreach ($elements as $element) {
            $dom->appendChild(
                $dom->importNode($element, $deepClone = true)
            );
        }

        return $dom;
        $dom = new DOMDocument();
        $dom->loadHTMLFile($this->path);

        return $dom;
    }

    /**
     * @param string $path
     */
    protected function __construct($path, $selector)
    {
        $this->path = $path;
        $this->selector = $selector;
    }

    /**
     * Return the HTML content for a template
     *
     * @param array $transformers
     *
     * @return string
     */
    protected function html(array $transformers)
    {
        $dom = $this->getDOMDocument();

        foreach ($transformers as $selector => $transformer) {
            $elements = $this->query($dom, $selector);

            foreach ($elements as $element) {
                self::apply($element, array($transformer));
            }
        }

        return $dom->saveHTML();
    }
}
