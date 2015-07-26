<?php

namespace Phat\Routing;

use Phat\Http\Exception\NotFoundException;
use Phat\Http\Request;
use Phat\Routing\Exception\DispatchException;

class Dispatcher
{
    public static function dispatch(Request $request)
    {
        $controller = self::loadController($request);
        $action = $request->action;

        if (!empty($request->prefix)) {
            $action = $request->prefix.'_'.$request->action;
        }

        if (!method_exists($controller, $action)) {
            throw new NotFoundException("The controller '$request->controller' has no method '$action'.");
        }

        // Everything is fine, time to do some Controller action !
        $controller->beforeAction();
        $response = call_user_func_array(array($controller, $action), $request->parameters);
        $controller->afterAction();

        // And rendering
        $controller->beforeRender();
        $response->send();
        $controller->afterRender();
    }

    private static function loadController(Request $request)
    {
        $ctrlName = $request->controller;

        if (!class_exists($ctrlName)) {
            throw new NotFoundException("The controller '$ctrlName' hasn't been found. Please make sure the namespace is included in the name and the class does exist");
        }
        if (!is_subclass_of($ctrlName, 'Phat\Controller\ControllerInterface')) {
            throw new DispatchException('Controllers must implement Phat\\Controller\\ControllerInterface.');
        }

        return new $ctrlName($request);
    }
}
