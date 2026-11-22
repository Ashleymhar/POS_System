<?php
function appUrl($path = '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $projectDirectory = basename(dirname(__DIR__));
    $localPrefix = '/' . $projectDirectory;
    $basePath = strpos($scriptName, $localPrefix . '/') === 0 ? $localPrefix : '';

    return $basePath . '/' . ltrim($path, '/');
}
