<?php

namespace App\Http\Controllers;

use App\Models\MaQRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QRCodeController extends Controller
{
    public function generate($buoihoc_id)
    {
        $code = MaQRCode::create([
            'code_value' => Str::uuid(),
            'thoi_gian_tao' => now(),
            'han_su_dung' => now()->addMinutes(10),
            'buoihoc_id' => $buoihoc_id
        ]);
        return response()->json(['message' => 'Tạo QR thành công', 'data' => $code]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code_value' => 'required']);
        $qr = MaQRCode::where('code_value', $request->code_value)->first();

        if (!$qr) return response()->json(['valid' => false, 'message' => 'QR không hợp lệ']);
        if (Carbon::now()->greaterThan($qr->han_su_dung)) {
            return response()->json(['valid' => false, 'message' => 'QR đã hết hạn']);
        }

        return response()->json(['valid' => true, 'buoihoc_id' => $qr->buoihoc_id]);
    }
}
