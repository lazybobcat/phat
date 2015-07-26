<?php

namespace Phat\Controller;

/**
 * If you don't want to use the provided Controller class, you can create your own controllers as long as you
 * implements the ControllerInterface.
 */
interface ControllerInterface
{
    public function beforeAction();
    public function afterAction();
    public function beforeRender();
    public function afterRender();
}
