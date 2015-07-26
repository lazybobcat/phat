<?php

namespace Phat\Http;

class Request
{
    public $url;
    public $method = self::Get;
    public $data = [];
    public $plugin = null;
    public $controller = null;
    public $action = null;
    public $prefix = null;
    public $parameters = [];

    const Get = 'get';
    const Post = 'post';
//    const Put = 'put';
//    const Delete = 'delete';
    const All = 'all';
    const Unknown = -1;

    public function __construct()
    {
        // Requested URI
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->url = $_SERVER['REQUEST_URI'];
        } else {
            $this->url = '';
        }

        // Used Method
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->method = self::Post;
                break;

            case 'GET':
                $this->method = self::Get;
                break;

            default:
                $this->method = self::Unknown;
        }

        if (isset($_POST)) {
            $this->data = $_POST;
        }
    }
}
