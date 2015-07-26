<?php

namespace Phat\Http\Response;

use Phat\Http\Response;

class NotFoundResponse extends Response
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->setStatus(404);
    }
}
