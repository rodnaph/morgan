
# Morgan - Pure HTML Templating for PHP

Morgan is a small templating library for [PHP](http://www.php.net) to enable
using 'pure HTML' templates.  This means that no templating is included in
your HTML files, they are meant to be fully standalone components that you
can view and edit independently.  Morgan than handles transforming these
templates with your application data.

Morgan is a PHP port of the ideas from [EnLive](https://github.com/cgrand/enlive).

## Usage

```php
use Morgan\Template as T;

T::render(
    'page.html',
    array(
        'title' => T::content('Some Text')
    )
);
```

Here, we're creating a template using a HTML file we have.  When we call
render we pass an array of key/value pairs where the keys are CSS selectors to
match elements in the document, and the values are functions to transform those
elements.

So in the above example we're selecting the _title_ element, and then we're
setting its content to the string 'Some Text'.

_render()_ will echo the content of the template, you can also use _fetch()_ to
just return the transformed content.

## Transformers

In the above simple example the transformer we used was the _content_ transformer.
There are other transformers available…

```php
T::content('Some new content')

T::append('This please')

T::setAttr('class', 'some-class')

T::removeAttr('class')
```

## Custom Transformers

You can also create your own transformers, they are just functions which accept
a _DOMElement_ objects and mutate it in some way.

```php
T::render(
    'file.html',
    array(
        'a' => function(DOMElement $element) { … }
    )
);
```

## Snippets

As well as entire documents, you can also create snippets.  These are fragments
of some document, and you can use them for things like extracting a blog post
from a HTML file, then rendering this post in another template.

```php
$snippet = T::snippet(
    'blog-post-list.html',
    '.post',
    function($data) {
        return array(
            '.subject' => T::content($data['title'])
        );
    }
);
```

The second argument to allows selecting a part of the specified HTML file.

## Non-Trivial Example

As a more fully featured example, imagine you have a page where you want to list
a bunch of blog posts.  This page will contain a title, then a list of blog post
subjects with a short summary of their content (the title linking to each post).

We can markup this in a single file called _blog-posts.html_, then use it as the
template for our page, extract the example snippet for a blog post in summary
view to use for the blog posts.

```php
# array of blog posts to show
$posts = array(
    array(
        'title' => 'First Post',
        'summary' => 'Some short snippet'
    ),
    array(
        'title' => 'Another Post',
        'summary' => 'And another short snippet'
    )
);

// re-usable blog post summary snippet

$postSnippet = T::snippet(
    'blog-posts.html',
    '.post',
    function($item) {
        return array(
            'h3' => T::content($item['title']),
            'p' => T::content($item['summary'])
        );
    }
);

// render the main template

T::render(
    'blog-posts.html',
    array(
        'h1' => 'The Blog Posts Page',
        '.posts' => T::mapSnippet($postSnippet, $posts)
    )
);
```

## Motivation

This library was inspired by EnLive, and is mainly a [just-for-fun](http://en.wikipedia.org/wiki/Just_for_Fun)
implementation in PHP.  If you find it useful though feel free to contribute!

