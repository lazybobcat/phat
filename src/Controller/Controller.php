<?php

namespace Phat\Controller;


use Phat\Http\Request;

class Controller implements ControllerInterface {

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