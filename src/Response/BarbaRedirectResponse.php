<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class BarbaRedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('');
        $this->headers->set('redirect-to', $url);
    }
}
