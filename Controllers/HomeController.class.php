<?php

class HomeController extends AController
{
    function process($params)
    {
        $this->headers = array(
            'title' => 'main web',
            'keyWords' => 'main, web, info',
            'description' => 'main site on my web'
        );

        $this->view = 'home';
    }
}
