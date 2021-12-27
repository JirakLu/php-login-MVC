<?php

use JetBrains\PhpStorm\NoReturn;

abstract class AController
{
    protected array $data = array();
    protected array $headers = array('title' => '', 'keyWords' => '', 'description' => '');
    protected string $view = '';

    public function renderView()
    {
        if ($this->view) {
            extract($this->data);
            require("./Views/" . $this->view . ".phtml");
        }
    }

    #[NoReturn] public function redirect($url): void {
        header("Location: $url");
        header("Connection: close");
        exit;
    }

    abstract function process($params);

}