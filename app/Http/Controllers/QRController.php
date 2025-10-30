<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class QRController extends Controller
{
    public function generate(Request $request, $maBuoi)
    {
        // Link tạm thời (5 phút)
        $expires = now()->addMinutes(5);
        $signedUrl = URL::temporarySignedRoute(
            'qr.signed', $expires, ['maBuoi' => $maBuoi, 'nonce' => bin2hex(random_bytes(8))]
        );
        // FE dùng thư viện qrcode để render $signedUrl thành ảnh QR
        return response()->json([
            'url' => $signedUrl,
            'expires_at' => $expires->toIso8601String()
        ]);
    }

    public function close($maBuoi)
    {
        // Nếu cần: cập nhật trạng thái "đóng cửa sổ QR" trong bảng buoihoc
        return response()->json(null, 204);
    }
}
