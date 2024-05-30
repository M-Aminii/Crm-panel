<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyAdminRole() || $user->invoices()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id || $user->hasAnyAdminRole();

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Customer $customer): bool
    {
        if ($customer->status === "Incomplete" || $customer->status === "Inactive") {
            throw new \App\Exceptions\IncompleteOrInactiveCustomerException();
        }

        return $user->id === $customer->user_id || $user->hasAnyAdminRole();
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        //
    }
}
