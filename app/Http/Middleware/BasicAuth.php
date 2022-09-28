<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class BasicAuth
{
    private string $username;

    private string $password;

    abstract protected function getConfigPath(): string;

    public function __construct()
    {
        $config = config($this->getConfigPath());

        if (! isset($config['username']) || ! isset($config['password'])) {
            throw new RuntimeException(
                'Invalid Auth configuration. Class - ' . static::class
            );
        }

        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    /**
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $username = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');

        if ($username !== $this->username || $password !== $this->password) {
            return response()->make(
                'Authentication Required',
                Response::HTTP_UNAUTHORIZED,
                ['WWW-Authenticate' => 'Basic']
            );
        }

        return $next($request);
    }
}
