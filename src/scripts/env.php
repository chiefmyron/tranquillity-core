<?php

/**
 * Include helper function for accessing environment variables
 */

declare(strict_types=1);

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        if (array_key_exists($key, $_ENV) === false) {
            return $default;
        }

        $value = $_ENV[$key];
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        // Remove quotes from value
        if (strlen($value) > 1 && (substr($value, 0, 1) == '"') && (substr($value, -1, 1) == '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
