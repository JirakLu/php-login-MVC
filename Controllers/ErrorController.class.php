<?php

class ErrorController extends AController
{

    function process($params)
    {
        $this->headers = array(
            'title' => 'error page',
            'keyWords' => 'error',
            'description' => 'page does not exist'
        );

        $this->view = 'error';
    }
}