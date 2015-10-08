<?php


function url($controller = null, $parameters = null) {
    $parts = parse_url($_SERVER['REQUEST_URI']);

    if ($controller !== null) {
        $parts['path'] = dirname($parts['path']) . '/' . $controller;
    }

    if ($parameters !== null) {
        $query = array();

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $parts['query'] = http_build_query(array_merge($query, $parameters));
    }

    if (!function_exists('http_build_url')) {
        require_once realpath(dirname(__FILE__) . '/php5/http_build_url.php');
    }

    $parts['scheme'] = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
    $parts['host']   = $_SERVER['SERVER_NAME'];

    return http_build_url($parts);
}