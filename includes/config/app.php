<?php
declare(strict_types=1);

const APP_NAME = 'Mini POS System';
const APP_TAGLINE = 'Portfolio-ready point of sale and inventory dashboard';
const APP_VERSION = '0.1.0';
const BASE_URL = '';

function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $relativePath = ltrim($path, '/');

    if ($relativePath === '') {
        return $base === '' ? './' : $base . '/';
    }

    return $base === '' ? $relativePath : $base . '/' . $relativePath;
}

