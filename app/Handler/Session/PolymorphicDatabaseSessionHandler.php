<?php
namespace App\Handler\Session;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\DatabaseSessionHandler;

class PolymorphicDatabaseSessionHandler extends DatabaseSessionHandler
{

    /**
     * Add the user information to the session payload.
     *
     * @param  array  $payload
     * @return $this
     */
    protected function addUserInformation(&$payload)
    {
        if ($this->container->bound(Guard::class)) {
            info(($this->user() ? get_class($this->user()) : 'no user'));
            $payload['userable_type'] = $this->user() ? get_class($this->user()) : null;
            $payload['userable_id'] = $this->userId();
        }

        return $this;
    }

    /**
     * Get the currently authenticated user's ID.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function user(): mixed
    {
        return $this->container->make(Guard::class)->user();
    }
}