<?php

class LoginManager
{

    public static string $passwordRegex = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/";

    public static function handleRegister(string $email, string $password1, string $password2): bool
    {
        $db = LoginDbFile::get();
        if (!self::checkCredentials($email, $password1)) {
            self::setSession(false, '', '');
        } else if ($db->existsItem($email)) {
            self::setErrorMsg('Účet na tento email již existuje');
        } else if ($password1 != $password2) {
            self::setErrorMsg('Hesla se neschodují');
        } else {
            $pepper = get_cfg_var("pepper");
            $psw_peppered = hash_hmac("sha256", $password1, $pepper);
            $psw_hashed = password_hash($psw_peppered, PASSWORD_ARGON2ID);
            $db->writeItem($email,$psw_hashed);
            return true;
        }
        return false;
    }

    public static function handleLogin(string $email, string $password)
    {
        $db = LoginDbFile::get();
        if (!self::checkCredentials($email, $password)) {
            self::setSession(false, '', '');
        } else if (!($db->existsItem($email))) {
            self::setErrorMsg('Špatný email, musíš se nejdřív zaregistrovat.');
        } else {
            $pepper = get_cfg_var("pepper");
            $psw_peppered = hash_hmac("sha256", $password, $pepper);
            $user = $db->getItem($email);
            $psw_db = $user->password;
            if (password_verify($psw_peppered, $psw_db)) {
                self::setSession(true, $user->login, $user->level);
            } else {
                self::setErrorMsg('Špatné heslo.');
            }
        }

    }

    public static function checkCredentials(string $email, string $password): bool
    {
        if (empty($email) && empty($password)) {
            self::setErrorMsg('Vyplň email a heslo');
        } else if (empty($email)) {
            self::setErrorMsg('Vyplň email');
        } else if (empty($password)) {
            self::setErrorMsg('Vyplň heslo');
        } else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            self::setErrorMsg('Neplatný email');
        } else if (preg_match(self::$passwordRegex, $password) == false) {
            self::setErrorMsg('Heslo nesplňuje požadované parametry <ul class="error-list">
                <li>Alespoň 8 znaků</li>
                <li>Alespoň jedna číslice</li>
                <li>Alespoň jeden malý a velký znak</li>
                <li>Alespoň jeden speciální znak</li>
            </ul>');
        } else {
            return true;
        }
        return false;
    }

    public static function setErrorMsg(string $msg)
    {
        $_SESSION['errorMsg'] = [true, $msg];
    }

    public static function setSession(bool $isLoggedIn, string $email, string $level)
    {
        $_SESSION['isLoggedIn'] = $isLoggedIn;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $level;
    }
}
