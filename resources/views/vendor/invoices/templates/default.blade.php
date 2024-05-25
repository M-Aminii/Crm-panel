{{--<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $invoice->name }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4, .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4, .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table.table-items td {
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }
        * {
            font-family: "DejaVu Sans";
        }
        body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
            line-height: 1.1;
        }
        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }
        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }
        .border-0 {
            border: none !important;
        }
        .cool-gray {
            color: #6B7280;
        }
    </style>
</head>

<body>
--}}{{-- Header --}}{{--
@if($invoice->logo)
    <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">
@endif

<table class="table mt-5">
    <tbody>
    <tr>
        <td class="border-0 pl-0" width="70%">
            <h4 class="text-uppercase">
                <strong>{{ $invoice->name }}</strong>
            </h4>
        </td>
        <td class="border-0 pl-0">
            @if($invoice->status)
                <h4 class="text-uppercase cool-gray">
                    <strong>{{ $invoice->status }}</strong>
                </h4>
            @endif
            <p>{{ __('invoices::invoice.serial') }} <strong>{{ $invoice->getSerialNumber() }}</strong></p>
            <p>{{ __('invoices::invoice.date') }}: <strong>{{ $invoice->getDate() }}</strong></p>
        </td>
    </tr>
    </tbody>
</table>

--}}{{-- Seller - Buyer --}}{{--
<table class="table">
    <thead>
    <tr>
        <th class="border-0 pl-0 party-header" width="48.5%">
            {{ __('invoices::invoice.seller') }}
        </th>
        <th class="border-0" width="3%"></th>
        <th class="border-0 pl-0 party-header">
            {{ __('invoices::invoice.buyer') }}
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="px-0">
            @if($invoice->seller->name)
                <p class="seller-name">
                    <strong>{{ $invoice->seller->name }}</strong>
                </p>
            @endif

            @if($invoice->seller->address)
                <p class="seller-address">
                    {{ __('invoices::invoice.address') }}: {{ $invoice->seller->address }}
                </p>
            @endif

            @if($invoice->seller->code)
                <p class="seller-code">
                    {{ __('invoices::invoice.code') }}: {{ $invoice->seller->code }}
                </p>
            @endif

            @if($invoice->seller->vat)
                <p class="seller-vat">
                    {{ __('invoices::invoice.vat') }}: {{ $invoice->seller->vat }}
                </p>
            @endif

            @if($invoice->seller->phone)
                <p class="seller-phone">
                    {{ __('invoices::invoice.phone') }}: {{ $invoice->seller->phone }}
                </p>
            @endif

            @foreach($invoice->seller->custom_fields as $key => $value)
                <p class="seller-custom-field">
                    {{ ucfirst($key) }}: {{ $value }}
                </p>
            @endforeach
        </td>
        <td class="border-0"></td>
        <td class="px-0">
            @if($invoice->buyer->name)
                <p class="buyer-name">
                    <strong>{{ $invoice->buyer->name }}</strong>
                </p>
            @endif

            @if($invoice->buyer->address)
                <p class="buyer-address">
                    {{ __('invoices::invoice.address') }}: {{ $invoice->buyer->address }}
                </p>
            @endif

            @if($invoice->buyer->code)
                <p class="buyer-code">
                    {{ __('invoices::invoice.code') }}: {{ $invoice->buyer->code }}
                </p>
            @endif

            @if($invoice->buyer->vat)
                <p class="buyer-vat">
                    {{ __('invoices::invoice.vat') }}: {{ $invoice->buyer->vat }}
                </p>
            @endif

            @if($invoice->buyer->phone)
                <p class="buyer-phone">
                    {{ __('invoices::invoice.phone') }}: {{ $invoice->buyer->phone }}
                </p>
            @endif

            @foreach($invoice->buyer->custom_fields as $key => $value)
                <p class="buyer-custom-field">
                    {{ ucfirst($key) }}: {{ $value }}
                </p>
            @endforeach
        </td>
    </tr>
    </tbody>
</table>

--}}{{-- Table --}}{{--
<table class="table table-items">
    <thead>
    <tr>
        <th scope="col" class="border-0 pl-0">{{ __('invoices::invoice.description') }}</th>
        @if($invoice->hasItemUnits)
            <th scope="col" class="text-center border-0">{{ __('invoices::invoice.units') }}</th>
        @endif
        <th scope="col" class="text-center border-0">{{ __('invoices::invoice.quantity') }}</th>
        <th scope="col" class="text-right border-0">{{ __('invoices::invoice.price') }}</th>
        @if($invoice->hasItemDiscount)
            <th scope="col" class="text-right border-0">{{ __('invoices::invoice.discount') }}</th>
        @endif
        @if($invoice->hasItemTax)
            <th scope="col" class="text-right border-0">{{ __('invoices::invoice.tax') }}</th>
        @endif
        <th scope="col" class="text-right border-0 pr-0">{{ __('invoices::invoice.sub_total') }}</th>
    </tr>
    </thead>
    <tbody>
    --}}{{-- Items --}}{{--
    @foreach($invoice->items as $item)
        <tr>
            <td class="pl-0">
                {{ $item->title }}

                @if($item->description)
                    <p class="cool-gray">{{ $item->description }}</p>
                @endif
            </td>
            @if($invoice->hasItemUnits)
                <td class="text-center">{{ $item->units }}</td>
            @endif
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-right">
                {{ $invoice->formatCurrency($item->price_per_unit) }}
            </td>
            @if($invoice->hasItemDiscount)
                <td class="text-right">
                    {{ $invoice->formatCurrency($item->discount) }}
                </td>
            @endif
            @if($invoice->hasItemTax)
                <td class="text-right">
                    {{ $invoice->formatCurrency($item->tax) }}
                </td>
            @endif

            <td class="text-right pr-0">
                {{ $invoice->formatCurrency($item->sub_total_price) }}
            </td>
        </tr>
    @endforeach
    --}}{{-- Summary --}}{{--
    @if($invoice->hasItemOrInvoiceDiscount())
        <tr>
            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
            <td class="text-right pl-0">{{ __('invoices::invoice.total_discount') }}</td>
            <td class="text-right pr-0">
                {{ $invoice->formatCurrency($invoice->total_discount) }}
            </td>
        </tr>
    @endif
    @if($invoice->taxable_amount)
        <tr>
            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
            <td class="text-right pl-0">{{ __('invoices::invoice.taxable_amount') }}</td>
            <td class="text-right pr-0">
                {{ $invoice->formatCurrency($invoice->taxable_amount) }}
            </td>
        </tr>
    @endif
    @if($invoice->tax_rate)
        <tr>
            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
            <td class="text-right pl-0">{{ __('invoices::invoice.tax_rate') }}</td>
            <td class="text-right pr-0">
                {{ $invoice->tax_rate }}%
            </td>
        </tr>
    @endif
    @if($invoice->hasItemOrInvoiceTax())
        <tr>
            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
            <td class="text-right pl-0">{{ __('invoices::invoice.total_taxes') }}</td>
            <td class="text-right pr-0">
                {{ $invoice->formatCurrency($invoice->total_taxes) }}
            </td>
        </tr>
    @endif
    @if($invoice->shipping_amount)
        <tr>
            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
            <td class="text-right pl-0">{{ __('invoices::invoice.shipping') }}</td>
            <td class="text-right pr-0">
                {{ $invoice->formatCurrency($invoice->shipping_amount) }}
            </td>
        </tr>
    @endif
    <tr>
        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
        <td class="text-right pl-0">{{ __('invoices::invoice.total_amount') }}</td>
        <td class="text-right pr-0 total-amount">
            {{ $invoice->formatCurrency($invoice->total_amount) }}
        </td>
    </tr>
    </tbody>
</table>

@if($invoice->notes)
    <p>
        {{ __('invoices::invoice.notes') }}: {!! $invoice->notes !!}
    </p>
@endif

<p>
    {{ __('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}
</p>
<p>
    {{ __('invoices::invoice.pay_until') }}: {{ $invoice->getPayUntilDate() }}
</p>

<script type="text/php">
    if (isset($pdf) && $PAGE_COUNT > 1) {
        $text = "{{ __('invoices::invoice.page') }} {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
</script>
</body>
</html>--}}
<!-- resources/views/pdf_template.blade.php -->
{{--<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./reset.css" />
    <link rel="stylesheet" href="./style.css" />
    <title>Document</title>
</head>
<style>
    html,
    body,
    div,
    span,
    applet,
    object,
    iframe,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    blockquote,
    pre,
    a,
    abbr,
    acronym,
    address,
    big,
    cite,
    code,
    del,
    dfn,
    em,
    img,
    ins,
    kbd,
    q,
    s,
    samp,
    small,
    strike,
    strong,
    sub,
    sup,
    tt,
    var,
    b,
    u,
    i,
    center,
    dl,
    dt,
    dd,
    ol,
    ul,
    li,
    fieldset,
    form,
    label,
    legend,
    table,
    caption,
    tbody,
    tfoot,
    thead,
    tr,
    th,
    td,
    article,
    aside,
    canvas,
    details,
    embed,
    figure,
    figcaption,
    footer,
    header,
    hgroup,
    menu,
    nav,
    output,
    ruby,
    section,
    summary,
    time,
    mark,
    audio,
    video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline;
    }

    article,
    aside,
    details,
    figcaption,
    figure,
    footer,
    header,
    hgroup,
    menu,
    nav,
    section {
        display: block;
    }

    body {
        line-height: 1;
    }

    ol,
    ul {
        list-style: none;
    }

    blockquote,
    q {
        quotes: none;
    }

    blockquote:before,
    blockquote:after,
    q:before,
    q:after {
        content: "";
        content: none;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    body {
        direction: rtl;

    }

    table,
    th,
    td {
        padding: 0.3rem 0;
        border: 1px solid black;
    }

    main {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 17px;
    }

    table {
        width: 100%;
    }

    span {
        margin: 0 0.5rem;
    }

    .container {
        width: 80%;
        margin: 5rem 0;
        border: 8px solid #85e285;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border: 1px solid #000000;
    }

    .ardakan-logo {
        width: 15rem;
    }

    .omid-logo {
        width: 5rem;
    }

    .invoice-title {
        margin-left: 13rem;
        text-align: center;
    }

    .invoice-title h1 {
        font-weight: bold;
        font-size: 25px;
        white-space: nowrap;
    }

    .invoice-title h2 {
        font-weight: bold;
        font-size: 25px;
        white-space: nowrap;
    }

    .invoice-title h2 {
        font-size: 25px;
        white-space: nowrap;
    }

    .background {
        background-color: #85e285;
    }

    .text-center {
        text-align: center;
    }

    .direction-glass {
        display: flex;
        align-items: center;
        justify-content: space-around;
    }
</style>

<body>
<main>
    <div class="container">
        <div class="header-content">
            --}}{{--<div>
                <img
                    src="./image/ardakan-logo.png"
                    alt="ardakan-logo"
                    class="ardakan-logo"
                />
            </div>--}}{{--
            <div class="invoice-title">
                <h1>گروه کارخانجات شیشه اردکان</h1>
                <h2>نمایندگی شرکت امید</h2>
                <h3>فرم جامع فروش</h3>
            </div>
           --}}{{-- <div>
                <img src="./image/logo.png" alt="omid-logo" class="omid-logo" />
            </div>--}}{{--
        </div>

        <table>
            <tr>
                <td style="width: 29.5%">
                    <span>ثبت کننده:</span>
                    <span></span>
                </td>
                <td>
                    <span>کارشناس:</span>
                    <span></span>
                </td>
                <td>
                    <span>نوع فروش:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تاریخ سفارش:</span>
                    <span></span>
                </td>
                <td>
                    <span></span>
                    <span></span>
                </td>
                <td>
                    <span>تلفن:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>شماره مرجع:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره امکان سنجی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره حواله:</span>
                    <span></span>
                </td>
            </tr>

            <tr class="background">
                <th colspan="3">مشخصات فروشنده</th>
            </tr>

            <tr>
                <td>
                    <span>شخص حقیقی/حقوقی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره اقتصادی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره ثبت/شماره ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تماس:</span>
                    <span></span>
                </td>
                <td>
                    <span>کد پستی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شناسه ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>نشانی:</span>
                    <span></span>
                </td>
            </tr>

            <tr class="background">
                <th colspan="3">مشخصات خریدار</th>
            </tr>

            <tr>
                <td>
                    <span>شخص حقیقی/حقوقی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره اقتصادی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره ثبت/شماره ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تماس:</span>
                    <span></span>
                </td>
                <td>
                    <span>کد پستی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شناسه ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>نشانی:</span>
                    <span></span>
                </td>
            </tr>
        </table>
    </div>
</main>
</body>

</html>--}}
    <!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        b,
        u,
        i,
        center,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
            /* table, */
        caption,
            /* tbody, */
            /* tfoot, */
            /* thead, */
            /* tr, */
            /* th, */
            /* td, */
        article,
        aside,
        canvas,
        details,
        embed,
        figure,
        figcaption,
        footer,
        header,
        hgroup,
        menu,
        nav,
        output,
        ruby,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section {
            display: block;
        }
        body {
            line-height: 1;
        }
        ol,
        ul {
            list-style: none;
        }
        blockquote,
        q {
            quotes: none;
        }
        blockquote:before,
        blockquote:after,
        q:before,
        q:after {
            content: "";
            content: none;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        body {
            direction: rtl;
            font-size: 12px;
            white-space: nowrap;
        }
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
           /* font-size: 17px;*/
        }
        .container {
            width: 100%;
            border: 8px solid #85e285;
            margin: 5rem 0;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid #000000;
        }
        .ardakan-logo {
            width: 2%;
        }

        .omid-logo {
            width: 2%;
        }

        /*.header-content img {
            width: 50px;
        }*/
        .invoice-title {
            /*margin-left: 13rem;*/
            margin-left: 30%;
            text-align: center;
        }
        /*.invoice-title h1 {
            font-weight: bold;
            font-size: 25px;
            white-space: nowrap;
        }*/
        .invoice-title h1 {
            font-weight: bold;
            font-size: 20px;
        }

        .invoice-title h2 {
            font-weight: bold;
            font-size: 20px;
            white-space: nowrap;
            margin-top: 0.5rem
        }

        .invoice-title h2 {
            font-size: 25px;
            white-space: nowrap;
        }
        .invoice-title h3 {
            /* font-size: 20px; */
            margin-top: 0.5rem

        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table, th, td {
            padding: 0.3rem 0;
            border: 1px solid black;
        }
        span {
            margin: 0 0.5rem;
        }
        .background {
            background-color: #85e285;
        }
        .text-center {
            text-align: center;
        }
        .direction-glass {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }


    </style>
</head>
<body>
<main>
    <div class="container">
        <div class="header-content">
            <div>
                <img
                    src="./image/ardakan-logo.png"
                    alt="ardakan-logo"
                    class="ardakan-logo"
                />
            </div>
            <div class="invoice-title">
                <h1>گروه کارخانجات شیشه اردکان</h1>
                <h2>نمایندگی شرکت امید</h2>
                <h3>فرم جامع فروش</h3>
            </div>
            <div>
                <img src="./image/logo.png" alt="omid-logo" class="omid-logo" />
            </div>
        </div>

        <table>
            <tr>
                <td style="width: 29.5%">
                    <span>ثبت کننده:</span>
                    <span></span>
                </td>
                <td>
                    <span>کارشناس:</span>
                    <span></span>
                </td>
                <td>
                    <span>نوع فروش:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تاریخ سفارش:</span>
                    <span></span>
                </td>
                <td>
                    <span></span>
                    <span></span>
                </td>
                <td>
                    <span>تلفن:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>شماره مرجع:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره امکان سنجی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره حواله:</span>
                    <span></span>
                </td>
            </tr>

            <tr class="background">
                <th colspan="3">مشخصات فروشنده</th>
            </tr>

            <tr>
                <td>
                    <span>شخص حقیقی/حقوقی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره اقتصادی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره ثبت/شماره ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تماس:</span>
                    <span></span>
                </td>
                <td>
                    <span>کد پستی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شناسه ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>نشانی:</span>
                    <span></span>
                </td>
            </tr>

            <tr class="background">
                <th colspan="3">مشخصات خریدار</th>
            </tr>

            <tr>
                <td>
                    <span>شخص حقیقی/حقوقی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره اقتصادی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره ثبت/شماره ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>تماس:</span>
                    <span></span>
                </td>
                <td>
                    <span>کد پستی:</span>
                    <span></span>
                </td>
                <td>
                    <span>شناسه ملی:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>نشانی:</span>
                    <span></span>
                </td>
            </tr>
        </table>

        <table style="margin-top: 0.5rem">
            <tr>
                <td class="text-center" style="width: 3rem">a</td>
                <td class="text-center" style="width: 20%">لمینت سفارشی</td>
                <td class="text-center" colspan="5">
                    سوپر کلیر 6 سکوریت + طلق 1/52 + دودی 6 سکوریت
                </td>
            </tr>

            <tr>
                <th class="text-center" rowspan="3" style="width: 3rem; height: 24rem; writing-mode: vertical-lr">
                    ساختار نهایی شیشه
                </th>
                <td class="text-center" rowspan="3">لمینت سفارشی</td>

                <td class="text-center" style="height: 2rem; width: 5rem"></td>

                <td class="text-center" style="height: 2rem; width: 3rem"></td>

                <td class="text-center" style="height: 2rem"></td>

                <td class="text-center" style="height: 2rem; width: 3rem"></td>

                <td class="text-center" style="height: 2rem; width: 5rem"></td>
            </tr>

            <tr>
                <td
                    rowspan="3"
                    class="text-center"
                    style="width: 3rem; font-size: 25px; writing-mode: vertical-lr"
                >
                    <div class="direction-glass">
                <span>
                  <img
                      src="./image/indicating-outside.png"
                      alt=""
                      style="width: 5rem"
                  />
                </span>
                        <span
                            style="
                    border: 1px solid black;
                    border-radius: 4px;
                    padding: 1rem 0.5rem;
                  "
                        >داخل</span
                        >
                    </div>
                </td>
                <td rowspan="1" class="text-center"></td>
                <td rowspan="2" class="text-center">
                    <!-- <img src='' alt="" /> -->
                </td>
                <td class="text-center"></td>
                <td
                    rowspan="2"
                    class="text-center"
                    style="width: 3rem; font-size: 25px; writing-mode: vertical-lr"
                >
                    <div class="direction-glass">
                <span
                    style="
                    border: 1px solid black;
                    border-radius: 4px;
                    padding: 1rem 0.5rem;
                  "
                >بیرون</span
                >
                        <span>
                  <img
                      src="./image/Indicates-inside.png"
                      alt=""
                      style="width: 5rem"
                  />
                </span>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="text-center" style="height: 11rem"></td>
                <td class="text-center"></td>
            </tr>
        </table>

        <table>
            <tr>
                <th
                    rowspan="6"
                    class="text-center"
                    style="width: 3rem; writing-mode: vertical-lr"
                >
                    مشخصات فنی
                </th>
                <td style="width: 25%">
                    <span>نوع لبه:</span>
                    <span></span>
                </td>
                <td>
                    <span>قاب:</span>
                    <span></span>
                </td>
                <td>
                    <span>نقشه ابعاد:</span>
                    <span></span>
                </td>
                <td>
                    <span>نام ابعاد:</span>
                    <span></span>
                </td>
            </tr>

            <tr>
                <td>
                    <span>نوع چسب:</span>
                    <span></span>
                </td>
                <td>
                    <span>ایربالانس:</span>
                    <span></span>
                </td>
                <td>
                    <span>دیدنقشه:</span>
                    <span></span>
                </td>
                <td>
                    <span>شماره حواله:</span>
                    <span></span>
                </td>
            </tr>

            <tr>
                <td>
                    <span>نوع ارسال:</span>
                    <span></span>
                </td>
                <td>
                    <span>نوع خرک:</span>
                    <span></span>
                </td>
                <td>
                    <span>تعداد خرک:</span>
                    <span></span>
                </td>
                <td>
                    <span>کاربرد:</span>
                    <span></span>
                </td>
            </tr>

            <tr>
                <td>
                    <span>تاریخ تحویل هر پارت:</span>
                    <span></span>
                </td>
                <td>
                    <span>تعداد پارت:</span>
                    <span></span>
                </td>
                <td>
                    <span>متراژ تحویل هر پارت:</span>
                    <span></span>
                </td>
                <td>
                    <span>نوع ماشین:</span>
                    <span></span>
                </td>
            </tr>

            <td>
                <span>توضیحات:</span>
                <span></span>
            </td>
        </table>

        <table>
            <tr>
                <th
                    class="text-center"
                    rowspan="3"
                    style="width: 3rem; font-size: 6px; writing-mode: vertical-lr"
                >
                    ملاحضات تولید
                </th>
                <td>
                    <span>پارت تولید:</span>
                    <span></span>
                </td>
                <td style="width: 17%"></td>
                <td>
                    <span>ساختار اولیه:</span>
                    <span></span>
                </td>
                <td style="width: 17%"></td>
                <td colspan="4" style="width: 20rem">
                    <span>کد تولید:</span>
                    <span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>مراحل تولید:</span>
                    <span></span>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table style="margin-top: 1.2rem">
            <tr>
                <th>ردیف</th>
                <th>ارتفاع</th>
                <th>عرض</th>
                <th>تعداد</th>
                <th>position</th>
                <th>مساحت</th>
                <th>مساحت کل</th>
                <th>محیط</th>
                <th>درصد اور</th>
                <th style="width: 38.5%">توضیحات</th>
            </tr>
            <tr>
                <td class="text-center">01</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                @foreach($invoice->items as $item)
                <td class="text-center" colspan="3">جمع کل:</td>
                <td>  {{ $invoice->formatCurrency($item->price_per_unit) }}</td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
                <td></td>
                @endforeach
            </tr>

            <tr>
                <td colspan="10">
                    <span>توضیحات کالا:</span>
                    @foreach($invoice->items as $item)
                    <span> {{ $item->description }}</span>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>
</main>
</body>
</html>

