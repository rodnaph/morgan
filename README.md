
# Morgan - Pure HTML Templating for PHP [![Build Status](https://secure.travis-ci.org/rodnaph/morgan.png?branch=master)](http://travis-ci.org/rodnaph/morgan)

Morgan is a small templating library for [PHP](http://www.php.net) to enable
using 'pure HTML' templates.  This means that no templating is included in
your HTML files, they are meant to be fully standalone components that you
can view and edit independently.  Morgan then handles transforming these
templates using your application data.

Morgan is a PHP port of the ideas from [EnLive](https://github.com/cgrand/enlive).

## Usage

```php
use Morgan\Template as T;

T::render(
    'page.html',
    array(
        '.header h1' => T::content('Some Text')
    )
);
```

Here, we're rendering a template using a HTML file we have.  When we call
render we pass the name of the source file, and an array of key/value pairs
where the keys are CSS selectors to match elements in the document, and the
values are functions to transform those elements.

So in the above example we're selecting all _h1_ elements inside _.header_
elements, and then we're setting their content to the string 'Some Text'.

(_render() will echo the content of the template, you can also use fetch() to
just return the transformed content._)

You can also get handles to [Callable](http://php.net/manual/en/language.types.callable.php)
template functions like this...

```php
$homePage = T::template(
    'index.html',
    function($data) {
        return array(
            'h1' => T::content($data['title'])
        );
    }
);

$html = $homePage(array('title' => 'The Title'));
```

## Transformers

In the above simple example the transformer we used was the _content_ transformer.
There are other transformers available…

```php
# set the content of an element
T::content('Some new content')

# append content to an element
T::append('This please')

# prepend content to an element
T::prepend('Some string')

# use HTML as content for an element
T::htmlContent('<b>bold text</b>')

# set attributes of elements
T::setAttr('href', '/blog/post.html')

# remove attributes of elements
T::removeAttr('class')

# add classes to element
T::addClass('foo', 'bar', 'baz')

# remove classes from element
T::removeClass('foo', 'bar', 'baz')

# replace with some HTML
T::replaceWith('<b>Some HTML</b>')
```

## Custom Transformers

You can also create your own transformers, they are just functions which accept
a _DOMElement_ object and mutate it in some way.

```php
T::render(
    'file.html',
    array(
        'a' => function(DOMElement $element) { … }
    )
);
```

## Multi Transforms

Often you'll want to apply multiple transformers to a given selector.  You can
do this by using the *do_* form.

```php
array(
    '.foo' => T::do_(
        T::content('New content'),
        T::setAttr('href', '/some/page.html')
    )
)
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

The second argument is the CSS selector for selecting the fragment from the document.

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
        'summary' => 'Some short snippet',
        'href' => '/blog/post-one.html'
    ),
    array(
        'title' => 'Another Post',
        'summary' => 'And another short snippet',
        'href' => '/blog/another-post.html'
    )
);

// re-usable blog post summary snippet

$postSnippet = T::snippet(
    'blog-posts.html',
    '.post',
    function($item) {
        return array(
            'h3 a' => T::do_(
                T::content($item['title']),
                T::setAttr('href', $item['href'])
            ),
            'p' => T::content($item['summary'])
        );
    }
);

// render the main template

T::render(
    'blog-posts.html',
    array(
        'h1' => 'The Blog Posts Page',
        '.posts' => T::map($postSnippet, $posts)
    )
);
```

## Installation with Composer

Morgan is available via [Composer](http://getcomposer.org), just add it with composer, and specify the version as _dev-master_.

```javascript
composer require rodnaph/morgan
```

You may need to specify the _minimum-stability_ as "dev".

## Motivation

This library was inspired by EnLive, and is mainly a [just-for-fun](http://en.wikipedia.org/wiki/Just_for_Fun)
implementation in PHP.  If you find it useful though feel free to contribute!

## TODO

The following functions from EnLive are not implemented yet, so I still need to
review them and do that if they make sense...

```
wrap
unwrap
after
before
move
```

