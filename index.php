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

//Cookie login
if (!$_SESSION['isLoggedIn'] && !empty($_COOKIE['remember'])) {
    list($selector, $authenticator) = explode(':', $_COOKIE['remember']);

    $user = LoginDbFile::get()->findAuthenticator($selector);


    if (hash_equals($user->cookie->authenticate, hash('sha256', base64_decode($authenticator)))) {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['level'] = $user->level;
        $_SESSION['email'] = $user->login;
    }
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

$router = new RouterController();
$router->process(array($_SERVER['REQUEST_URI']));
$router->renderView();
