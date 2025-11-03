<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;
use Illuminate\Http\Request;

class MonHocController extends Controller
{
    /**
     * 🧾 Lấy danh sách tất cả môn học (kèm tên ngành)
     */
    public function index()
    {
        $monHoc = MonHoc::with('nganh')->get()
            ->map(function ($item) {
                return [
                    'maMon' => $item->maMon,
                    'maSoMon' => $item->maSoMon,
                    'tenMon' => $item->tenMon,
                    'soTinChi' => $item->soTinChi,
                    'moTa' => $item->moTa,
                    'maNganh' => $item->maNganh,
                    'tenNganh' => $item->nganh?->tenNganh ?? null, // ✅ tên ngành thật
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

        return response()->json($monHoc);
    }

    /**
     * ➕ Thêm mới môn học
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'maNganh' => 'required|exists:nganh,maNganh',
            'maSoMon' => 'required|string|max:20|unique:monhoc,maSoMon',
            'tenMon' => 'required|string|max:100',
            'soTinChi' => 'required|integer|min:1',
            'moTa' => 'nullable|string',
        ]);

        $monHoc = MonHoc::create($data);
        $monHoc->load('nganh');

        return response()->json([
            'message' => 'Thêm môn học thành công',
            'data' => [
                'maMon' => $monHoc->maMon,
                'maSoMon' => $monHoc->maSoMon,
                'tenMon' => $monHoc->tenMon,
                'soTinChi' => $monHoc->soTinChi,
                'moTa' => $monHoc->moTa,
                'maNganh' => $monHoc->maNganh,
                'tenNganh' => $monHoc->nganh?->tenNganh,
            ]
        ], 201);
    }

    /**
     * 👁️ Xem chi tiết môn học
     */
    public function show($id)
    {
        $monHoc = MonHoc::with('nganh')->findOrFail($id);

        return response()->json([
            'maMon' => $monHoc->maMon,
            'maSoMon' => $monHoc->maSoMon,
            'tenMon' => $monHoc->tenMon,
            'soTinChi' => $monHoc->soTinChi,
            'moTa' => $monHoc->moTa,
            'maNganh' => $monHoc->maNganh,
            'tenNganh' => $monHoc->nganh?->tenNganh,
        ]);
    }

    /**
     * ✏️ Cập nhật môn học
     */
    public function update(Request $request, $id)
    {
        $monHoc = MonHoc::findOrFail($id);

        $data = $request->validate([
            'maNganh' => 'nullable|exists:nganh,maNganh',
            'maSoMon' => 'nullable|string|max:20|unique:monhoc,maSoMon,' . $id . ',maMon',
            'tenMon' => 'nullable|string|max:100',
            'soTinChi' => 'nullable|integer|min:1',
            'moTa' => 'nullable|string',
        ]);

        $monHoc->update($data);
        $monHoc->load('nganh');

        return response()->json([
            'message' => 'Cập nhật môn học thành công',
            'data' => [
                'maMon' => $monHoc->maMon,
                'maSoMon' => $monHoc->maSoMon,
                'tenMon' => $monHoc->tenMon,
                'soTinChi' => $monHoc->soTinChi,
                'moTa' => $monHoc->moTa,
                'maNganh' => $monHoc->maNganh,
                'tenNganh' => $monHoc->nganh?->tenNganh,
            ]
        ]);
    }

    /**
     * 🗑️ Xóa môn học
     */
    public function destroy($id)
    {
        $monHoc = MonHoc::findOrFail($id);
        $monHoc->delete();

        return response()->json(['message' => 'Xóa môn học thành công']);
    }
}
