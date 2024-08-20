<?php

namespace App\Policies;

use App\Models\Order\{Journey, OrderJourney};
use App\Models\User;
use App\Models\Users\{Admin, Buyer, LogisticsPersonnel};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

class OrderJourneyPolicy
{
    use HandlesAuthorization;

    private function samePersonnel(OrderJourney $orderJourney, LogisticsPersonnel $logisticsPersonnel): bool
    {
        if ($orderJourney->has_journey) {
            if (is_null($orderJourney->journey->logisticsPersonnel))
                return false;
            else
                return $orderJourney->journey->logisticsPersonnel->id == $logisticsPersonnel->id;
        }
        return false;
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
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, OrderJourney $orderJourney)
    {
        if ($user instanceof Admin) {
            return true;
        } else if ($user instanceof LogisticsPersonnel) {
            return $this->samePersonnel($orderJourney, $user);
        } else if ($user instanceof Buyer) {
            return $orderJourney->order->buyer_id === $user->id;
        }
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
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Determine whether the user can declare the journey as started.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsDelivered(Authenticatable $user, OrderJourney $orderJourney)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($orderJourney, $user)) {
            if (!$orderJourney->has_journey)
                return Response::deny('Order has not been assigned to a journey yet');
            else if ($orderJourney->was_delivered)
                return Response::deny('Order has already been marked as `delivered`.');
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
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsLeft(Authenticatable $user, OrderJourney $orderJourney)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($orderJourney, $user)) {
            if (!$orderJourney->has_journey)
                return Response::deny('Order has not been assigned to a journey yet');
            else if (!$orderJourney->journey->was_received)
                return Response::deny('Order has already been marked as `received`.');
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
    public function assignToJourney(Authenticatable $user, OrderJourney $orderJourney, Journey $journey)
    {
        if ($user instanceof Admin) {
            if ($journey->destination_town_id !== $orderJourney->order->delivery_town_id)
                return Response::deny("the journey desination is not the same as the order\'s delivery town");
            else if ($journey->has_left)
                return Response::deny('Order has already left for its destination.');
            else if ($journey->has_arrived)
                return Response::deny('Journey has already been marked as `finished`.');
            else if ($orderJourney->was_delivered)
                return Response::deny('Order has already been marked as `delivered`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can add many orders to a journey.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Database\Eloquent\Collection<mixed, \App\Models\Order\OrderJourney>  $orderJourneys
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignManyToJourney(Authenticatable $user, Collection $orderJourneys)
    {
        if ($user instanceof Admin) {
            foreach ($orderJourneys as $orderJourney) {
                if ($orderJourney->was_delivered)
                    return Response::deny("Order with waybill id `{$orderJourney->id}` has already been marked as `delivered`.");
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user update waybill journey.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateJourney(Authenticatable $user, OrderJourney $orderJourney, Journey $journey)
    {
        if ($user instanceof Admin) {
            if (!$orderJourney->is_editable)
                return Response::deny('Unable to update. Perhaps order has been received.');
            if ($journey->has_arrived)
                return Response::deny('Journey has already been marked as `finished`.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update order journey town.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateTown(Authenticatable $user, OrderJourney $orderJourney)
    {
        if ($user instanceof LogisticsPersonnel && $this->samePersonnel($orderJourney, $user)) {
            if (!$orderJourney->is_editable)
                return Response::deny('Unable to update. Perhaps order has been received.');
            else if (!$orderJourney->has_journey)
                return Response::deny('Order has not yet been assigned to a journey.');
            else if ($orderJourney->journey->has_left)
                return Response::deny('Order shipment has already left origin.');
            else
                return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update cost of shipment.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateCost(Authenticatable $user, OrderJourney $orderJourney)
    {
        if ($user instanceof Admin) {
            if (!$orderJourney->is_editable)
                return Response::deny('Unable to update. Perhaps order has been received.');
            else if (!$orderJourney->order->amount_paid > 0)
                return Response::deny('Payment has been made for this order already.');
            else
                return true;
        } else {
            return false;
        }
    }
}
