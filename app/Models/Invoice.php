<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number', 'user_id', 'customer_id', 'position', 'status','amount_payable'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function typeItems()
    {
        return $this->hasMany(TypeItem::class);
    }

    public function technicalItems()
    {
        return $this->hasMany(TechnicalItem::class);
    }

    public function dimensionItems()
    {
        return $this->hasMany(DimensionItem::class);
    }
    public function aggregatedItems()
    {
        return $this->hasMany(AggregatedItem::class);
    }
}

