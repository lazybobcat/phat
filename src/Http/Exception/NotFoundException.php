<?php

namespace Phat\Http\Exception;

class NotFoundException extends \Exception
{
    public function __construct($msg = '404 Not Found')
    {
        header('HTTP/1.0 404 Not Found');
        parent::__construct($msg);
    }
}
