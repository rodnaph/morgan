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
     * Echo the result of applying the arguments to fetch()
     *
     * @param string $path
     * @param array $transformers
     * @param string $selector Optional
     */
    public static function render($path, array $transformers = array(), $selector = null)
    {
        echo self::fetch($path, $transformers, $selector);
    }

    /**
     * Returns the result of applying the transformer functions to the
     * HTML document at the specified path.  You can optionally limit to a
     * fragment inside the document with the $selector
     *
     * @param string $path
     * @param array $transformers Optional
     * @param string $selector Optional
     *
     * @return string
     */
    public static function fetch($path, array $transformers = array(), $selector = null)
    {
        $t = new Template($path, $selector);

        return $t->html($transformers);
    }

    /**
     * Map a snippet over the array of items and return a content
     * transformer for the result
     *
     * @param Callable $snippet
     * @param array $items
     *
     * @return Callable
     */
    public static function map($snippet, array $items)
    {
        return self::htmlContent(
            implode(
                '',
                array_map($snippet, $items)
            )
        );
    }

    /**
     * Create a snippet function for the fragment selected from
     * the specified file.  The return value is then callable with some
     * data than the $handler expects.  The handler then needs to
     * return an array of selector/transformer key values pairs
     *
     * @param string $path
     * @param string $selector
     * @param Callable $handler Optional
     *
     * @return Callable
     */
    public static function snippet($path, $selector, $handler = null)
    {
        return function () use ($path, $selector, $handler) {
            $args = func_get_args();

            return Template::fetch(
                $path,
                $handler ? call_user_func_array($handler, $args) : array(),
                $selector
            );
        };
    }

    /**
     * Create a template function that will dispatch to the specified
     * $handler when it needs to render the template with some data.
     *
     * @param string $path
     * @param Callable $handler Optional
     *
     * @return Callable
     */
    public static function template($path, $handler = null)
    {
        return self::snippet($path, null, $handler);
    }

    /**
     * @param string $path
     * @param string $selector Optional
     */
    public function __construct($path, $selector = null)
    {
        $this->path = $path;
        $this->selector = $selector;
    }

    /**
     * Return the HTML content for a template
     *
     * @param array $transformers Optional
     *
     * @return string
     */
    public function html(array $transformers = array())
    {
        $dom = $this->getDOMDocument();

        foreach ($transformers as $selector => $transformer) {
            $elements = $this->query($dom, $selector);

            foreach ($elements as $element) {
                Element::apply($element, array($transformer));
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
        $source = new DOMDocument();
        $source->loadHTMLFile($this->path);

        return $this->selector
            ? $this->fragment($source, $this->selector)
            : $source;
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
     * Return a document from the specified source document
     *
     * @param DOMDocument $source
     *
     * @return DOMDocument
     */
    protected function fragment(DOMDocument $source, $selector)
    {
        $dom = new DOMDocument;
        $elements = $this->query($source, $selector);

        foreach ($elements as $element) {
            $dom->appendChild(
                $dom->importNode($element, $deepClone = true)
            );
        }

        return $dom;
    }
}
