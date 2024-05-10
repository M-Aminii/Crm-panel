<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class InvoiceController extends Controller
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
    public function store(Request $request)
    {


        $sellerData= $request->user();
        // دریافت اطلاعات خریدار از ورودی کاربر
        $buyerData = $request->input('buyer');

        $client = new Party([
            'name'          => 'Roosevelt Lloyd',
            'phone'         => '(520) 318-9486',
            'custom_fields' => [
                'note'        => 'IDDQD',
                'business id' => '365#GG',
            ],
        ]);

        $customer = new Party([
            'name'          => $buyerData['name'],
            'address'       => 'The Green Street 12',
            'code'          => '#22663214',
            'custom_fields' => [
                'order number' => '> 654321 <',
            ],
        ]);


        // دریافت اطلاعات آیتم‌ها از ورودی کاربر
        $itemsDatas = $request->input('items');

        // ایجاد شیء جدید از کتابخانه Laravel Invoices
        $invoice = Invoice::make('receipt')
            ->series('BIG')
            ->status(__('invoices::invoice.paid'))
            ->sequence(667)
            ->seller($client)
            ->buyer($customer)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->date(now())
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('ریال')
            ->currencyCode('IRR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',')
            ->currencyDecimalPoint(',')
            ->filename( $customer->name . '_invoice26');



        $items = [];
        foreach ($itemsDatas as $itemData) {

            $item = InvoiceItem::make($itemData['title'])
                ->description(InvoiceService::mergeProductStructures($itemData['description']))
                ->pricePerUnit($itemData['price_per_unit'])
                ->quantity($itemData['quantity']);
                //->discount($itemData['discount'])
            //->TechnicalDetails($itemData['technical_details']);

            $dimensions = [];

            foreach ($itemData['dimensions'] as $key=> $data) {
                $dimensions[] = [
                    'row' => $key+1,
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'quantity' => $data['quantity'],
                    'position' => $data['position']
                ];
            }

            $item->dimensions($dimensions);

            if (!isset($itemData['description'])) {
                dd('not ok description');
            }

            // افزودن آیتم به لیست آیتم‌ها
            $items[] = $item;
        }


        // اضافه کردن تمام آیتم‌ها به فاکتور
        $invoice->addItems($items);
        $invoice->save('public');

        $link = $invoice->url();

        return $invoice->download();

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
