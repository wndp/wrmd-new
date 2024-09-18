<?php

/**
 * Wrap a sql's field name in back ticks.
 *
 * @param  string  $field
 * @return string
 */
if (! function_exists('protect_identifiers')) {
    function protect_identifiers($field)
    {
        return '`'.str_replace('.', '`.`', $field).'`';
    }
}

/**
 * Determine if given text is a 4 character year.
 *
 * @param  mixed  $text
 * @return bool
 */
if (! function_exists('is_year')) {
    function is_year($text)
    {
        if (strlen($text) === 4) {
            if (is_int($text)) {
                return true;
            } elseif (is_string($text)) {
                return ctype_digit($text);
            }
        }

        return false;
    }
}
