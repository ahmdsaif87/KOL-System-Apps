<?php
/**
 * Router untuk PHP built-in server (development only).
 * Mendukung Windows, Linux, dan macOS tanpa bergantung pada OS symlink.
 *
 * Jalankan: cd landing && php -S 127.0.0.1:8087 dev-router.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$repoRoot = dirname(__DIR__);

// Helper untuk menyajikan file statis (css, js, gambar, svg, fonts)
function serve_static_dev($file) {
    if (!is_file($file)) {
        return false;
    }
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'ico'   => 'image/x-icon',
        'json'  => 'application/json',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    readfile($file);
    exit;
}

// 1. Sajikan file statis landing
$landingFile = __DIR__ . $path;
if ($path !== '/' && $path !== '/index.php' && is_file($landingFile)) {
    return FALSE;
}

// 2. Sub-aplikasi: Campaign (For Brand) - Direct path ke root repo
if ($path === '/campaign' || strpos($path, '/campaign/') === 0) {
    if ($path === '/campaign') {
        header('Location: /campaign/');
        exit;
    }
    $realFile = $repoRoot . $path;
    if (is_file($realFile)) {
        serve_static_dev($realFile);
    }
    $_SERVER['SCRIPT_NAME'] = '/campaign/index.php';
    chdir($repoRoot . '/campaign');
    require $repoRoot . '/campaign/index.php';
    exit;
}

// 3. Sub-aplikasi: KOL Datahub (For KOL) - Direct path ke root repo
if ($path === '/datahub' || strpos($path, '/datahub/') === 0) {
    if ($path === '/datahub') {
        header('Location: /datahub/');
        exit;
    }
    $realFile = $repoRoot . $path;
    if (is_file($realFile)) {
        serve_static_dev($realFile);
    }
    $_SERVER['SCRIPT_NAME'] = '/datahub/index.php';
    chdir($repoRoot . '/datahub');
    require $repoRoot . '/datahub/index.php';
    exit;
}

// 4. Default: Landing page
require __DIR__ . '/index.php';
