<?php

namespace App\Http\Controllers;

use App\Exceptions\NoStructureInCreateException;
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
                throw new NotFoundHttpException('محصول مورد نظر یافت نشد.');
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
        } catch (NoStructureInCreateException $exception) {
            DB::rollBack();
            return response(['message' => $exception->getMessage()], $exception->getCode());
        }catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }



       /* try {
            DB::beginTransaction();
            $user = auth()->user();
            $product = $request->product_id;
            if (!$product) {
                throw new NotFoundHttpException('محصول انتخابی وجود ندارد');
            }
            $structure = $request->input('structure');
            if (!$structure){
                throw new BadRequestException('برای این ساختار شیشه هیچ مشخصاتی ثبت نشده');
            }
            $newQuestions = GlassStructureService::existQuestions($structure);
            GlassFinalStructure::create(
                ['user_id'=> $user->id],
                ['product_id' => $product], // جستجو بر اساس product_id
                ['structure_data' => $newQuestions] // اطلاعات جدید برای ذخیره
            );
            DB::commit();
            return response()->json(['message' => 'سوالات با موفقیت به آزمون اضافه شد'], 201);
        } catch (NoActiveExamException | NoQuestionInExamException $exception) {
            DB::rollBack();
            return response(['message' => $exception->getMessage()], $exception->getCode());
        }catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }*/
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // بازیابی اطلاعات مدل‌ها بر اساس شناسه محصول
        $modelData = GlassStructureService::retrieveModelData($productId);

        // بررسی نتیجه و ارسال پاسخ به درخواست کننده
        if ($modelData) {
            return response()->json($modelData, 200);
        } else {
            return response()->json(['error' => 'هیچ اطلاعاتی یافت نشد.'], 404);
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
