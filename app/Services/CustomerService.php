<?php

namespace App\Services;


use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class CustomerService
{

    public function isDataComplete($customer)
    {
        $requiredFields = ['name', 'national_id', 'registration_number', 'type', 'postal_code', 'address', 'province_id', 'city_id'];

        foreach ($requiredFields as $field) {
            if (empty($customer->$field)) {
                return false;
            }
        }

        return true;
    }






}
