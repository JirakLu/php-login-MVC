<?php
require_once('./LoginDbFile.class.php');
session_start();

//Session defaults
if (!isset($_SESSION['isLoggedIn'])) {
    $_SESSION['isLoggedIn'] = false;
    $_SESSION['level'] = '';
    $_SESSION['email'] = '';
    $_SESSION['errorMsg'] = [false,''];
}



//Initializing
mb_internal_encoding('UTF-8');

spl_autoload_register(function ($classname) {
    if (preg_match('/Controller$/', $classname)) {
        require_once("./Controllers/$classname" . ".class.php");
    } else {
        require_once("./Models/$classname" . ".class.php");
    }
});

//Cookie login
if (!$_SESSION['isLoggedIn'] && !empty($_COOKIE['remember'])) {
    RememberMe::checkCookies();
}

$router = new RouterController();
$router->process(array($_SERVER['REQUEST_URI']));
$router->renderView();
