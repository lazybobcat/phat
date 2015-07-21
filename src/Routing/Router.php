<?php

namespace Phat\Routing;


class Router {

    private $url;
    private $routes = [];

    public function __construct($url)
    {
        $this->url = $url;
    }

}