<?php

class RegisterController extends AController
{
    function process($params)
    {
        $this->headers = array(
            'title' => 'register form',
            'keyWords' => 'register, email, password',
            'description' => 'register form on my web'
        );

        $_SESSION['errorMsg'] = [false, ''];

        $this->view = 'register';

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $status = LoginManager::handleRegister($_POST['email'], $_POST['password'], $_POST['passwordRepeat']);
            if ($status) $this->redirect('login');
        }
    }
}