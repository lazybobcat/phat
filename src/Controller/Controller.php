<?php

namespace Phat\Controller;


use Phat\Http\Request;

class Controller implements ControllerInterface {

    // TODO : View class and sending vars to view, easy render, etc.
    // TODO : Helpers and Components
    // TODO : easy 404
    // TODO : easy redirect

    protected   $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function beforeAction() {}

    public function afterAction() {}

    public function beforeRender() {}

    public function afterRender() {}
}