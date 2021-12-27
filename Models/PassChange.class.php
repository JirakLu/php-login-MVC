<?php

class PassChange {

    public static function changePass(string $login, string $oldPas, string $newPass1, string $newPass2) {
        $db = LoginDbFile::get();

        if (empty($oldPas) || empty($newPass1) || empty($newPass2)) {
            LoginManager::setErrorMsg('Prosím vyplň všechny pole');
        } else if ($newPass1 != $newPass2) {
            LoginManager::setErrorMsg('Hesla se neschodují');
        }  else if (preg_match(LoginManager::$passwordRegex, $newPass1) == false) {
            LoginManager::setErrorMsg('Heslo nesplňuje požadované parametry <ul class="error-list">
                <li>Alespoň 8 znaků</li>
                <li>Alespoň jedna číslice</li>
                <li>Alespoň jeden malý a velký znak</li>
                <li>Alespoň jeden speciální znak</li>
            </ul>');
        } else {
            $pepper = get_cfg_var("pepper");
            $psw_peppered = hash_hmac("sha256", $oldPas, $pepper);
            $user = $db->getItem($login);
            $psw_db = $user->password;
            if (password_verify($psw_peppered, $psw_db)) {
                $pepper = get_cfg_var("pepper");
                $psw_peppered = hash_hmac("sha256", $newPass1, $pepper);
                $psw_hashed = password_hash($psw_peppered, PASSWORD_ARGON2ID);
                $db->writeItem($login,$psw_hashed);
                LoginManager::setErrorMsg('Heslo změněno');
            } else {
                LoginManager::setErrorMsg('Staré heslo nesedí');
            }
        }
    }
}