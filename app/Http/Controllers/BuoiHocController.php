<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class BuoiHocController extends Controller
{
    /**
     * 🔹 Danh sách buổi học (lọc theo lớp học phần)
     */
    public function index(Request $request)
    {
        $query = BuoiHoc::with(['giangVien', 'lopHocPhan.monHoc'])
            ->orderByRaw("FIELD(thu, 'Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7','Chủ nhật')")
            ->orderBy('tietBatDau', 'asc');

        if ($request->has('maLopHP')) {
            $query->where('maLopHP', $request->get('maLopHP'));
        }

        return response()->json($query->get());
    }

    /**
     * 🔹 Tạo buổi học đơn lẻ
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP'     => 'required|exists:lophocphan,maLopHP',
            'maGV'        => 'nullable|exists:giangvien,maGV',
            'thu'         => 'required|string|max:20',
            'tietBatDau'  => 'required|integer|min:1|max:12',
            'tietKetThuc' => 'required|integer|gte:tietBatDau|max:12',
            'phongHoc'    => 'required|string|max:50',
            'ngayHoc'     => 'nullable|date',
            'gioBatDau'   => 'nullable|string|max:10',
            'gioKetThuc'  => 'nullable|string|max:10',
        ]);

        // Kiểm tra trùng lịch
        $conflict = BuoiHoc::where('thu', $data['thu'])
            ->where('phongHoc', $data['phongHoc'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tietBatDau', [$data['tietBatDau'], $data['tietKetThuc']])
                  ->orWhereBetween('tietKetThuc', [$data['tietBatDau'], $data['tietKetThuc']]);
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'tietBatDau' => '⚠️ Khung tiết đã được sử dụng trong cùng phòng và cùng thứ.',
            ]);
        }

        $buoi = BuoiHoc::create($data);

        return response()->json([
            'message' => '✅ Thêm buổi học thành công',
            'data' => $buoi,
        ], 201);
    }

    /**
     * 🔹 Tạo nhiều buổi học
     */
    public function storeMultiple(Request $request)
    {
        $list = $request->input('list', []);
        if (empty($list)) {
            return response()->json(['message' => 'Danh sách trống'], 400);
        }

        $created = [];
        foreach ($list as $item) {
            $validated = validator($item, [
                'maLopHP'     => 'required|exists:lophocphan,maLopHP',
                'maGV'        => 'nullable|exists:giangvien,maGV',
                'thu'         => 'required|string|max:20',
                'tietBatDau'  => 'required|integer|min:1|max:12',
                'tietKetThuc' => 'required|integer|gte:tietBatDau|max:12',
                'phongHoc'    => 'required|string|max:50',
                'ngayHoc'     => 'nullable|date',
                'gioBatDau'   => 'nullable|string|max:10',
                'gioKetThuc'  => 'nullable|string|max:10',
            ])->validate();

            $conflict = BuoiHoc::where('thu', $validated['thu'])
                ->where('phongHoc', $validated['phongHoc'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('tietBatDau', [$validated['tietBatDau'], $validated['tietKetThuc']])
                      ->orWhereBetween('tietKetThuc', [$validated['tietBatDau'], $validated['tietKetThuc']]);
                })
                ->exists();

            if ($conflict) continue;

            $created[] = BuoiHoc::create($validated);
        }

        return response()->json([
            'message' => '✅ Tạo thành công ' . count($created) . ' buổi học',
            'count' => count($created),
        ]);
    }

    /**
     * 🔹 Tạo mã QR
     */
    public function generateQR($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);
        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $maQR = strtoupper(Str::random(8));
        $buoiHoc->maQR = $maQR;
        $buoiHoc->save();

        return response()->json([
            'message' => '✅ Tạo mã QR thành công',
            'maQR' => $maQR,
            'buoiHoc' => $buoiHoc,
        ]);
    }

    /**
     * 🔹 Xóa mã QR
     */
    public function clearQR($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);
        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $buoiHoc->maQR = null;
        $buoiHoc->save();

        return response()->json(['message' => '✅ Xóa mã QR thành công']);
    }

    /**
     * 🔹 Lấy chi tiết 1 buổi học
     */
    public function getDetail($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::with(['giangVien', 'lopHocPhan.monHoc'])->find($idBuoiHoc);
        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }
        return response()->json($buoiHoc);
    }

    /**
     * 🔹 Cập nhật buổi học
     */
    public function update(Request $request, $id)
    {
        $buoi = BuoiHoc::findOrFail($id);

        $data = $request->validate([
            'thu'         => 'sometimes|string|max:20',
            'tietBatDau'  => 'sometimes|integer|min:1|max:12',
            'tietKetThuc' => 'sometimes|integer|gte:tietBatDau|max:12',
            'phongHoc'    => 'nullable|string|max:50',
            'ngayHoc'     => 'nullable|date',
            'gioBatDau'   => 'nullable|string|max:10',
            'gioKetThuc'  => 'nullable|string|max:10',
            'maGV'        => 'nullable|exists:giangvien,maGV',
        ]);

        $buoi->update($data);

        return response()->json([
            'message' => '✅ Cập nhật buổi học thành công',
            'data' => $buoi,
        ]);
    }

    /**
     * 🔹 Xóa buổi học
     */
    public function destroy($id)
    {
        BuoiHoc::destroy($id);
        return response()->json(['message' => '🗑 Xóa buổi học thành công']);
    }

    /**
     * 🔹 Lấy danh sách sinh viên theo buổi học
     */
    public function getDanhSachSinhVien($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);

        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $maLopHP = $buoiHoc->maLopHP;

        // Lấy danh sách sinh viên của lớp học phần
        $sinhViens = DB::table('sinhvien')
            ->join('sinhvien_lophocphan', 'sinhvien.maSV', '=', 'sinhvien_lophocphan.maSV')
            ->where('sinhvien_lophocphan.maLopHP', $maLopHP)
            ->select(
                'sinhvien.maSV as ma',
                'sinhvien.ten',
                DB::raw("IF(sinhvien.avatar IS NULL OR sinhvien.avatar = '', 'default_avatar.png', sinhvien.avatar) as avatarOrDefault")
            )
            ->get();

        return response()->json($sinhViens);
    }
}
