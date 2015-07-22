<?php

namespace Phat\Routing;


use Phat\Http\Request;

class Router {

    private $routes = [];


    public function connect($template, $callable)
    {
        $route = new Route($template, $callable);
        $this->routes[] = $route;
    }

    public function parse(Request $request)
    {
        foreach($this->routes as $route) {
            if($route->match($request->url)) {
                return $route->call();
            }
        }
    }

}