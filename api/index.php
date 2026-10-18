<?php
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestPath = rawurldecode(is_string($requestPath) ? $requestPath : '/');

if (str_contains($requestPath, "\0") || str_contains($requestPath, '\\')) {
    http_response_code(400);
    exit('Bad request');
}

$relativePath = trim($requestPath, '/');
if ($relativePath === '') {
    $relativePath = 'index.php';
}

$staticDirectories = ['assets', 'css', 'images', 'js'];
$staticExtensions = [
    'css' => 'text/css; charset=utf-8',
    'gif' => 'image/gif',
    'ico' => 'image/x-icon',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'js' => 'application/javascript; charset=utf-8',
    'png' => 'image/png',
    'svg' => 'image/svg+xml',
    'webp' => 'image/webp',
];

foreach ($staticDirectories as $directory) {
    $prefix = $directory . '/';
    if (strncmp($relativePath, $prefix, strlen($prefix)) !== 0) {
        continue;
    }

    $staticPath = realpath($projectRoot . DIRECTORY_SEPARATOR . $relativePath);
    $staticRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . $directory);
    $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

    if (
        $staticPath === false
        || $staticRoot === false
        || strpos($staticPath, $staticRoot . DIRECTORY_SEPARATOR) !== 0
        || !is_file($staticPath)
        || !isset($staticExtensions[$extension])
    ) {
        http_response_code(404);
        exit('Not found');
    }

    header('Content-Type: ' . $staticExtensions[$extension]);
    header('Content-Length: ' . filesize($staticPath));
    readfile($staticPath);
    exit;
}

$isPublicPhpPage = $relativePath === 'index.php'
    || preg_match('#^(views|controllers|reports)/[A-Za-z0-9_-]+\.php$#', $relativePath) === 1;

if (!$isPublicPhpPage) {
    http_response_code(404);
    exit('Not found');
}

$target = realpath($projectRoot . DIRECTORY_SEPARATOR . $relativePath);
$rootPrefix = $projectRoot . DIRECTORY_SEPARATOR;
if ($target === false || strpos($target, $rootPrefix) !== 0 || !is_file($target)) {
    http_response_code(404);
    exit('Not found');
}

$_SERVER['PHP_SELF'] = '/' . $relativePath;
$_SERVER['SCRIPT_NAME'] = '/' . $relativePath;
chdir(dirname($target));
require $target;
