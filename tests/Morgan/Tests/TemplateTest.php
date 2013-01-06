<?php

namespace Morgan\Tests;

use Morgan\Template as T;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->p = 'tests/data/test.html';
    }

    public function testTemplateCanBeFetched()
    {
        $this->assertNotNull(T::fetch($this->p, array()));
    }

    public function testContentCanBeTransformed()
    {
        $html = T::fetch($this->p, array('h1' => T::content('New Title')));
        $this->assertContains('New Title', $html);
    }

    public function testAttributesCanBeSetOnNodes()
    {
        $html = T::fetch($this->p, array('h1' => T::setAttr('class', 'foo')));

        $this->assertContains('class="foo"', $html);
    }

    public function testAttributesCanBeRemovedFromNodes()
    {
        $html = T::fetch($this->p, array('.things' => T::removeAttr('class')));

        $this->assertNotContains('class="things"', $html);
    }

    public function testContentCanBeAppendedToElements()
    {
        $html = T::fetch($this->p, array('title' => T::append(' - Bazzle')));

        $this->assertContains('Example Title - Bazzle', $html);
    }

    public function testMultipleTransformersCanBeApplied()
    {
        $html = T::fetch($this->p, array('title' => T::do_(T::append(' - Boo'), T::setAttr('class', 'foo'))));

        $this->assertContains('Example Title - Boo', $html);
        $this->assertContains('class="foo"', $html);
    }

    public function testContentCanBePrependedToElements()
    {
        $html = T::fetch($this->p, array('title' => T::prepend('The ')));

        $this->assertContains('The Example Title', $html);
    }
}
