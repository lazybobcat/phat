<?php

namespace Phat\Http;

/**
 * The Request class represents the client's HTTP request and more.
 * It contains information about the HTTP Request as well as Routing and Dispatching data.
 */
class Request
{
    /**
     * @var string The url asked by the client
     */
    public $url;

    /**
     * @var int|string The HTTP Method
     */
    public $method = self::Get;

    /**
     * @var array POST data if provided
     */
    public $data = [];

    /**
     * @var string The plugin the Request should route to
     */
    public $plugin = null;

    /**
     * @var string The controller the Request should route to
     */
    public $controller = null;

    /**
     * @var string The action the Request should route to
     */
    public $action = null;

    /**
     * @var string The prefix the Request should route to
     */
    public $prefix = null;

    /**
     * @var array The parameters that are passed to the action
     */
    public $parameters = [];

    /**
     * HTTP Get Method.
     */
    const Get = 'get';

    /**
     * HTTP Post Method.
     */
    const Post = 'post';
//    const Put = 'put';
//    const Delete = 'delete';

    /**
     * When any method should match.
     */
    const All = 'all';

    /**
     * Unhandled HTTP Method.
     */
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
