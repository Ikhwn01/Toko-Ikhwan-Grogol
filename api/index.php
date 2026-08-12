<?php
// Router entrypoint for Vercel PHP Serverless Functions

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = ltrim($uri, '/');

if (empty($file)) {
    $file = 'index.php';
}

$target = __DIR__ . '/../' . $file;

if (file_exists($target) && !is_dir($target)) {
    // Serve file
    require_once $target;
} else if (file_exists($target . '.php')) {
    require_once $target . '.php';
} else {
    require_once __DIR__ . '/../index.php';
}
?>
