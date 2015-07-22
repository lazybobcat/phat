<?php

namespace Phat\Http\Exception;


class FileNotFoundException extends \Exception {
    public function __construct($msg = "404 Not Found") {
        header("HTTP/1.0 404 Not Found");
        header("Content-Type: text/html");
        parent::__construct($msg);
    }
}