<?php
/**
 * Router untuk PHP built-in server (development only).
 *
 * Produksi memakai Apache/LiteSpeed + .htaccess. File ini tidak dipakai
 * di produksi dan aman dibiarkan di repo.
 *
 * Jalankan: php -S 127.0.0.1:8087 dev-router.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Sajikan file statis langsung (css, js, gambar, favicon, dll.)
$targetFile = __DIR__ . $path;
if ($path !== '/' && $path !== '/index.php' && file_exists($targetFile) && !is_dir($targetFile)) {
    return FALSE;
}

// 2. Sub-aplikasi: Campaign (For Brand)
if ($path === '/campaign' || strpos($path, '/campaign/') === 0) {
    if ($path === '/campaign') {
        header('Location: /campaign/');
        exit;
    }
    if (file_exists($targetFile) && !is_dir($targetFile)) {
        return FALSE;
    }
    $_SERVER['SCRIPT_NAME'] = '/campaign/index.php';
    chdir(__DIR__ . '/campaign');
    require __DIR__ . '/campaign/index.php';
    exit;
}

// 3. Sub-aplikasi: KOL Datahub (For KOL)
if ($path === '/datahub' || strpos($path, '/datahub/') === 0) {
    if ($path === '/datahub') {
        header('Location: /datahub/');
        exit;
    }
    if (file_exists($targetFile) && !is_dir($targetFile)) {
        return FALSE;
    }
    $_SERVER['SCRIPT_NAME'] = '/datahub/index.php';
    chdir(__DIR__ . '/datahub');
    require __DIR__ . '/datahub/index.php';
    exit;
}

// 4. Default: Landing page
require __DIR__ . '/index.php';
