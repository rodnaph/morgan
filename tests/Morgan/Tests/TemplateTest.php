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

    public function testSnippetsCanBeRendered()
    {
        $html = T::fetch($this->p, array(), '.things');

        $this->assertContains('Title of thing', $html);
        $this->assertNotContains('Page Heading', $html);
        $this->assertNotContains('Example Title', $html);
    }

    public function testSnippetsCanBeMappedToReplaceContent()
    {
        $items = array(
            array('title' => 'One'),
            array('title' => 'Two')
        );
        $snippet = T::snippet(
            'tests/data/test.html',
            '.things',
            function($item) {
                return array('h3' => T::content($item['title']));
            }
        );
        $html = T::fetch(
            $this->p,
            array('.things' => T::mapSnippet($snippet, $items))
        );

        $this->assertContains('One', $html);
        $this->assertContains('Two', $html);
    }

    public function testTemplatesCanBeRenderedAsFunctions()
    {
        $tpl = T::template($this->p, function() { return array(); });

        $this->assertContains('Example Title', $tpl(array()));
    }

    public function testDataForTemplatesAndSnippetsIsOptional()
    {
        $func = function() { return array(); };
        $tpl = T::template($this->p, $func);
        $snip = T::snippet($this->p, '.things', $func);

        $this->assertContains('Example Title', $tpl());
        $this->assertContains('Title of thing', $snip());
    }

    public function testHandlerFunctionIsOptionalToTemplate()
    {
        $tpl = T::template($this->p);

        $this->assertNotNull($tpl());
    }

    public function testHandlerFunctionIsOptionalToSnippet()
    {
        $snip = T::snippet($this->p, '.things');

        $this->assertNotNull($snip());
    }

    public function testTransformersAreOptionalForFetch()
    {
        $this->assertNotNull(T::fetch($this->p));
    }

    public function testContentIsOptionalToHtmlContent()
    {
        T::fetch($this->p, array('title' => T::htmlContent(null)));
    }

    public function testSnippetsDoNotContainHtmlOrBody()
    {
        $snip = T::snippet($this->p, '.things');
        $html = T::fetch($this->p, array('body' => T::htmlContent($snip())));

        $this->assertNotContains('<body><html>', $html);
    }

    public function testTemplatesAndSnippetsCanTakeMultipleArguments()
    {
        $snip = T::snippet(
            $this->p,
            '.things',
            function($a, $b) {
                return array(
                    'h3' => T::content($a),
                    'p' => T::content($b)
                );
            }
        );
        $html = $snip('One', 'Two');

        $this->assertContains('One', $html);
        $this->assertContains('Two', $html);
    }
}
