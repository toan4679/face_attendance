<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BuoiHocController extends Controller
{
    /**
     * 🔹 Danh sách buổi học (lọc theo lớp học phần)
     * GET /api/v1/pdt/buoihoc?maLopHP=...
     */
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


      // Tạo mã QR
    public function generateQR(Request $request, $idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);

    /**
     * 🔹 Thêm buổi học đơn lẻ
     * POST /api/v1/pdt/buoihoc
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


        // 🔍 Kiểm tra trùng lịch — chỉ trùng nếu cùng phòng + cùng thứ + trùng khung tiết
        $conflict = BuoiHoc::where('thu', $data['thu'])
            ->where('phongHoc', $data['phongHoc'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tietBatDau', [$data['tietBatDau'], $data['tietKetThuc']])
                  ->orWhereBetween('tietKetThuc', [$data['tietBatDau'], $data['tietKetThuc']]);
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'tietBatDau' => '⚠️ Khung tiết này đã được sử dụng trong cùng phòng học và cùng thứ.',
            ]);
        }


        // Tạo mã QR ngẫu nhiên
        $maQR = strtoupper(Str::random(8));
        $buoiHoc->maQR = $maQR;
        $buoiHoc->save();

        $buoi = BuoiHoc::create($data);


        return response()->json([
            'message' => '✅ Thêm buổi học thành công',
            'data'    => $buoi,
        ], 201);
    }

    /**
     * 🔹 Tạo nhiều buổi học cùng lúc
     * POST /api/v1/pdt/buoihoc/multiple
     */
    public function storeMultiple(Request $request)
    {
        $list = $request->input('list', []);

        if (empty($list)) {
            return response()->json(['message' => 'Danh sách trống'], 400);
        }

        $created = [];

        foreach ($list as $item) {
            $data = [
                'maLopHP'     => $item['maLopHP'] ?? null,
                'maGV'        => $item['maGV'] ?? null,
                'thu'         => $item['thu'] ?? null,
                'tietBatDau'  => $item['tietBatDau'] ?? null,
                'tietKetThuc' => $item['tietKetThuc'] ?? null,
                'phongHoc'    => $item['phongHoc'] ?? null,
                'ngayHoc'     => $item['ngayHoc'] ?? null,
                'gioBatDau'   => $item['gioBatDau'] ?? null,
                'gioKetThuc'  => $item['gioKetThuc'] ?? null,
            ];

            // ✅ Validate từng dòng
            $validated = validator($data, [
                'maLopHP'     => 'required|exists:lophocphan,maLopHP',
                'thu'         => 'required|string|max:20',
                'tietBatDau'  => 'required|integer|min:1|max:12',
                'tietKetThuc' => 'required|integer|gte:tietBatDau|max:12',
                'phongHoc'    => 'required|string|max:50',
                'ngayHoc'     => 'nullable|date',
                'gioBatDau'   => 'nullable|string|max:10',
                'gioKetThuc'  => 'nullable|string|max:10',
            ])->validate();

            // 🔍 Kiểm tra trùng lịch — chỉ trùng nếu cùng phòng + cùng thứ + trùng tiết
            $conflict = BuoiHoc::where('thu', $validated['thu'])
                ->where('phongHoc', $validated['phongHoc'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('tietBatDau', [$validated['tietBatDau'], $validated['tietKetThuc']])
                      ->orWhereBetween('tietKetThuc', [$validated['tietBatDau'], $validated['tietKetThuc']]);
                })
                ->exists();

            if ($conflict) {
                // ⚠️ Bỏ qua buổi học trùng, không throw lỗi toàn bộ
                continue;
            }

            $created[] = BuoiHoc::create($validated);
        }

        return response()->json([
            'message' => '✅ Đã tạo ' . count($created) . ' buổi học thành công',
            'count'   => count($created),
        ]);
    }


    // Xóa mã QR
    public function clearQR($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);

        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $buoiHoc->maQR = null;
        $buoiHoc->save();

        return response()->json(['message' => 'Đã xóa mã QR thành công']);
    }

    // Lấy chi tiết buổi học
    public function getDetail($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::with(['giangVien', 'lopHocPhan'])->find($idBuoiHoc);

    /**
     * 🔹 Xem chi tiết 1 buổi học
     */
    public function show($id)
    {
        $buoi = BuoiHoc::with(['giangVien', 'lopHocPhan.monHoc'])->findOrFail($id);
        return response()->json($buoi);
    }

    /**
     * 🔹 Cập nhật buổi học
     * PATCH /api/v1/pdt/buoihoc/{id}
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

        // 🔍 Kiểm tra trùng lịch khi update
        if (isset($data['thu']) || isset($data['tietBatDau']) || isset($data['tietKetThuc']) || isset($data['phongHoc'])) {
            $thuCheck = $data['thu'] ?? $buoi->thu;
            $phongCheck = $data['phongHoc'] ?? $buoi->phongHoc;
            $start = $data['tietBatDau'] ?? $buoi->tietBatDau;
            $end   = $data['tietKetThuc'] ?? $buoi->tietKetThuc;

            $check = BuoiHoc::where('thu', $thuCheck)
                ->where('phongHoc', $phongCheck)
                ->where('maBuoi', '!=', $buoi->maBuoi)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('tietBatDau', [$start, $end])
                      ->orWhereBetween('tietKetThuc', [$start, $end]);
                })
                ->exists();

            if ($check) {
                throw ValidationException::withMessages([
                    'tietBatDau' => '⚠️ Khung tiết bị trùng với buổi học khác trong cùng phòng học và cùng thứ.',
                ]);
            }
        }

        $buoi->update($data);

        return response()->json([
            'message' => '✅ Cập nhật buổi học thành công',
            'data'    => $buoi,
        ]);
    }

    /**
     * 🔹 Xóa buổi học
     * DELETE /api/v1/pdt/buoihoc/{id}
     */
    public function destroy($id)
    {
        BuoiHoc::destroy($id);
        return response()->json(['message' => '🗑 Xóa buổi học thành công']);
    }
}
