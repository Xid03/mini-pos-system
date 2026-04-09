<?php
declare(strict_types=1);

const APP_NAME = 'Mini POS System';
const APP_TAGLINE = 'Point of sale and inventory dashboard';
const APP_VERSION = '0.1.0';
const BASE_URL = '';

function app_base_url(): string
{
    if (BASE_URL !== '') {
        return '/' . trim(BASE_URL, '/');
    }

    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptFilename = (string) ($_SERVER['SCRIPT_FILENAME'] ?? '');
    $resolvedScriptFilename = realpath($scriptFilename);
    $scriptFilename = str_replace('\\', '/', $resolvedScriptFilename !== false ? $resolvedScriptFilename : $scriptFilename);

    $appRoot = realpath(dirname(__DIR__, 2));
    $appRoot = str_replace('\\', '/', $appRoot !== false ? $appRoot : dirname(__DIR__, 2));

    if ($scriptName === '' || $scriptFilename === '' || !str_starts_with($scriptFilename, $appRoot)) {
        return '';
    }

    $scriptDirectoryUrl = trim(str_replace('\\', '/', dirname($scriptName)), '/.');
    $scriptDirectoryPath = str_replace('\\', '/', dirname($scriptFilename));
    $relativeDirectory = trim(substr($scriptDirectoryPath, strlen($appRoot)), '/');

    if ($relativeDirectory === '') {
        return $scriptDirectoryUrl === '' ? '' : '/' . $scriptDirectoryUrl;
    }

    $urlSegments = $scriptDirectoryUrl === '' ? [] : explode('/', $scriptDirectoryUrl);
    $relativeSegments = explode('/', $relativeDirectory);
    $baseSegments = array_slice($urlSegments, 0, max(0, count($urlSegments) - count($relativeSegments)));

    return $baseSegments === [] ? '' : '/' . implode('/', $baseSegments);
}

function url(string $path = ''): string
{
    $base = app_base_url();
    $relativePath = ltrim($path, '/');

    if ($relativePath === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return ($base === '' ? '' : $base) . '/' . $relativePath;
}
