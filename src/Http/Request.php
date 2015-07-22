<?php

namespace Phat\Http;


class Request {

    public $url;
    public $method;
    public $data = [];

    const Get = 0;
    const Post = 1;
//    const Put = 2;
//    const Delete = 3;
    const Unknown = -1;


    public function __construct()
    {
        // Requested URI
        if(isset($_SERVER['REQUEST_URI'])) {
            $this->url = $_SERVER['REQUEST_URI'];
        } else {
            $this->url = '';
        }

        // Used Method
        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->method = self::Post;
                break;

            case 'GET':
                $this->method = self::Get;
                break;

            default:
                $this->method = self::Unknown;
        }

        if(isset($_POST))
            $this->data = $_POST;
    }

}