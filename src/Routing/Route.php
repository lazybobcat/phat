<?php

namespace Phat\Routing;


use Phat\Http\Request;

class Route {

    public $template    = '';
    public $method      = Request::All;
    public $plugin      = null;
    public $controller  = null;
    public $action      = null;
    public $prefix      = null;
    public $dynamicData = [];
    public $urlVariables= [];

    public function matches(Request $request)
    {
        if($this->method != Request::All && $this->method != $request->method) {
            return false;
        }

        if(preg_match('/^'.$this->template.'$/is', rtrim($request->url, '/'), $match)) {
            return $match;
        }

        return false;
    }

}