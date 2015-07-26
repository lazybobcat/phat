<?php

namespace Phat\Controller;

interface ControllerInterface
{
    public function beforeAction();
    public function afterAction();
    public function beforeRender();
    public function afterRender();
}
