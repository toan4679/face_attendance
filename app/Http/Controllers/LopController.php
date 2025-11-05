<?php

namespace App\Http\Controllers;

use App\Models\Lop;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SinhVienImport;
use Illuminate\Support\Facades\Log;

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
        try {
            $sinhvien = \App\Models\SinhVien::where('maLop', $maLop)->get();
            return response()->json($sinhvien);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Lỗi khi lấy danh sách sinh viên.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function importSinhVienExcel(Request $request, $maLop)
{

    // 🧩 Kiểm tra có file gửi lên không
    if (!$request->hasFile('file')) {
        Log::warning("⚠️ Không tìm thấy file trong request multipart.");

        // Nếu Flutter web gửi dạng bytes (string hoặc stream)
        if ($request->has('file')) {
            $tempPath = storage_path('app/temp_upload_' . time() . '.xlsx');
            file_put_contents($tempPath, $request->file);

            Log::info("📄 Tạo file tạm thành công tại $tempPath");

            $file = new \Illuminate\Http\UploadedFile(
                $tempPath,
                'temp.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            );
        } else {
            Log::error("❌ Không có file gửi lên trong cả form-data và body.");
            return response()->json(['message' => 'Không có file được gửi lên.'], 400);
        }
    } else {
        $file = $request->file('file');
        Log::info("✅ Laravel nhận được file: " . $file->getClientOriginalName());
        Log::info("📦 MIME: " . $file->getMimeType() . " | Size: " . $file->getSize());
    }

    try {
        Excel::import(new \App\Imports\SinhVienImport($maLop), $file);

        Log::info("✅ Import sinh viên thành công cho lớp $maLop");

        return response()->json([
            'message' => '✅ Import sinh viên thành công!',
            'file_name' => $file->getClientOriginalName(),
        ]);
    } catch (\Throwable $e) {
        Log::error("❌ Lỗi khi import file Excel: " . $e->getMessage());
        Log::error($e->getTraceAsString());

        return response()->json([
            'message' => '❌ Lỗi khi import file.',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}

}
