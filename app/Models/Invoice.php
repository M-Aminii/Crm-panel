<?php
namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number', 'user_id', 'customer_id',
        'description', 'status','informal_status','delivery','discount',
        'pre_payment','before_delivery',
        'cheque','amount_payable'
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

    public function scopeFormal($query)
    {
        return $query->where('status', InvoiceStatus::Formal);
    }

    // تعریف اسکوپ برای فاکتورهای informal
    public function scopeInformal($query)
    {
        return $query->where('status', InvoiceStatus::InFormal);
    }
    public function scopePrebuy($query)
    {
        return $query->where('status', InvoiceStatus::PreBuy);
    }
    public function userDiscount()
    {
        return $this->hasOne(Access::class, 'user_id', 'user_id');
    }
}

