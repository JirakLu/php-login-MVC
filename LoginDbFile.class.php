<?php

class LoginDbFile
{
    private static $instance = null;

    public static function get(): LoginDbFile
    {
        if (self::$instance == null)
            $instance = new LoginDbFile();

        return $instance;
    }

    private const FILENAME = "loginDb.txt";

    private function __construct()
    {
        if (!file_exists(self::FILENAME)) {
            file_put_contents(self::FILENAME, json_encode([]));
        }
    }

    public function existsItem($login): bool
    {
        return array_key_exists($login, $this->loadData());
    }

    public function writeItem($login, $password, $level = 'guest')
    {
        $data = $this->loadData();
        if (array_key_exists($login, $data)) {
            $data[$login]->password = $password;
            $data[$login]->level = $level;
        } else {
            $new = new stdClass();
            $new->login = $login;
            $new->password = $password;
            $new->level = $level;
            $data[] = $new;
        }

        file_put_contents(self::FILENAME, json_encode(array_values($data)));
    }

    public function setRememberCokie($login, $selector, $authenticator) {
        $data = $this->loadData();
        if (array_key_exists($login, $data)) {
            $data[$login]->cookie = ['selector' => $selector, 'authenticate' => hash('sha256', $authenticator), 'expire' => date('Y-m-d\TH:i:s', time() + 864000)];
            file_put_contents(self::FILENAME, json_encode(array_values($data)));
        }
    }

    public function findAuthenticator($selector) {
        $data = $this->loadData();
        foreach ($data as $value) {
            if (property_exists($value, 'cookie')) {
                $cookieSelector = $value->cookie->selector;
                if ($cookieSelector == $selector) {
                    return $value;
                }
            }
        }
        return false;
    }

    public function unsetCookie($login) {
        $data = $this->loadData();
        unset($data[$login]->cookie);
        file_put_contents(self::FILENAME, json_encode(array_values($data)));
    }

    public function getItem($login)
    {
        $data = $this->loadData();
        if (!array_key_exists($login, $data))
            return false;
        return $data[$login];
    }

    private function loadData(): array
    {
        $data = json_decode(file_get_contents(self::FILENAME));
        return array_combine(
            array_map(
                function ($el) use ($data) {
                    return $data[$el]->login;
                },
                array_keys($data)
            ),
            array_values($data)
        );
    }
}
