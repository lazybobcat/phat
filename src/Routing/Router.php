<?php

namespace Phat\Routing;


use Phat\Http\Exception\NotFoundException;
use Phat\Http\Request;
use Phat\Routing\Exception\BadParameterException;
use Phat\Routing\Exception\BadRouteException;

class Router {

    private static $routes = [];
    private static $prefixes = ['' => ''];


    /**
     * This methods adds a prefix configuration to the routing system
     * @param string $urlKey    The keyword to match in the URL (including slash(es))
     * @param string $prefix    The prefix to apply to the controller actions (ie: admin_index())
     */
    public static function prefix($urlKey, $prefix)
    {
        self::$prefixes[$prefix] = $urlKey;
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

                // Dynamic data
                foreach($r->dynamicData as $attr => $var) {
                    $request->$attr = $match[$r->urlVariables[$var]];
                    unset($match[$r->urlVariables[$var]]);
                }

                $request->parameters    = $match;
                return $request;
            }
        }

        // If the request doesn't match any user route, we try to find the default controller/action that could match
        $params = explode('/', $request->url);

        // Handle prefixes
        if(!empty($params) && in_array($params[0], array_keys(self::$prefixes))) {
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
     * - method     : The HTTP method the Route must use. Default Request::All. @see Request
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
        $route->pattern     = $route->template;
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

        // Check if prefix exists
        if(!empty($route->prefix) && empty(self::$prefixes[$route->prefix])) {
            throw new BadRouteException("The Route is using the prefix '$route->prefix' but this prefix hasn't been declared. Please use Router::prefix() first.");
        }

        // Extract dynamic data
        preg_match_all('/:[a-zA-Z0-9]+/', trim($template, '/'), $urlVariables);
        if(!empty($urlVariables)) {
            $urlVariables = current($urlVariables);
            foreach($urlVariables as $k => $var) {
                $route->urlVariables[$var] = $k;
            }
        }
        foreach($parameters as $p => $v) {
            if(false !== strpos($v, ':')) {
                $route->dynamicData[$p] = $v;
            }
        }

        // Save the Route
        $name = empty($parameters['name']) ? spl_object_hash($route) : $parameters['name'];
        self::$routes[$name] = $route;

        return $route;
    }


    /**
     * Connects the template to an action by forcing the HTTP method to GET
     * @see Router::connect()
     * @param $template
     * @param $parameters
     * @return Route
     * @throws BadRouteException
     */
    public static function get($template, $parameters)
    {
        $parameters = array_merge($parameters, ['method' => Request::Get]);
        return self::connect($template, $parameters);
    }


    /**
     * Connects the template to an action by forcing the HTTP method to POST
     * @see Router::connect()
     * @param $template
     * @param $parameters
     * @return Route
     * @throws BadRouteException
     */
    public static function post($template, $parameters)
    {
        $parameters = array_merge($parameters, ['method' => Request::Post]);
        return self::connect($template, $parameters);
    }


    public static function url($parameters)
    {
        // TODO : ability to return full URL
        $url = '/';

        if(is_string($parameters)) {
            return self::urlFromName($parameters, $url);
        } elseif(!is_array($parameters)) {
            throw new BadParameterException("The Router::url() method only takes array or string parameters. Read the documentation for more information.");
        }

        if(!empty($parameters['name'])) {
            return self::urlFromName($parameters['name'], $url);
        }

        foreach(self::$routes as $r) {
            if($r->equals($parameters)) {
                return self::urlFromParameters($parameters, $r, $url);
            }
        }

        throw new BadRouteException("The Route you're asking an URL from does not exist. Please check you did pass all the needed parameters or add the Route with Router::connect() first.");
    }

    /**
     * @param $name
     * @param $url
     * @return string
     * @throws BadParameterException
     */
    private static function urlFromName($name, $url) {
        if (empty(self::$routes[$name])) {
            throw new BadParameterException("The named route you're asking for does not exist. Please add the Route with Router::connect() first.");
        }
        $r = self::$routes[$name];
        $url .= $r->pattern;
        if(!empty($r->prefix)) {
            $url = self::$prefixes[$r->prefix].$url;
        }
        return $url;
    }

    /**
     * @param $parameters
     * @param $route
     * @param $url
     * @return string
     * @throws BadParameterException
     * @throws BadRouteException
     */
    private static function urlFromParameters($parameters, $route, $url) {
        $url .= $route->pattern;
        preg_match_all('/:[a-zA-Z0-9]+/', $route->pattern, $urlVariables);
        if (!empty($urlVariables)) {
            $urlVariables = current($urlVariables);
            foreach ($urlVariables as $var) {
                $varname = substr($var, 1);
                if (empty($parameters[$varname])) {
                    throw new BadParameterException("The parameter '$varname' is missing for the Router::url() to work.");
                }
                $url = str_replace("$var", $parameters[$varname], $url);
            }
            if(!empty($parameters['prefix'])) {
                $url = self::$prefixes[$parameters['prefix']].$url;
            }
            return $url;
        }
        throw new BadRouteException("The Route you're asking an URL from does not exist. Please add the Route with Router::connect() first.");
    }

    // TODO : match template params to regexes
    // TODO : Dispatcher



}