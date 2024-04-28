<?php

namespace App\Services;



class FilterService
{

    public static function ApplyFilterCustomers($query,$filters, $request)
    {
        foreach ($filters as $filter => $column) {
            if ($request->filled($filter)) {
                $query->where($column, 'like', '%' . $request->input($filter) . '%');
            }
        }

        return $query;
    }
}
