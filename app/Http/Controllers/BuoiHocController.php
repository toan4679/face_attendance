<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BuoiHocController extends Controller
{
    // 🔹 Danh sách buổi học (lọc theo lớp học phần)
    public function index(Request $request)
    {
        $query = BuoiHoc::with(['giangVien', 'lopHocPhan.monHoc'])
            ->orderByRaw("
                FIELD(thu, 'Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7','Chủ nhật')
            ")
            ->orderBy('tietBatDau', 'asc');

        if ($request->has('maLopHP')) {
            $query->where('maLopHP', $request->get('maLopHP'));
        }

        return response()->json($query->get());
    }

    // 🔹 Thêm buổi học
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP'     => 'required|exists:lophocphan,maLopHP',
            'maGV'        => 'nullable|exists:giangvien,maGV',
            'thu'         => 'required|string|max:20',
            'tietBatDau'  => 'required|integer|min:1|max:12',
            'tietKetThuc' => 'required|integer|gte:tietBatDau|max:12',
            'phongHoc'    => 'required|string|max:50',
            'maQR'        => 'nullable|string|max:255',
        ]);

        $conflict = BuoiHoc::where('maLopHP', $data['maLopHP'])
            ->where('thu', $data['thu'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tietBatDau', [$data['tietBatDau'], $data['tietKetThuc']])
                  ->orWhereBetween('tietKetThuc', [$data['tietBatDau'], $data['tietKetThuc']]);
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'tietBatDau' => 'Khung tiết này đã được sử dụng cho buổi học khác trong cùng lớp học phần.',
            ]);
        }

        $buoi = BuoiHoc::create($data);
        return response()->json([
            'message' => '✅ Thêm buổi học thành công',
            'data'    => $buoi,
        ], 201);
    }

    // 🔹 Xem chi tiết
    public function show($id)
    {
        $buoi = BuoiHoc::with(['giangVien', 'lopHocPhan.monHoc'])->findOrFail($id);
        return response()->json($buoi);
    }

    // 🔹 Cập nhật
    public function update(Request $request, $id)
    {
        $buoi = BuoiHoc::findOrFail($id);

        $data = $request->validate([
            'thu'         => 'sometimes|string|max:20',
            'tietBatDau'  => 'sometimes|integer|min:1|max:12',
            'tietKetThuc' => 'sometimes|integer|gte:tietBatDau|max:12',
            'phongHoc'    => 'nullable|string|max:50',
            'maGV'        => 'nullable|exists:giangvien,maGV',
            'maQR'        => 'nullable|string|max:255',
        ]);

        if (isset($data['thu']) || isset($data['tietBatDau']) || isset($data['tietKetThuc'])) {
            $check = BuoiHoc::where('maLopHP', $buoi->maLopHP)
                ->where('thu', $data['thu'] ?? $buoi->thu)
                ->where('maBuoi', '!=', $buoi->maBuoi)
                ->where(function ($q) use ($data, $buoi) {
                    $start = $data['tietBatDau'] ?? $buoi->tietBatDau;
                    $end   = $data['tietKetThuc'] ?? $buoi->tietKetThuc;
                    $q->whereBetween('tietBatDau', [$start, $end])
                      ->orWhereBetween('tietKetThuc', [$start, $end]);
                })
                ->exists();

            if ($check) {
                throw ValidationException::withMessages([
                    'tietBatDau' => 'Khung tiết bị trùng với buổi học khác.',
                ]);
            }
        }

        $buoi->update($data);

        return response()->json([
            'message' => '✅ Cập nhật buổi học thành công',
            'data'    => $buoi,
        ]);
    }

    // 🔹 Xóa
    public function destroy($id)
    {
        BuoiHoc::destroy($id);
        return response()->json(['message' => '🗑 Xóa buổi học thành công']);
    }

    // =====================================
    // 🔸 TẠO MÃ QR CHO BUỔI HỌC
    // =====================================
    public function generateQR($id)
    {
        $buoi = BuoiHoc::find($id);

        if (!$buoi) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $maQR = strtoupper(Str::random(8));
        $buoi->maQR = $maQR;
        $buoi->save();

        return response()->json([
            'message' => '✅ Tạo mã QR thành công',
            'maQR' => $maQR,
            'buoiHoc' => $buoi,
        ]);
    }

    // =====================================
    // 🔸 XÓA MÃ QR (KHI KẾT THÚC BUỔI HỌC)
    // =====================================
    public function clearQR($id)
    {
        $buoi = BuoiHoc::find($id);

        if (!$buoi) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $buoi->maQR = null;
        $buoi->save();

        return response()->json(['message' => '🧹 Đã xóa mã QR thành công']);
    }
}
