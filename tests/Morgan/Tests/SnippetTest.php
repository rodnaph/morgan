<?php

namespace Morgan\Tests;

use Morgan\Snippet as S;

class SnippetTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->s = new S('tests/data/test.html', '.things');
    }

    public function testSnippetsCanBeRendered()
    {
        $html = $this->s->fetch(array());

        $this->assertNotNull($html);
    }

    public function testSnippetSelectsFragmentOfHTML()
    {
        $html = $this->s->fetch(array());

        $this->assertContains('Title of thing', $html);
        $this->assertNotContains('Example Title', $html);
        $this->assertNotContains('List of things', $html);
    }
}
