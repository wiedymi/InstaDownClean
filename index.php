<?php
error_reporting(0); // disable errors

$request = explode('?', $_SERVER['REQUEST_URI'], 2);

switch ($request[0]) {
    case '/' :
        require __DIR__ . '/views/main.php';
        break;
    case '' :
        require __DIR__ . '/views/main.php';
        break;
    case '/page/about' :
        require __DIR__ . '/views/about.php';
        break;
    case '/page/contacts' :
        require __DIR__ . '/views/contact.php';
        break;
    case '/ajax' :
        require __DIR__ . '/ajax.php';
        break;
    case '/mail' :
        require __DIR__ . '/mail.php';
        break;
    default:
        require __DIR__ . '/views/404.php';
        break;
}