<?php

namespace App\Http\Controllers\Glass;

use App\DTO\Glass\LayerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Glass\CreateGlassLayerRequest;
use App\Models\GlassLayer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Layer extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateGlassLayerRequest $request)
    {
        try {
            DB::beginTransaction();
            $layerDTO = new LayerDTO($request->validated());
            GlassLayer::create((array) $layerDTO);
            DB::commit();
            return response(['message' => 'مشخصات شیشه مشخص شد'], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
