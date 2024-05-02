<?php

namespace App\Services;

use App\Exceptions\NoQuestionInExamException;
use App\Models\DescriptiveQuestions;
use App\Models\GlassLaminate;
use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassType;
use App\Models\GlassWidth;
use App\Models\MultipleChoiceQuestion;

class GlassStructureService
{
    public static function processAndTransformData($structureData)
    {
        // نام‌های متناظر با شناسه‌ها
        $names = [
            'type_id' => 'type',
            'width_id' => 'width',
            'material_id' => 'material',
            'laminate_id' => 'laminate',
            'spacer_id' => 'spacer' // اضافه کردن نام جدید
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
            if (isset($indexedItem['laminate'])) {
                $name = "laminate_$laminateIndex";
                $laminateIndex++;
            } elseif (isset($indexedItem['spacer'])) {
                $name = "spacer_$spacerIndex";
                $spacerIndex++;
            } else {
                $name = "layer_$layerIndex";
                $layerIndex++;
            }
            // اضافه کردن مورد به آرایه
            $indexedStructure[$name] = $indexedItem;
        }

        // تبدیل داده‌های به شکل نهایی به JSON
        $jsonData = json_encode($indexedStructure);

        return $jsonData;
    }

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

    // متد جدیدی برای بازیابی نام مدل از طریق ایدی آن ایجاد می‌کنیم
    private static function getModelName($modelId)
    {
        // دسترسی به مدل مربوط به این ایدی و بازیابی نام مدل
        $glassType = GlassType::find($modelId);
        if ($glassType) {
            return 'Type';
        }

        $glassWidth = GlassWidth::find($modelId);
        if ($glassWidth) {
            return 'Width';
        }

        $glassMaterial = GlassMaterial::find($modelId);
        if ($glassMaterial) {
            return 'Material';
        }

        $glassSpacer = GlassSpacer::find($modelId);
        if ($glassSpacer) {
            return 'Spacer';
        }

        $glassLaminate = GlassLaminate::find($modelId);
        if ($glassLaminate) {
            return 'Laminate';
        }

        // اگر هیچ مدلی با این ایدی پیدا نشد، مقدار null را برگردانید
        return null;
    }

    public static function retrieveModelData($structureData)
    {
        $result = [];

        foreach ($structureData as $key => $data) {
            foreach ($data as $columnName => $columnValue) {
                // چک می‌کنیم آیا نام ستون به '_id' ختم می‌شود یا نه
                if (substr($columnName, -3) === '_id') {
                    // ایدی مدل را از نام ستون به دست می‌آوریم
                    $modelId = $columnValue;
                    // نام مدل را از طریق ایدی آن بازیابی می‌کنیم
                    $modelName = self::getModelName($modelId);
                    // اگر نام مدل بازگردانده شده، مقدار غیر null داشته باشد، اطلاعات مربوط به آن را به آرایه نتیجه اضافه می‌کنیم
                    if ($modelName !== null) {
                        // اطلاعات مربوط به مدل را به آرایه نتیجه اضافه می‌کنیم
                        $result[$key][$modelName] = self::getModelInstance($modelName)::find($modelId);
                    }
                }
            }
        }

        return $result;
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
