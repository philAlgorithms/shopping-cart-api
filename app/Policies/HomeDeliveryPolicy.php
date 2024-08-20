<?php

namespace App\Policies;

use App\Models\Order\{HomeDelivery, Journey};
use App\Models\User;
use App\Models\Users\{Admin, LogisticsPersonnel};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class HomeDeliveryPolicy
{
    use HandlesAuthorization;

    private function samePersonnel(HomeDelivery $homeDelivery, LogisticsPersonnel $logisticsPersonnel): bool
    {
        return $homeDelivery->logisticsPersonnel->id == $logisticsPersonnel->id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Determine whether the user can declare the journey as finished.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsArrived(Authenticatable $user, HomeDelivery $journey)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            if (!$journey->has_left)
                return Response::deny('Delivery has not yet started.');
            else if ($journey->has_arrived)
                return Response::deny('Delivery has already been marked as `started`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can declare the journey as started.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsDelivered(Authenticatable $user, HomeDelivery $homeDelivery)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($homeDelivery, $user)) {
            if ($homeDelivery->was_delivered)
                return Response::deny('Delivery has already been marked as `delivered`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can declare the delivery as received.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsReceived(Authenticatable $user, HomeDelivery $homeDelivery)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($homeDelivery, $user)) {
            if ($homeDelivery->was_received)
                return Response::deny('Delivery has already been marked as `received`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can declare the journey as finished.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsLeft(Authenticatable $user, HomeDelivery $homeDelivery)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($homeDelivery, $user)) {
            if (!$homeDelivery->journey->has_left)
                return Response::deny('Order has already been marked as `left`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can add the order to a journey.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignToJourney(Authenticatable $user, HomeDelivery $homeDelivery, Journey $journey)
    {
        if ($user instanceof Admin) {
            if ($journey->has_arrived)
                return Response::deny('Journey has already been marked as `finished`.');
            else if ($homeDelivery->was_delivered)
                return Response::deny('This home delivery has already been marked as `delivered`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can set the origin address.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setOrigin(Authenticatable $user, HomeDelivery $homeDelivery)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($homeDelivery, $user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can set the destination address.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setDestination(Authenticatable $user, HomeDelivery $homeDelivery)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($homeDelivery, $user)) {
            return true;
        } else {
            return false;
        }
    }
}
