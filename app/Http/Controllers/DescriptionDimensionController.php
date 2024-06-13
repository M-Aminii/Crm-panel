<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescriptionDimension\CreateDescriptionDimensionRequest;
use App\Models\DescriptionDimension;
use Illuminate\Http\Request;

class DescriptionDimensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $descriptionDimensions = DescriptionDimension::all();
        return response()->json($descriptionDimensions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDescriptionDimensionRequest $request)
    {
        DescriptionDimension::create($request->validated());
        return response(['message' => 'توضیحات ابعاد شیشه ثبت شد'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $descriptionDimension = DescriptionDimension::find($id);
        if (is_null($descriptionDimension)) {
            return response()->json(['message' =>'موردی برای نمایش وجود ندارد'], 404);
        }
        return response()->json($descriptionDimension);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $descriptionDimension = DescriptionDimension::find($id);
        if (is_null($descriptionDimension)) {
            return response()->json(['message' =>'موردی برای نمایش وجود ندارد'], 404);
        }

        $descriptionDimension->update($request->validated());
        return response()->json($descriptionDimension);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $descriptionDimension = DescriptionDimension::find($id);
        if (is_null($descriptionDimension)) {
            return response()->json(['message' =>'موردی برای نمایش وجود ندارد'], 404);
        }
        $descriptionDimension->delete();
        return response(['message' => 'توضیحات ابعاد شیشه با موفقیت حذف شد'],200);
    }
}
