<?php

namespace Phat\Controller;

use Phat\Core\Configure;
use Phat\Http\Request;
use Phat\Http\Response;
use Phat\Routing\Router;
use Phat\View\View;

class Controller implements ControllerInterface
{
    // TODO : Helpers and Components
    // TODO : easy redirect

    protected $request;
    protected $response;
    protected $view;
    protected $name;

    /**
     * Redirects to the given url.
     *
     * @param array|string $url    An array of parameters (controller, action, plugin, prefix, etc.) or a Route alias
     * @param int          $status The HTTP status to send back (ie: 301 or 302)
     *
     * @return Response
     *
     * @throws \Phat\Http\Exception\UnknownStatusException
     * @throws \Phat\Routing\Exception\BadParameterException
     * @throws \Phat\Routing\Exception\BadRouteException
     */
    public function redirect($url, $status = 302)
    {
        $response = new Response();
        $response->setStatus($status);
        $response->addHeader('Location', Router::url($url, true));

        return $response;
    }

    public function __construct(Request $request = null)
    {
        $this->request = ($request ? $request : new Request());
        $this->response = new Response();

        $nameArray = namespaceSplit(get_class($this));
        $this->name = substr(end($nameArray), 0, -10);

        $controllerView = 'App\\View\\'.$this->name.'View';
        if (class_exists($controllerView)) {
            $this->view = new $controllerView($this->request, $this->name, []);
        } else {
            $this->view = new View($this->request, $this->name, []);
        }
    }

    /**
     * Renders a view with a layout and construct the Response object to be returned to the client.
     * If not specified, the fetched view will correspond to the current action.
     * If not specified, the fetched layout will be the default layout.
     *
     * @param string $view
     * @param string $layout
     *
     * @return Response
     */
    public function render($view = null, $layout = null)
    {
        $this->beforeRender();
        $body = $this->view->render($view, $layout);
        $this->afterRender();

        return new Response(['body' => $body]);
    }

    /**
     * Renders the app/Template/ e404 view and sets the HTTP Status to 404.
     *
     * @param string $view
     * @param string $layout
     *
     * @return Response
     */
    public function e404($view = 'e404', $layout = null)
    {
        $config = Configure::read('App');
        $this->view->viewPath = $config['appDir'].DIRECTORY_SEPARATOR.'Template';
        $this->beforeRender();
        $body = $this->view->render($view, $layout);
        $this->afterRender();

        return new Response\NotFoundResponse(['body' => $body]);
    }

    /**
     * Sends a custom variable to the view template.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value = null)
    {
        $this->view->set($key, $value);
    }

    /**
     * This hook is called before the action method is executed.
     */
    public function beforeAction()
    {
    }

    /**
     * This hook is called after the action method is executed.
     */
    public function afterAction()
    {
    }

    /**
     * This hook is called before the view is rendered.
     * This hook is only called when using Controller::render() method, if you return a custom Response directly from
     * the action, this hook does not apply.
     */
    public function beforeRender()
    {
    }

    /**
     * This hook is called after the view is rendered.
     * This hook is only called when using Controller::render() method, if you return a custom Response directly from
     * the action, this hook does not apply.
     */
    public function afterRender()
    {
    }
}
