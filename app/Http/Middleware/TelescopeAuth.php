<?php

namespace App\Http\Middleware;

final class TelescopeAuth extends BasicAuth
{
    protected function getConfigPath(): string
    {
        return 'telescope.auth';
    }
}
