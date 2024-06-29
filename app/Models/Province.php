<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = 'provinces';

    protected $fillable = ['name'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

   /*    public function customers()
    {
        return $this->hasMany(Customer::class);
    }*/
    public function cities()
    {
        return $this->hasMany(Cities::class);
    }

}
