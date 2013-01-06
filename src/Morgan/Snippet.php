<?php

namespace Morgan;

use DOMDocument;

class Snippet extends Template
{
    /**
     * @param string $path
     * @param string $selector
     */
    public function __construct($path, $selector)
    {
        $this->path = $path;
        $this->selector = $selector;
    }

    /**
     * Fetch snippet from source file by selecting it from
     * the main document.
     *
     * @return DOMDocument
     */
    protected function getDOMDocument()
    {
        $dom = new DOMDocument;
        $source = new DOMDocument();
        $source->loadHTMLFile($this->path);
        $elements = $this->query($source, $this->selector);

        foreach ($elements as $element) {
            $dom->appendChild(
                $dom->importNode($element, $deepClone = true)
            );
        }

        return $dom;
    }
}
