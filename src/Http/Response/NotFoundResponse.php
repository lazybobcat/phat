<?php

namespace Phat\Http\Response;

use Phat\Http\Response;

/**
 * Handy shortcut to return 404 Not Found Response.
 */
class NotFoundResponse extends Response
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->setStatus(404);
    }
}
