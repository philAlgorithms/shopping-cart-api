<?php

namespace App\Policies;

use App\Models\Location\Town;
use App\Models\Order\Journey;
use App\Models\User;
use App\Models\Users\{Admin, LogisticsPersonnel};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class JourneyPolicy
{
    use HandlesAuthorization;

    private function samePersonnel(Journey $journey, LogisticsPersonnel $logisticsPersonnel): bool
    {
        return $journey->logisticsPersonnel->id == $logisticsPersonnel->id;
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
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Journey $journey)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        if ($user instanceof Admin) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Journey $journey)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Journey $journey)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Journey $journey)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Journey $journey)
    {
        //
    }

    /**
     * Determine whether the user can set the origin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setOrigin(Authenticatable $user, Journey $journey)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can set the destination.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setDestination(Authenticatable $user, Journey $journey)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can assign logistics personnel.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignLogisticsPersonnel(Authenticatable $user, Journey $journey)
    {
        if ($user instanceof Admin) {
            if ($journey->has_arrived)
                return Response::deny('Journey has already been marked as `finished`.');
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
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsLeft(Authenticatable $user, Journey $journey)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            if ($journey->has_left)
                return Response::deny('Journey has already been marked as `started`.');
            if (!$journey->has_logistics_personnel)
                return Response::deny('Journey has not been assigned a logistics personnel.');
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
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsArrived(Authenticatable $user, Journey $journey)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            if (!$journey->has_left)
                return Response::deny('Journey has not yet started.');
            else if ($journey->has_arrived)
                return Response::deny('Journey has already been marked as `arrived`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can set a checkpoint.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setCheckpoint(Authenticatable $user, Journey $journey, Town $town)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($journey, $user)) {
            if (!$journey->has_arrived)
                return Response::deny('This journey has already reached its end.');
            if (!$journey->has_left)
                return Response::deny('Journey has not yet started.');
            else if ($town->id == $journey->destination->id)
                return Response::deny('Destination cannot be set as a checkpoint.');
            else if ($town->id == $journey->origin->id)
                return Response::deny('Origin cannot be set as a checkpoint.');
            else
                return true;
        } else {
            return false;
        }
    }
}
