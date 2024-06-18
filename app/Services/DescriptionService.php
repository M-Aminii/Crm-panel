<?php

namespace App\Services;




use App\Models\GlassLaminate;
use App\Models\GlassLaminateColor;
use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassSpacerColor;
use App\Models\GlassSpacerGlue;
use App\Models\GlassType;
use App\Models\GlassWidth;
use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class DescriptionService
{

    public function convertDescriptions(array $descriptions): array
    {
        foreach ($descriptions as &$description) {
            if (isset($description['type']) && is_numeric($description['type'])) {
                $glassType = GlassType::find($description['type']);
                if ($glassType) {
                    $description['type'] = $glassType->name;
                }
            }
            if (isset($description['width']) && is_numeric($description['width'])) {
                $glassWidth = GlassWidth::find($description['width']);
                if ($glassWidth) {
                    $width = $glassWidth->size;
                    $width = rtrim(rtrim($width, '0'), '.');
                    $description['width'] = is_numeric($width) && floor($width) == $width ? intval($width) : floatval($width);
                }
            }
            if (isset($description['material']) && is_numeric($description['material'])) {
                $glassMaterial = GlassMaterial::find($description['material']);
                if ($glassMaterial) {
                    $description['material'] = $glassMaterial->name;
                }
            }
            if (isset($description['laminate']) && is_numeric($description['laminate'])) {
                $glassLaminate = GlassLaminate::find($description['laminate']);
                if ($glassLaminate) {
                    $description['laminate'] = $glassLaminate->size;
                }
            }
            if (isset($description['laminateColor']) && is_numeric($description['laminateColor'])) {
                $glassLaminateColor = GlassLaminateColor::find($description['laminateColor']);
                if ($glassLaminateColor) {
                    $description['laminateColor'] = $glassLaminateColor->english_name;
                }
            }
            if (isset($description['spacer']) && is_numeric($description['spacer'])) {
                $glassSpacer = GlassSpacer::find($description['spacer']);
                if ($glassSpacer) {
                    $description['spacer'] = $glassSpacer->size;
                }
            }
            if (isset($description['spacerColor']) && is_numeric($description['spacerColor'])) {
                $glassSpacerColor = GlassSpacerColor::find($description['spacerColor']);
                if ($glassSpacerColor) {
                    $description['spacerColor'] = $glassSpacerColor->name;
                }
            }
            if (isset($description['adhesive']) && is_numeric($description['adhesive'])) {
                $glassSpacerGlue = GlassSpacerGlue::find($description['adhesive']);
                if ($glassSpacerGlue) {
                    $description['adhesive'] = $glassSpacerGlue->name;
                }
            }
        }

        return $descriptions;
    }


}
