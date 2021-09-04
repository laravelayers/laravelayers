<?php

namespace Laravelayers\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Str;
use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Contracts\Auth\UserService as UserServiceContract;

class ServiceUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The user service implementation.
     *
     * @var \Laravelayers\Contracts\Auth\UserService
     */
    protected $service;

    /**
     * Create a new service user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  \Laravelayers\Contracts\Auth\UserService  $service
     * @return void
     */
    public function __construct(HasherContract $hasher, UserServiceContract $service)
    {
        $this->hasher = $hasher;
        $this->service = $service;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->service->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $result = $this->service->getResult();

        $user = $this->service->whereCredentials($result->getAuthIdentifierName(), $identifier)->first();

        if (! $user) {
            return;
        }

        $rememberToken = $user->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|UserDecorator  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $this->service->save($user);

        $user->timestamps = $timestamps;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                array_key_exists($this->service->getResult()->getPasswordColumn(), $credentials))) {
            return null;
        }

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $this->service->whereCredentials($key, $value);
            }
        }

        return $this->service->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|UserDecorator  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        if ($user->isEmpty()) {
            return false;
        }

        $plain = $credentials[$user->getPasswordColumn()];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Handle dynamic method calls into the provider.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->service->{$method}(...$parameters);
    }
}
