<?php

namespace App\Http\Controllers;

use App\Exceptions\NoStructureInCreateException;
use App\Exceptions\NotFoundPageException;
use App\Http\Requests\GlassFinalStructure\CreateGlassFinalStructureRequest;
use App\Models\GlassFinalStructure;
use App\Models\Product;
use App\Services\GlassStructureService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlassFinalStructureController extends Controller
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
    public function store(CreateGlassFinalStructureRequest $request)
    {
        try {
            DB::beginTransaction();
            // اطلاعات ارسالی از درخواست
            $product_id = $request->product_id;
            $structureData = $request->structure;

            $product = Product::find($request->product_id);
            if (!$product){
                throw new NotFoundPageException;
            }
            if (!$structureData){
                throw new NoStructureInCreateException;
            }
            $errorMessage = GlassStructureService::validateStructure($structureData);
            if ($errorMessage) {
                return response()->json(['error' => $errorMessage], 422);
            }
            $newStructure = GlassStructureService::processAndTransformData($structureData);


            GlassFinalStructure::create([
                'user_id' =>auth('api')->id(),
                'product_id' => $product_id,
                'structure_data' => $newStructure
            ]);

            DB::commit();
            return response()->json(['message' => 'ساختار نهایی محصول با موفقیت ایجاد شد'], 201);
        } catch (NotFoundPageException |NoStructureInCreateException $exception) {
            DB::rollBack();
            return response(['message' => $exception->getMessage()], $exception->getCode());
        }catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            DB::beginTransaction();
            $result = GlassFinalStructure::where('id', $id)->first();
            if (!$result) {
                throw new NotFoundPageException;
            }
            $dataArray = json_decode($result->structure_data, true);
            // بازیابی اطلاعات مدل‌ها بر اساس شناسه محصول
            $modelData = GlassStructureService::retrieveModelData($dataArray);
            $title = GlassStructureService::generateTitles($modelData);
            DB::commit();
            // بررسی نتیجه و ارسال پاسخ به درخواست کننده
            return response()->json([
                'title' => $title,
                'structure' => $modelData, // ارسال تمامی سوالات به عنوان یک آرایه
            ], 201);
        } catch (NotFoundPageException|NoStructureInCreateException $exception) {
            DB::rollBack();
            return response(['message' => $exception->getMessage()], $exception->getCode());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }
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
