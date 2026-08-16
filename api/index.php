<?php
// Entry point for Vercel Serverless PHP Runtime

$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'] ?? '/';

// Clean path
$path = ltrim($path, '/');

// Default to index.php if root
if ($path === '' || $path === 'index.php') {
    $target_file = __DIR__ . '/../index.php';
} else {
    // Prevent directory traversal
    $safe_path = str_replace('..', '', $path);
    $target_file = __DIR__ . '/../' . $safe_path;
}

// Handle directory request
if (is_dir($target_file)) {
    $target_file = rtrim($target_file, '/') . '/index.php';
}

// If file exists and is a PHP file
if (file_exists($target_file) && is_file($target_file)) {
    // Set working directory to project root so relative includes work seamlessly
    chdir(dirname(__DIR__));
    require $target_file;
} else {
    // Fallback if file not found
    if (file_exists(__DIR__ . '/../index.php')) {
        chdir(dirname(__DIR__));
        require __DIR__ . '/../index.php';
    } else {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
