<?php

class RememberMe {
    /**
     * @throws Exception
     */
    public static function rememberMe() {
        $db = LoginDbFile::get();
        if ($db->existsItem($_SESSION['email'])) {
            $selector = base64_encode(random_bytes(9));
            $authenticator = random_bytes(33);

            setcookie(
                'remember',
                $selector.':'.base64_encode($authenticator),
                time() + 864000,
            );

            $db->setRememberCokie($_SESSION['email'],$selector,$authenticator);
        }
    }

    public static function checkCookies() {
        list($selector, $authenticator) = explode(':', $_COOKIE['remember']);

        $user = LoginDbFile::get()->findAuthenticator($selector);


        if (hash_equals($user->cookie->authenticate, hash('sha256', base64_decode($authenticator)))) {
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['level'] = $user->level;
            $_SESSION['email'] = $user->login;
        }
    }
}