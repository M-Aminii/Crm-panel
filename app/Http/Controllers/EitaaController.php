<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Controller
class EitaaController extends Controller
{
    const ET_KEY = 'bot278152:a2a4119a-0f0b-4c1e-8fee-164719121d1a';
    const CHAT_ID = 9847463;

    private function botet($method = "", $datas = [])
    {
        $url = "https://eitaayar.ir/api/" . self::ET_KEY . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);
        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            return json_decode($res);
        }
    }

    public function sendMessage(Request $request)
    {
        $chatid = self::CHAT_ID;
        $msg = $request->input('msg');

        return $this->botet('sendmessage', [
            'chat_id' => $chatid,
            'text' => $msg
        ]);
    }

    public function sendFile(Request $request)
    {
        $chatid = self::CHAT_ID;
        $file = $request->file('file')->getPathname();
        $msg = $request->input('msg');

        return $this->botet('sendfile', [
            'chat_id' => $chatid,
            'file' => new \CURLFile($file),
            'caption' => $msg
        ]);
    }
    public function getMe()
    {
        return $this->botet('getme');
    }

    public function sendToFactory(Request $request, $id)
    {
        $finalOrder = FinalOrder::findOrFail($id);

        // Check which files are available
        $filesToSend = [
            'pdf_map' => $finalOrder->pdf_map,
            'cad_map' => $finalOrder->cad_map
        ];

        $msg = "ارسال فایل‌ها به کارخانه برای سفارش نهایی: " . $finalOrder->serial_number;
        $filesSent = false;

        foreach ($filesToSend as $fileType => $filePath) {
            if ($filePath) {
                $fullFilePath = storage_path('app/' . $filePath); // Assuming the files are stored in the storage/app directory
                if (file_exists($fullFilePath)) {
                    $this->sendFile($fullFilePath, $msg);
                    $filesSent = true;
                }
            }
        }

        if (!$filesSent) {
            $filesToSend = [
                'pdf_dimension' => $finalOrder->pdf_dimension,
                'xml_dimension' => $finalOrder->xml_dimension
            ];

            foreach ($filesToSend as $fileType => $filePath) {
                if ($filePath) {
                    $fullFilePath = storage_path('app/' . $filePath); // Assuming the files are stored in the storage/app directory
                    if (file_exists($fullFilePath)) {
                        $this->sendFile($fullFilePath, $msg);
                        $filesSent = true;
                    }
                }
            }
        }

        if (!$filesSent) {
            return response()->json(['message' => 'فایلی برای ارسال وجود ندارد.'], 404);
        }

        // Update the sent_to_factory flag
        $finalOrder->sent_to_factory = true;
        $finalOrder->save();

        return response()->json(['message' => 'فایل‌ها با موفقیت به کارخانه ارسال شدند.'], 200);
    }
}
