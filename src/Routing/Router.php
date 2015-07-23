<?php

namespace Phat\Routing;


use Phat\Http\Exception\NotFoundException;
use Phat\Http\Request;
use Phat\Routing\Exception\BadRouteException;

class Router {

    private static $routes = [];
    private static $prefixes = [];


    /**
     * This methods adds a prefix configuration to the routing system
     * @param string $urlKey    The keyword to match in the URL
     * @param string $prefix    The prefix to apply to the controller actions (ie: admin_index())
     */
    public static function prefix($urlKey, $prefix)
    {
        self::$prefixes[$urlKey] = $prefix;
    }


    /**
     * This method parses the Request URL and fills the Request with useful information (controller, acton, parameters, etc.)
     * @param Request $request The Request to parse
     * @return Request
     * @throws NotFoundException
     */
    public static function parse(Request $request)
    {
        // Get a clean URL, just to be sure
        if($request->url !== '/') {
            $request->url = trim($request->url, '/');
        }

        // Check if the request matches a route
        foreach(self::$routes as $r) {
            $match = $r->matches($request);
            if(!empty($match)) {
                $request->plugin        = $r->plugin;
                $request->controller    = $r->controller;
                $request->action        = $r->action;
                array_shift($match);
                $request->parameters    = $match;

                return $request;
            }
        }

        // If the request doesn't match any user route, we try to find the default controller/action that could match
        $params = explode('/', $request->url);

        // Handle prefixes
        if(in_array($params[0], array_keys(self::$prefixes))) {
            $request->prefix = self::$prefixes[$params[0]];
            array_shift($params);
        }

        if(empty($params[0])) {
            throw new NotFoundException("This URL is not connected to any route. Use Router::connect() to fix it.");
        } else {
            $request->controller = $params[0];
        }

        $request->action = empty($params[1]) ? 'index' : $params[1];
        $request->parameters = array_slice($params, 2);

        return $request;
    }


    /**
     * Creates a Route by connecting a templated path/url to a set of {controller, action, prefix, plugn, name}
     * The $parameters keys can be :
     * - controller : The pointed controller name
     * - action     : The pointed controller action
     * - prefix     : The prefix that should be used (leave empty for no prefix)
     * - plugin     : The plugin in which he controller is (leave empty for no plugin)
     * - name       : You can give a name to the Route to get its URL more easily afterwards
     * @param $template
     * @param $parameters
     * @return Route
     * @throws BadRouteException
     */
    public static function connect($template, $parameters)
    {
        $route = new Route();
        $route->template    = trim($template, '/');
        $route->template    = str_replace('/', '\\/', $route->template);
        $route->template    = preg_replace("/(:[a-zA-Z0-9]+)/", "([^\/]+)", $route->template);
        if(empty($parameters['controller'])) {
            throw new BadRouteException("The controller is missing from the Route parameters");
        } else {
            $route->controller = $parameters['controller'];
        }
        $route->action      = empty($parameters['action']) ? 'index' : $parameters['action'];
        $route->prefix      = empty($parameters['prefix']) ? null : $parameters['prefix'];
        $route->plugin      = empty($parameters['plugin']) ? null : $parameters['plugin'];
        if(!empty($parameters['method'])) {
            if(is_string($parameters['method'])) {
                $route->method = strtolower($parameters['method']);
            } else {
                $route->method = $parameters['method'];
            }
        } else {
            $route->method = Request::All;
        }

        // Save the Route
        $name = empty($parameters['name']) ? spl_object_hash($route) : $parameters['name'];
        self::$routes[$name] = $route;

        return $route;
    }

    // TODO : Router::url($name/$parameters)
    // TODO : match template params to regexes
    // TODO : Dispatcher



}