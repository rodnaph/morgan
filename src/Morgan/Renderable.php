<?php

namespace Morgan;

interface Renderable
{
    /**
     * Render this item and echo its content
     *
     * @param array $transfomers
     */
    public function render(array $transformers);

    /**
     * Render this item and return its content
     *
     * @param array $transformers
     *
     * @return string
     */
    public function fetch(array $transformers);
}
