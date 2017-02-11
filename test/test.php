<?php

require dirname(__DIR__) . '/vendor/autoload.php';

test(
    'escape HTML content',
    function () {
        eq(html('\''), '\'', "single quotes are preserved without escaping");

        eq(html('"'), '"', "double quotes are preserved without escaping");

        eq(html('&'), '&amp;', "ampersand is escaped");

        eq(html('\\'), '\\', "backslashes are preserved");

        eq(html('æøå'), 'æøå', "UTF-8 characters are preserved");

        eq(html(null), "", "null treated as empty string");

        eq(html(123), "123", "numeric values treated as strings");
    }
);

test(
    'escape HTML attributes',
    function () {
        eq(attr('\''), '&apos;', "single quotes are escaped");

        eq(attr('"'), '&quot;', "double quotes are escaped");

        eq(attr('&'), '&amp;', "ampersand is escaped");

        eq(attr('&amp;'), '&amp;amp;', "entities are double-escaped");

        eq('\\', attr('\\'), "backslashes are preserved");

        eq(attr('æøå'), 'æøå', "UTF-8 characters are preserved");

        eq(attr(null), "", "null treated as empty string");

        eq(attr(123), "123", "numeric values treated as strings");
    }
);

test(
    'escape in JavaScript string-literal context',
    function () {
        eq(js('\''), '\\\'', "single quotes are escaped");

        eq(js('"'), '\\"', "double quotes are escaped");

        eq(js('\\'), '\\\\', "backslashes are escaped");

        eq(js('æøå'), 'æøå', "UTF-8 characters are preserved");

        eq(js(null), "", "null treated as empty string");

        eq(js(123), "123", "numeric values treated as strings");
    }
);

test(
    'encode JSON values',
    function () {
        eq(
            json('I said \'" \\Hello & Wørld!'),
            '"I said \'\" \\\\Hello & Wørld!"'
        );

        eq(json(null), "null");

        eq(json(123), "123");
    }
);

exit(run());
