<?php

namespace App\Services;

use App\Models\FinalOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class FinalOrderService
{
    public function uploadMapsAndDimensions(FinalOrder $finalOrder, $pdfMapFile, $cadMapFile, $pdfDimensionFile, $xmlDimensionFile)
    {
        $pdfMapPath = $this->storeFile($finalOrder, $pdfMapFile, 'maps', 'pdf_map');
        $cadMapPath = $this->storeFile($finalOrder, $cadMapFile, 'maps', 'cad_map');
        $pdfDimensionPath = $this->storeFile($finalOrder, $pdfDimensionFile, 'dimensions', 'pdf_dimension');
        $xmlDimensionPath = $this->storeFile($finalOrder, $xmlDimensionFile, 'dimensions', 'xml_dimension');

        $finalOrder->update([
            'pdf_map' => $pdfMapPath,
            'cad_map' => $cadMapPath,
            'pdf_dimension' => $pdfDimensionPath,
            'xml_dimension' => $xmlDimensionPath,
        ]);
    }

    private function storeFile(FinalOrder $finalOrder, $file, $folder, $field)
    {
        if (!$file) {
            return $finalOrder->{$field};
        }

        // حذف فایل قبلی
        if ($finalOrder->{$field}) {
            Storage::delete('public/' . $finalOrder->{$field});
        }

        // ساخت نام فایل جدید با استفاده از شناسه finalOrder
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . $finalOrder->id . '.' . $file->getClientOriginalExtension();

        // ذخیره‌سازی فایل جدید در پوشه public/folder/id
        $filePath = $file->storeAs('public/' . $folder . '/' . $finalOrder->id, $fileName);

        // برگرداندن مسیر فایل بدون 'public/'
        return str_replace('public/', '', $filePath);
    }

}



