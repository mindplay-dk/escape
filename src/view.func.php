<?php

/**
 * Escape content for output in the context of an HTML tag, e.g.:
 *
 *     <div><?= html($content) ?></div>
 *
 * If the given content contains HTML-like content, this *will* be double-escaped -
 * in a proper data-flow, your content should never contain already-encoded content,
 * which is why we do *not* offer any means of setting `$double_encode` set to `false`.
 *
 * @param string|mixed $value UTF-8 string (or any string-castable value)
 *
 * @return string HTML escaped UTF-8 string
 */
function html($value)
{
    return htmlspecialchars((string) $value, ENT_HTML5, 'UTF-8', true);
}

/**
 * Escape content for output in the context of an HTML attribute-value, e.g.:
 *
 *     <div data-id="<?= attr($id) ?>">
 *
 * Both single and double quotes will be escaped as entities, since this function
 * cannot know whether the enclosing attribute uses single or double quotes.
 *
 * If the given content contains HTML-like content, this *will* be double-escaped -
 * in a proper data-flow, your content should never contain already-encoded content,
 * which is why we do *not* offer any means of setting `$double_encode` set to `false`.
 *
 * @param string|mixed $value UTF-8 string (or any string-castable value)
 *
 * @return string HTML-attribute escaped UTF-8 string
 */
function attr($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
}

/**
 * Escape string for use in a JavaScript string-literal context, e.g.:
 *
 *     <script>
 *         document.title = 'Page: <%= html(js($title)) %>';
 *     </script>
 *
 * Note the use of both `html()` and `js()` here, since there are actually two nested
 * contexts in this example: a JavaScript string in an HTML context.
 *
 * To illustrate why HTML context isn't simply assumed, here is an example using both
 * `html()` and `js()`, since the following is JavaScript in an HTML-attribute context:
 *
 *     <button onclick="alert('Hello, <?= attr(js($username)) ?>')">
 *
 * @param string|mixed $value UTF-8 string (or any string-castable value)
 *
 * @return string Javascript-string-escaped UTF-8 string
 *
 * @throws InvalidArgumentException for invalid UTF-8 string
 */
function js($value)
{
    $value = (string) $value;

    if ($value === "") {
        return $value;
    }

    $str = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException(json_last_error_msg());
    }

    return preg_replace("#'#u", "\\'", substr($str, 1, -1));
}

/**
 * Encode data and escape the resulting content for use in a JavaScript-context, e.g.:
 *
 *     <script type="application/json">
 *         var data = <%= html(json($data)) %>;
 *     </script>
 *
 * Note the use of both `html()` and `json()` here, since the context in this example is
 * actually JavaScript in an HTML context.
 *
 * If the input data contains numeric values improperly typed as strings, these will be
 * encoded as strings - in a proper data-flow, your values should have the correct types,
 * which is why we do *not* offer options such as `JSON_NUMERIC_CHECK`.
 *
 * @param mixed $value  JSON-compatible value or (array) data-structure
 * @param bool  $pretty if true, output is pretty-printed (human-readable); defaults to false
 *
 * @return string JSON-encoded data
 *
 * @throws InvalidArgumentException if encoding the given data fails for any reason - for example,
 *                                  if a string contains an invalid UTF-8 character sequence.
 */
function json($value, $pretty = false)
{
    $json = json_encode(
        $value,
        $pretty
            ? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            : JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException(json_last_error_msg());
    }

    return $json;
}
