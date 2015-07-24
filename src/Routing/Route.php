<?php

namespace Phat\Routing;


use Phat\Http\Request;

class Route {

    public $template    = '';
    public $pattern     = '';
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

    public function equals(array $parameters)
    {
        return
            strtolower($this->controller)   == strtolower($parameters['controller']) &&
            strtolower($this->action)       == strtolower($parameters['action']) &&
            ($this->method == Request::All || (!empty($parameters['method']) && strtolower($this->method) == strtolower($parameters['method']))) &&
            ($this->plugin == null || (!empty($parameters['plugin']) && strtolower($this->plugin) == strtolower($parameters['plugin']))) &&
            ($this->prefix == null || (!empty($parameters['prefix']) && strtolower($this->prefix) == strtolower($parameters['prefix'])))
        ;
    }

}