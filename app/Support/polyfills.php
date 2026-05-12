<?php

if (! function_exists('mb_split')) {
    /**
     * Lightweight fallback for environments where ext-mbstring is unavailable.
     * Laravel uses mb_split() with mb-regex patterns without delimiters; this
     * wrapper converts that format into a UTF-8 PCRE pattern.
     */
    function mb_split(string $pattern, string $string, int $limit = -1): array|false
    {
        $delimiter = '~';
        $regex = $delimiter . str_replace($delimiter, '\\' . $delimiter, $pattern) . $delimiter . 'u';

        return preg_split($regex, $string, $limit === -1 ? -1 : $limit);
    }
}
