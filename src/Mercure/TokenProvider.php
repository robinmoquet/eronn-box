<?php

namespace App\Mercure;

use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

final class TokenProvider implements TokenProviderInterface
{
    public function getJwt(): string
    {
        return 'eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOltdfX0.Ou9SzHubmQ3DZbRb70XRdiro3GF_KhqE-O60oZ3ytvg';
    }
}
