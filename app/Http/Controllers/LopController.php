<?php

namespace App\Http\Controllers;

use App\Models\Lop;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SinhVienImport;

class LopController extends Controller
{
    public function index()
    {
        $lop = Lop::with('nganh')->get()->map(function ($item) {
            return [
                'maLop' => $item->maLop,
                'maSoLop' => $item->maSoLop,
                'tenLop' => $item->tenLop,
                'maNganh' => $item->maNganh,
                'tenNganh' => $item->nganh->tenNganh ?? '',
                'khoaHoc' => $item->khoaHoc,
                'coVan' => $item->coVan,
            ];
        });
        return response()->json($lop);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maSoLop' => 'required|string|unique:lop,maSoLop',
            'tenLop' => 'required|string',
            'maNganh' => 'required|exists:nganh,maNganh',
            'khoaHoc' => 'required|string',
            'coVan' => 'nullable|string',
        ]);

        $lop = Lop::create($data);
        return response()->json(['message' => 'Tạo lớp thành công', 'lop' => $lop], 201);
    }

    public function update(Request $request, $id)
    {
        $lop = Lop::findOrFail($id);
        $data = $request->validate([
            'maSoLop' => 'required|string|unique:lop,maSoLop,' . $id . ',maLop',
            'tenLop' => 'required|string',
            'maNganh' => 'required|exists:nganh,maNganh',
            'khoaHoc' => 'required|string',
            'coVan' => 'nullable|string',
        ]);

        $lop->update($data);
        return response()->json(['message' => 'Cập nhật lớp thành công']);
    }

    public function destroy($id)
    {
        $lop = Lop::findOrFail($id);
        $lop->delete();
        return response()->json(['message' => 'Xóa lớp thành công']);
    }

    public function getSinhVienByLop($maLop)
    {
        $lop = Lop::with('sinhviens')->find($maLop);

        if (!$lop) {
            return response()->json(['message' => 'Không tìm thấy lớp học.'], 404);
        }

        return response()->json(
            $lop->sinhviens->map(function ($sv) {
                return [
                    'maSV' => $sv->maSV,
                    'hoTen' => $sv->hoTen,
                    'email' => $sv->email,
                    'gioiTinh' => $sv->gioiTinh,
                    'khoaHoc' => $sv->khoaHoc,
                ];
            })
        );
    }

    public function importSinhVienExcel(Request $request, $maLop)
    {
        // Kiểm tra có file không
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'Không có file được gửi lên.'], 400);
        }

        $file = $request->file('file');

        // Kiểm tra định dạng hợp lệ
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return response()->json(['message' => 'Chỉ chấp nhận file Excel (.xls, .xlsx).'], 400);
        }

        try {
            // Import dữ liệu sinh viên
            Excel::import(new SinhVienImport($maLop), $file);

            return response()->json([
                'message' => '✅ Import sinh viên thành công!',
                'file_name' => $file->getClientOriginalName(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Lỗi khi import file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
