<?php

class LoginController extends AController
{
    /**
     * @throws Exception
     */
    function process($params)
    {
        if ($_SESSION['isLoggedIn']) $this->redirect('home');

        $this->headers = array(
            'title' => 'Login form',
            'keyWords' => 'login, email, password',
            'description' => 'login form on my web'
        );

        $_SESSION['errorMsg'] = [false, ''];

        $this->view = 'login';

        if (isset($_POST['email']) && isset($_POST['password'])) {
            LoginManager::handleLogin($_POST['email'], $_POST['password']);
            if ($_SESSION['isLoggedIn'] && $_POST['rememberMe']) {
                RememberMe::rememberMe();
            }
            if ($_SESSION['isLoggedIn']) $this->redirect('home');
        }
    }
}