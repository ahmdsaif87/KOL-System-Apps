<?php
// router.php — Unified router for all 3 apps: Landing, Campaign, and Datahub
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$repoRoot = __DIR__;

// Helper function to serve static files with proper MIME types
function serve_static($file) {
    if (!is_file($file)) return false;
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

// 1. Direct static file in workspace root
if ($uri !== '/' && is_file($repoRoot . $uri)) {
    serve_static($repoRoot . $uri);
}

// 2. Static file in landing assets / dist
if ($uri !== '/' && is_file($repoRoot . '/landing' . $uri)) {
    serve_static($repoRoot . '/landing' . $uri);
}

// 3. Sub-aplikasi: Campaign (Admin & Multi-Brand)
if ($uri === '/campaign' || strpos($uri, '/campaign/') === 0) {
    if ($uri === '/campaign') {
        header('Location: /campaign/administrator/login');
        exit;
    }
    $realFile = $repoRoot . $uri;
    if (is_file($realFile)) {
        serve_static($realFile);
    }
    $_SERVER['SCRIPT_NAME'] = '/campaign/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $repoRoot . '/campaign/index.php';
    chdir($repoRoot . '/campaign');
    require $repoRoot . '/campaign/index.php';
    exit;
}

// 4. Sub-aplikasi: Datahub (Talent KOL Portal)
if ($uri === '/datahub' || strpos($uri, '/datahub/') === 0) {
    if ($uri === '/datahub') {
        header('Location: /datahub/administrator/login');
        exit;
    }
    $realFile = $repoRoot . $uri;
    if (is_file($realFile)) {
        serve_static($realFile);
    }
    $_SERVER['SCRIPT_NAME'] = '/datahub/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $repoRoot . '/datahub/index.php';
    chdir($repoRoot . '/datahub');
    require $repoRoot . '/datahub/index.php';
    exit;
}

// 5. Default route: Landing Page
$_SERVER['SCRIPT_NAME'] = '/landing/index.php';
$_SERVER['SCRIPT_FILENAME'] = $repoRoot . '/landing/index.php';
chdir($repoRoot . '/landing');
require $repoRoot . '/landing/index.php';
