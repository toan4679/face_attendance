<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\KhuonMat;
use Maatwebsite\Excel\Facades\Excel;

class KhuonMatController extends Controller
{
    // ✅ Lấy danh sách ảnh sinh viên
    public function index(Request $request)
    {
        $query = SinhVien::with(['lop.nganh.khoa', 'khuonmat']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('hoTen', 'LIKE', "%$search%")
                  ->orWhere('maSo', 'LIKE', "%$search%");
            });
        }

        if ($lop = $request->input('lop')) {
            $query->where('maLop', $lop);
        }

        if ($khoa = $request->input('khoa')) {
            $query->whereHas('lop.nganh.khoa', fn($q) => $q->where('maKhoa', $khoa));
        }

        $result = $query->get()->map(function ($sv) {
            return [
                'maSV' => $sv->maSV,
                'maSo' => $sv->maSo,
                'hoTen' => $sv->hoTen,
                'lop' => $sv->lop->tenLop ?? null,
                'khoa' => $sv->lop->nganh->khoa->tenKhoa ?? null,
                'duongDanAnh' => $sv->khuonmat->duongDanAnh ?? null,
            ];
        });

        return response()->json([
            'message' => 'Danh sách khuôn mặt sinh viên',
            'data' => $result
        ]);
    }

        // ✅ Upload hoặc cập nhật ảnh khuôn mặt
    public function updatePhoto(Request $request, $maSV)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $file = $request->file('photo');
        $filePath = $file->store('faces', 'public');

        $khuonmat = KhuonMat::updateOrCreate(
            ['maSV' => $maSV],
            ['duongDanAnh' => 'storage/' . $filePath]
        );

        return response()->json([
            'message' => 'Cập nhật ảnh khuôn mặt thành công!',
            'duongDanAnh' => asset($khuonmat->duongDanAnh)
        ]);
    }

        // ✅ Import danh sách sinh viên chưa có ảnh
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'maLop' => 'required|integer'
        ]);

        Excel::import(new \App\Imports\SinhVienImport($request->maLop), $request->file('file'));

        return response()->json(['message' => 'Import danh sách sinh viên thành công']);
    }
}

