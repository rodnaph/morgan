
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
T::render(
    'blog-post-list.html',
    array(
        '.subject' => T::content('Post Title')
    ),
    '.post'
);
```

The third argument to allows selecting a part of the specified HTML file.

## Motivation

This library was inspired by EnLive, and is mainly a [just-for-fun](http://en.wikipedia.org/wiki/Just_for_Fun)
implementation in PHP.  If you find it useful though feel free to contribute!

