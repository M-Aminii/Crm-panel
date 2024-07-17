<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalOrder extends Model
{
    protected $fillable = [
        'invoice_id', 'user_id', 'customer_id', 'serial_number', 'delivery_date',
        'sent_to_factory', 'sent_to_customer', 'informal_invoice_date', 'formal_invoice_date',
        'delivery_time', 'financial_approval_date', 'pdf_map','cad_map','pdf_dimension','xml_dimension'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(FinalOrderItem::class);
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // متد به‌روزرسانی
    public function updateMapsAndDimensions($pdfMapFile, $cadMapFile, $pdfDimensionFile, $xmlDimensionFile)
    {
        $pdfMapPath = $this->storeFile($pdfMapFile, 'maps', 'pdf_map');
        $cadMapPath = $this->storeFile($cadMapFile, 'maps', 'cad_map');
        $pdfDimensionPath = $this->storeFile($pdfDimensionFile, 'dimensions', 'pdf_dimension');
        $xmlDimensionPath = $this->storeFile($xmlDimensionFile, 'dimensions', 'xml_dimension');

        $this->update([
            'pdf_map' => $pdfMapPath,
            'cad_map' => $cadMapPath,
            'pdf_dimension' => $pdfDimensionPath,
            'xml_dimension' => $xmlDimensionPath,
        ]);
    }

    // متد ذخیره‌سازی فایل
    private function storeFile($file, $folder, $field)
    {
        if (!$file) {
            return $this->{$field};
        }

        // حذف فایل قبلی
        if ($this->{$field}) {
            Storage::delete($this->{$field});
        }

        // ساخت نام فایل جدید
        $fileName = $folder . '/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . $this->id . '.' . $file->getClientOriginalExtension();

        // ذخیره‌سازی فایل جدید
        return $file->storeAs($folder, $fileName);
    }

}
