<?php
$admin = '/admin';

if (!str_starts_with($_SERVER['REQUEST_URI'], $admin)) {
    if (file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
        return false;
    }
}

if (str_starts_with($_SERVER['REQUEST_URI'], $admin)) {
    if ($_SERVER['REQUEST_URI'] === "$admin/") {
        require __DIR__ . "$admin/dashboard.php";
        exit;
    }
}

require __DIR__ . '/index.php';
