<?php


require_once 'vendor/autoload.php';
require_once 'src/common.php';

use Phat\Core\Configure;

$config = [
    'debug' => true,
    'App' => [
        // The base url, ie: 'http://example.com' without the final '/'
        'baseUrl' => '',
        'dir' => 'src',
        'webrootDir' => 'tests/test_app/webroot',
        'appDir' => 'tests/test_app/app',
        'pluginsDir' => 'tests/test_app/plugins',
        // The view dir will be situated under appDir/ folder or a specific pluginsDir/<plugin>/ folder
        'viewDir' => 'Template',
        'encoding' => 'utf-8',
    ],
];

Configure::write($config);
