<?php

namespace App\Services;


use App\Models\GlassLaminate;
use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassType;
use App\Models\GlassWidth;

class GlassStructureService
{
    public static function validateStructure($structureData)
{
    foreach ($structureData as $key => $data) {
        foreach ($data as $columnName => $columnValue) {
            // Check if the column name ends with '_id'
            if (substr($columnName, -3) === '_id') {
                $modelName = ucfirst(str_replace('_id', '', $columnName));
                $model = self::getModelInstance($modelName);
                if (!$model::where('id', $columnValue)->exists()) {
                    return "The $modelName with ID $columnValue does not exist in the database.";
                }
            }
        }
    }

    return null; // No errors
}
    public static function processAndTransformData($structureData)
    {
        // نام‌های متناظر با شناسه‌ها
        $names = [
            'type_id' => 'Type',
            'width_id' => 'Width',
            'material_id' => 'Material',
            'laminate_id' => 'Laminate',
            'spacer_id' => 'Spacer' // اضافه کردن نام جدید
        ];

        // تغییر شناسه‌ها به نام‌ها در داده‌ها
        $indexedStructure = [];
        $layerIndex = 1; // شماره‌گذاری لایه‌ها
        $laminateIndex = 1; // شماره‌گذاری طلق‌ها
        $spacerIndex = 1; // شماره‌گذاری اسپیسرها
        foreach ($structureData as $item) {
            $indexedItem = [];
            foreach ($item as $key => $value) {
                if (isset($names[$key])) {
                    $indexedItem[$names[$key]] = $value;
                }
            }
            // تعیین نام بر اساس شناسه و ایندکس
            if (isset($indexedItem['Laminate'])) {
                $name = "Laminate_$laminateIndex";
                $laminateIndex++;
            } elseif (isset($indexedItem['Spacer'])) {
                $name = "Spacer_$spacerIndex";
                $spacerIndex++;
            } else {
                $name = "Layer_$layerIndex";
                $layerIndex++;
            }
            // اضافه کردن مورد به آرایه
            $indexedStructure[$name] = $indexedItem;
        }

        // تبدیل داده‌های به شکل نهایی به JSON
        $jsonData = json_encode($indexedStructure);

        return $jsonData;
    }


//گرفتن اطلاعات ساختار نهایی شیشه
    public static function retrieveModelData($structureData)
    {
        $result = [];

        foreach ($structureData as $key => $data) {
            foreach ($data as $columnName => $columnValue) {
                    // ایدی مدل را از نام ستون به دست می‌آوریم
                    $modelId = $columnValue;
                    // اگر نام مدل بازگردانده شده، مقدار غیر null داشته باشد، اطلاعات مربوط به آن را به آرایه نتیجه اضافه می‌کنیم
                    if ($columnName !== null) {
                        // اطلاعات مربوط به مدل را به آرایه نتیجه اضافه می‌کنیم
                        $result[$key][$columnName] = self::getModelInstance($columnName)::find($modelId)->toArray();
                    }
            }
        }
        return $result;
    }

    public static function generateTitles($structureData)
    {
        $formattedData = '';

        foreach ($structureData as $layerName => $layerData) {
            foreach ($layerData as $propertyName => $propertyValue) {
                // بررسی نام ویژگی و ایجاد رشته متناظر
                if ($propertyName === 'Type' || $propertyName === 'Material') {
                    $formattedData .= $propertyValue['name'] . ' ';
                } elseif ($propertyName === 'Width') {
                    $formattedData .= ' ' . $propertyValue['size'] . ' ';
                } elseif ($propertyName === 'Spacer') {
                    $formattedData .= ' ' . $propertyValue['size'] . ' ';
                }
            }
            // افزودن جداکننده بین لایه‌ها
            $formattedData .= ' + ';
        }

        // حذف آخرین جداکننده اضافه شده
        $formattedData = rtrim($formattedData, ' + ');

        return $formattedData;
    }



    private static function getModelInstance($modelName)
    {
        switch ($modelName) {
            case 'Type':
                return new GlassType();
            case 'Width':
                return new GlassWidth();
            case 'Material':
                return new GlassMaterial();
            case 'Spacer':
                return new GlassSpacer();
            case 'Laminate':
                return new GlassLaminate();
            default:
                return null;
        }
    }




}
