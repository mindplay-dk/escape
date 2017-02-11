mindplay/escape
===============

This package provides a small set of global functions for use in plain PHP templates.

[![PHP Version](https://img.shields.io/badge/php-5.3%2B-blue.svg)](https://packagist.org/packages/mindplay/escape)
[![Build Status](https://travis-ci.org/mindplay-dk/escape.svg?branch=master)](https://travis-ci.org/mindplay-dk/escape)

I use this with [kisstpl](https://github.com/mindplay-dk/kisstpl) and plain PHP templates, but
any package (or plain, raw PHP scripts) should be fine.

It's really more documentation (perhaps even more just a philosophy) than it is code - and includes detailed
inline documentation with examples.

These are just tiny shorthand-functions wrapping `htmlspecialchars()` and `json_encode()` but
with the assumption that you *always* want **HTML 5** and **UTF-8** encoding - if you don't,
this package is not for you.

The source-file is bootstrapped to aggressively autoload via Composer, which
means these functions are always available, without having to use `require` or `include`.

Currently the following functions are included:

 * `html($value)` ~ `htmlspecialchars((string) $value, ENT_HTML5, 'UTF-8', true)`
 * `attr($value)` ~ `htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8', true)`
 * `js($value)` to escape inside a JavaScript string-literal context
 * `json($value)` ~ `json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`

Note that, because these functions are global, the addition of any new function would be considered a breaking
change with a major version number increase.

##### Why?

Because the PHP defaults for these functions are outdated.

And why global? Because we can't autoload functions, importing namespaced functions in every
template is a hassle, and we're not using the global namespace for anything anyway.

### Escaping and Encoding Values

Each function is intended for use within a single context - e.g. within an HTML tag, an
HTML attribute-value, a Javascript string literal, or inline in Javascript code. (Note that
contexts are often *nested*, as per the examples in the "Nested Contexts" section below.)

This is best illustrated with a few common examples.

##### HTML Tags

When outputting a `string` value in the context of an HTML tag, use the `html()` function:

```php
<h1><?= html($title) ?></h1>
```

##### HTML Attributes

When outputting a `string` value in the context of an HTML attribute, use the `attr()` function:

```php
<div id="<?= attr($id) ?>">
```

##### Javascript

The following examples assume a pure JSON or Javascript context, e.g. a template emitting content
for a response with a `Content-Type` of `application/json` or `application/javascript` - contrast
this with a *nested* context, such as Javascript inside a `<script>` tag.

When outputting a JSON-compatible (`string`, `int`, `float`, `bool`, `null` or `array`) value
in the context of JSON or Javascript code, use the `json()` function:

```php
function welcome() {
    alert(<?= json($message) ?>);
}
```

When outputting a `string` in the context of a Javascript string literal, use the `js()` function:

```php
function welcome() {
    alert('Welcome, <?= js($username) ?>');
}
```

Notice the difference: `json()` will add quotes when given a string value, whereas `js()` assumes
you're outputting string content between quotes.

##### Nested Contexts

For security reasons, it's important to always consider the context within which you're outputting content -
and helpful to think of some contexts as being *nested* within a different context.

The most common example of nested context is a `<script>` tag embedded in an HTML context - for example:

```php
<script>
function welcome() {
    alert(<?= html(json($message)) ?>);
}
</script>
```

In this example, the inner context is Javascript code, and the outer context is HTML - so the inner
function call is `json()` and the outer function call is `html()`.

Another example is a JavaScript string-literal context inside an HTML attribute:

```php
<button onclick="alert('Hello, <?= attr(js($username)) ?>')">
```

In this example, the inner context is a Javascript string literal, and the outer context is an HTML-attribute.

There are many possible use-cases combining two (or more) contexts - but if you can wrap your head around the
idea of *nested* contexts, selecting the right combination of functions should be fairly easy.
