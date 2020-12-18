<?php

namespace App\Service;

class TokenGeneratorService
{
    public function generate($length = 10): string
    {
        return sha1(random_bytes($length));
    }
}