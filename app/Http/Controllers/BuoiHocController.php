<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

        $this->checkConflict($data);

        $buoi = BuoiHoc::create($data);

        return response()->json([
            'message' => '✅ Thêm buổi học thành công',
            'data'    => $buoi,
        ], 201);
    }

    /**
     * 🔹 Tạo nhiều buổi học hàng loạt (theo danh sách ngày & thứ)
     * POST /api/v1/pdt/buoihoc/multiple
     */
    public function storeMultiple(Request $request)
    {
        // 🔸 Cho phép key là 'list' hoặc 'items'
        $list = $request->input('list', $request->input('items', []));

        if (empty($list)) {
            return response()->json(['message' => '⚠️ Danh sách buổi học trống.'], 400);
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
            ];

            // 🔹 Validate cơ bản
            $validated = validator($data, [
                'maLopHP'     => 'required|exists:lophocphan,maLopHP',
                'thu'         => 'required|string|max:20',
                'tietBatDau'  => 'required|integer|min:1|max:12',
                'tietKetThuc' => 'required|integer|gte:tietBatDau|max:12',
                'phongHoc'    => 'required|string|max:50',
            ])->validate();

            // 🔹 Lấy ngày học tương ứng với thứ trong tuần (từ lịch của lớp học phần)
            $lhp = LopHocPhan::find($validated['maLopHP']);
            if (!$lhp || !$lhp->ngayBatDau || !$lhp->ngayKetThuc) {
                throw ValidationException::withMessages([
                    'maLopHP' => 'Lớp học phần không có thông tin ngày bắt đầu/kết thúc.',
                ]);
            }

            $ngayHocList = $this->generateDatesForThu(
                $validated['thu'],
                $lhp->ngayBatDau,
                $lhp->ngayKetThuc
            );

            foreach ($ngayHocList as $ngayHoc) {
                $row = array_merge($validated, [
                    'ngayHoc'   => $ngayHoc->toDateString(),
                    'gioBatDau' => $item['gioBatDau'] ?? null,
                    'gioKetThuc' => $item['gioKetThuc'] ?? null,
                ]);

                // 🔍 Check trùng lịch
                $this->checkConflict($row);

                $created[] = BuoiHoc::create($row);
            }
        }

        return response()->json([
            'message' => '✅ Đã tạo ' . count($created) . ' buổi học thành công.',
            'count'   => count($created),
        ]);
    }

    /**
     * 🔎 Sinh danh sách ngày theo "thứ" trong khoảng
     */
    private function generateDatesForThu($thu, $ngayBatDau, $ngayKetThuc)
    {
        $thuMap = [
            'Thứ 2' => Carbon::MONDAY,
            'Thứ 3' => Carbon::TUESDAY,
            'Thứ 4' => Carbon::WEDNESDAY,
            'Thứ 5' => Carbon::THURSDAY,
            'Thứ 6' => Carbon::FRIDAY,
            'Thứ 7' => Carbon::SATURDAY,
            'Chủ nhật' => Carbon::SUNDAY,
        ];

        $day = $thuMap[$thu] ?? null;
        if (!$day) return [];

        $period = CarbonPeriod::create($ngayBatDau, $ngayKetThuc);
        $dates = [];

        foreach ($period as $date) {
            if ($date->dayOfWeek === $day) {
                $dates[] = Carbon::parse($date);
            }
        }

        return $dates;
    }

    /**
     * 🔍 Kiểm tra trùng lịch học trong cùng lớp học phần
     */
    private function checkConflict($data)
    {
        $exists = BuoiHoc::where('maLopHP', $data['maLopHP'])
            ->where('thu', $data['thu'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tietBatDau', [$data['tietBatDau'], $data['tietKetThuc']])
                  ->orWhereBetween('tietKetThuc', [$data['tietBatDau'], $data['tietKetThuc']]);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'tietBatDau' => '⚠️ Khung tiết này đã được sử dụng trong lớp học phần khác.',
            ]);
        }
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

        $this->checkConflict(array_merge($buoi->toArray(), $data));
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
