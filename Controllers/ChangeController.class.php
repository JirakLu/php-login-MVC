<?php

class ChangeController extends AController
{

    function process($params)
    {
        if (!$_SESSION['isLoggedIn']) $this->redirect('home');

        $this->headers = array(
            'title' => 'Password change',
            'keyWords' => 'password, change, renew',
            'description' => 'Password change form for my web'
        );

        $_SESSION['errorMsg'] = [false, ''];

        $this->view = 'passChange';

        if (isset($_POST['passwordOld']) && isset($_POST['password1']) && isset($_POST['password2'])) {
            PassChange::changePass($_SESSION['email'],$_POST['passwordOld'], $_POST['password1'], $_POST['password2']);
        }
    }
}
