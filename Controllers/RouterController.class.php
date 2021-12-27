<?php

class RouterController extends AController
{

    protected $controller;

    private function parseUrl($url): array
    {
        $parsedUrl = parse_url($url);
        $parsedUrl = str_replace('/loginProject', '', $parsedUrl);
        $parsedUrl["path"] = ltrim($parsedUrl["path"], "/");
        $parsedUrl["path"] = trim($parsedUrl["path"]);
        return explode("/", $parsedUrl["path"]);
    }

    private function camelCase($text): string
    {
        $text = str_replace('-', '', $text);
        $text = ucwords($text);
        return str_replace(' ', '', $text);
    }

    function process($params)
    {
        $parsedUrl = $this->parseUrl($params[0]);

        if (empty($parsedUrl[0]))
            $this->redirect('home');

        $controllerClass = $this->camelCase(array_shift($parsedUrl) . "Controller");

        if (file_exists("./Controllers/$controllerClass.class.php"))
            $this->controller = new $controllerClass;
        else
            $this->redirect('error');

        $this->controller->process($parsedUrl);

        $this->data['title'] = $this->controller->headers['title'];
        $this->data['description'] = $this->controller->headers['description'];
        $this->data['keyWords'] = $this->controller->headers['keyWords'];

        $this->view = 'htmlStruct';

    }
}
