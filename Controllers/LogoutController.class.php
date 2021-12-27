<?php

class LogoutController extends AController
{
    function process($params)
    {
        $this->headers = array(
            'title' => 'logout popup',
            'keyWords' => 'logout',
            'description' => 'you can logout here'
        );

        if (isset($_SESSION)) {
            LoginDbFile::get()->unsetCookie($_SESSION['email']);
            unset($_SESSION);
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
            session_destroy();
        }

        $this->redirect('home');
    }
}

