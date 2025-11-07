<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichDayController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $maGV = $user->maGV ?? null;

        if (!$maGV) {
            return response()->json(['error' => 'Không tìm thấy mã giảng viên'], 400);
        }

        $lich = DB::table('buoihoc as b')
            ->join('lophocphan as l', 'b.maLopHP', '=', 'l.maLopHP')
            ->join('monhoc as m', 'l.maMon', '=', 'm.maMon')
            ->select(
                'b.maBuoi',
                'm.tenMon',
                'l.maSoLopHP',
                'b.ngayHoc',
                'b.gioBatDau',
                'b.gioKetThuc',
                'b.phongHoc'
            )
            ->where('b.maGV', $maGV)
            ->orderBy('b.ngayHoc')
            ->get();

        return response()->json([
            'giangVien' => $user->hoTen,
            'maGV' => $maGV,
            'soBuoi' => $lich->count(),
            'lichDay' => $lich
        ]);
    }
    public function lichDayHomNay($id)
    {
        // $id là mã giảng viên từ route
        $maGV = $id;

        $today = date('Y-m-d'); // Lấy ngày hôm nay

        $lichHomNay = DB::table('buoihoc as b')
            ->join('lophocphan as l', 'b.maLopHP', '=', 'l.maLopHP')
            ->join('monhoc as m', 'l.maMon', '=', 'm.maMon')
            ->select(
                'b.maBuoi',
                'm.tenMon',
                'l.maSoLopHP',
                'b.ngayHoc',
                'b.gioBatDau',
                'b.gioKetThuc',
                'b.phongHoc'
            )
            ->where('b.maGV', $maGV)
            ->whereDate('b.ngayHoc', $today)
            ->orderBy('b.gioBatDau')
            ->get();

        return response()->json([
            'maGV' => $maGV,
            'ngayHomNay' => $today,
            'soBuoi' => $lichHomNay->count(),
            'lichDayHomNay' => $lichHomNay
        ]);
    }
}
