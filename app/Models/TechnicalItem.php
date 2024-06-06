<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalItem extends Model
{

    use HasFactory;

    protected $fillable = [
        'invoice_id', 'type_id', 'edge_type', 'glue_type', 'post_type',
        'delivery_date', 'frame', 'balance', 'vault_type',
        'map_dimension', 'map_view', 'usage',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function typeItem()
    {
        return $this->belongsTo(TypeItem::class, 'type_id');
    }
}
