<?php
// Get the request URI and remove leading/trailing slashes
$request = trim($_SERVER['REQUEST_URI'], '/');

// Handle routing
switch ($request) {
    case '/':
        require __DIR__ . '/index.php';
        break;
    case 'home':
        require __DIR__ . '/user.php';
        break;

    case 'admin':
        require __DIR__ . '/admin/index.php';
        break;

    case 'contact':
        require __DIR__ . '/pages/contact.php';
        break;

    default:
        // Check if the requested file exists (for API endpoints)
        $requestedFile = __DIR__ . '/' . $request;
        if (file_exists($requestedFile)) {
            require $requestedFile;
        } else {
            // Handle 404 if the file doesn't exist
            header("HTTP/1.0 404 Not Found");
            require __DIR__ . '/pages/404.php';
        }
        break;
}