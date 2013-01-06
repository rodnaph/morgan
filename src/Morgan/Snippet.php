<?php

namespace Morgan;

class Snippet implements Renderable
{
    public function __construct($path, $selector)
    {
        $this->path = $path;
        $this->selector = $selector;
    }
}
