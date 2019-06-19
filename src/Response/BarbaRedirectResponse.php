<?php

declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class BarbaRedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('');
        $this->headers->set('X-XHR-Redirected-To', $url);
    }
}
