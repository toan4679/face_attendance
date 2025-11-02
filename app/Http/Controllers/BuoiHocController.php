<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BuoiHoc;

class BuoiHocController extends Controller
{
    /** Lấy danh sách buổi học */
    public function index()
    {
        return BuoiHoc::all();
    }

    /** Thêm buổi học mới */
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP' => 'required|exists:lophocphan,maLopHP',
            'maGV' => 'required|exists:giangvien,maGV',
            'ngayHoc' => 'required|date',
            'gioBatDau' => 'required',
            'gioKetThuc' => 'required',
            'phongHoc' => 'required|string|max:50',
            'maQR' => 'nullable|string|max:100',
        ]);

        $buoi = BuoiHoc::create($data);
        return response()->json($buoi, 201);
    }

    /** ✅ Lấy lịch dạy của giảng viên */
    public function getByGiangVien($maGV)
    {
        $lichDay = DB::table('buoihoc as b')
            ->join('lophocphan as l', 'b.maLopHP', '=', 'l.maLopHP')
            ->join('monhoc as m', 'l.maMon', '=', 'm.maMon')
            ->select(
                'b.maBuoi',
                'm.tenMon',
                'l.maSoLopHP',
                'b.ngayHoc',
                'b.gioBatDau',
                'b.gioKetThuc',
                'b.phongHoc',
                'b.maQR'
            )
            ->where('b.maGV', $maGV)
            ->orderBy('b.ngayHoc', 'asc')
            ->get();

        if ($lichDay->isEmpty()) {
            return response()->json(['message' => 'Giảng viên chưa có lịch dạy'], 404);
        }

        return response()->json([
            'maGV' => $maGV,
            'tongBuoi' => $lichDay->count(),
            'lichDay' => $lichDay
        ]);
    }
}
