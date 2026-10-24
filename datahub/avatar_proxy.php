<?php
$url = $_GET['url'] ?? '';
if (empty($url) || !preg_match('/^https?:\/\//i', $url)) {
    http_response_code(404);
    exit;
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$data = curl_exec($ch);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && $data) {
    header("Content-Type: $content_type");
    header("Cache-Control: public, max-age=86400");
    echo $data;
} else {
    // Return a 1x1 transparent pixel or 404 to trigger onerror fallback in frontend
    http_response_code(404);
}
