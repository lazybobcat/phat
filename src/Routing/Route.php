<?php

namespace Phat\Routing;

use Phat\Http\Request;

/**
 * The Route class contains information about a specific Route in the application.
 */
class Route
{
    /**
     * @var string The Route template as defined for Router::connect() method
     */
    public $template = '';

    /**
     * @var string Regex pattern to match the template
     */
    public $pattern = '';

    /**
     * @var string The HTTP method the Route should match
     */
    public $method = Request::All;

    /**
     * @var string The application plugin the Route should match
     */
    public $plugin = null;

    /**
     * @var string The application controller the Route should match
     */
    public $controller = null;

    /**
     * @var string The application action the Route should match
     */
    public $action = null;

    /**
     * @var string The application prefix the Route should match
     */
    public $prefix = null;

    /**
     * @var array Used by the Router to store dynamic url parameters
     */
    public $dynamicData = [];

    /**
     * @var array Used by the Router to store dynamic url variables
     */
    public $urlVariables = [];

    /**
     * Checks if a Request matches this Route.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function matches(Request $request)
    {
        if ($this->method != Request::All && $this->method != $request->method) {
            return false;
        }

        if (preg_match('/^'.$this->template.'$/is', rtrim($request->url, '/'), $match)) {
            return $match;
        }

        return false;
    }

    /**
     * Checks if the given routing parameters match this Route.
     *
     * @param array $parameters
     *
     * @return bool
     */
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
