<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;
use App\Models\BuoiHoc;
use App\Models\DiemDanh;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SinhVienController extends Controller
{
    /**
     * 🔹 Lấy danh sách sinh viên
     */
    public function index()
    {
        try {
            $sinhViens = SinhVien::with('lop', 'nganh')->get();

            Log::info('[SinhVienController] Lấy danh sách sinh viên thành công.');

            return response()->json([
                'success' => true,
                'total' => $sinhViens->count(),
                'data' => $sinhViens,
            ]);
        } catch (\Exception $e) {
            Log::error('[SinhVienController] Lỗi lấy danh sách sinh viên: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sinh viên.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Dashboard - Lịch học hôm nay của sinh viên
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $lichHoc = DB::table('dangkyhoc')
            ->join('lophocphan', 'dangkyhoc.maLopHP', '=', 'lophocphan.maLopHP')
            ->join('monhoc', 'lophocphan.maMon', '=', 'monhoc.maMon')
            ->join('buoihoc', 'lophocphan.maLopHP', '=', 'buoihoc.maLopHP')
            ->leftJoin('giangvien', 'lophocphan.maGV', '=', 'giangvien.maGV')
            ->leftJoin('diemdanh', function ($join) use ($user) {
                $join->on('buoihoc.maBuoi', '=', 'diemdanh.maBuoi')
                    ->where('diemdanh.maSV', '=', $user->maSV);
            })
            ->whereDate('buoihoc.ngayHoc', $today)
            ->select(
                'monhoc.tenMon as monHoc',
                'buoihoc.phongHoc',
                'buoihoc.gioBatDau',
                'buoihoc.gioKetThuc',
                'giangvien.hoTen as tenGV',
                DB::raw("COALESCE(diemdanh.trangThai, 'Chưa điểm danh') as trangThai")
            )
            ->groupBy(
                'buoihoc.maBuoi',
                'monhoc.tenMon',
                'buoihoc.phongHoc',
                'buoihoc.gioBatDau',
                'buoihoc.gioKetThuc',
                'giangvien.hoTen',
                'diemdanh.trangThai'
            )
            ->get();

        Log::info("[Dashboard] Sinh viên {$user->maSV} - số buổi học hôm nay: " . $lichHoc->count());

        return response()->json([
            'today' => $today,
            'classes' => $lichHoc
        ]);
    }

    /**
     * 🔹 Lấy toàn bộ lịch học của sinh viên
     */
    public function lichHoc(Request $request)
    {
        $sv = $request->user();
        $lich = DangKyHoc::with('lophocphan.buoihoc')
            ->where('maSV', $sv->maSV)
            ->get();

        Log::info("[SinhVienController] Lấy lịch học của SV: {$sv->maSV}");

        return response()->json($lich);
    }

    /**
     * 🔹 Thống kê Dashboard
     */
    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof SinhVien)) {
            return response()->json(['error' => 'Chỉ sinh viên mới được truy cập API này.'], 403);
        }

        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        $lopDangKy = DangKyHoc::where('maSV', $user->maSV)->pluck('maLopHP')->toArray();

        $todayClasses = BuoiHoc::whereIn('maLopHP', $lopDangKy)
            ->whereDate('ngayHoc', $today)
            ->count();

        $attendanceRecords = DiemDanh::where('maSV', $user->maSV)
            ->whereBetween('ngayDiemDanh', [$weekStart, $weekEnd])
            ->get();

        $presentCount = $attendanceRecords->where('trangThai', 'Có mặt')->count();
        $absentCount = $attendanceRecords->where('trangThai', 'Vắng')->count();
        $lateCount = $attendanceRecords->where('trangThai', 'Đi muộn')->count();

        $weekRemaining = BuoiHoc::whereIn('maLopHP', $lopDangKy)
            ->whereBetween('ngayHoc', [$today, $weekEnd])
            ->count();

        Log::info("[DashboardStats] SV: {$user->maSV} - Có mặt: $presentCount, Vắng: $absentCount, Đi muộn: $lateCount");

        return response()->json([
            'maSV' => $user->maSV,
            'hoTen' => $user->hoTen,
            'todayClasses' => $todayClasses,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'weekRemaining' => $weekRemaining,
        ]);
    }

    /**
     * 🔹 Thông tin profile sinh viên
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        Log::info("[Profile] Lấy thông tin sinh viên {$user->maSV}");

        return response()->json([
            'success' => true,
            'data' => [
                'maSV' => (string)$user->maSV,
                'hoTen' => $user->hoTen,
                'email' => $user->email,
                'lop' => optional($user->lop)->tenLop,
                'nganh' => optional($user->nganh)->tenNganh,
                'soDienThoai' => $user->soDienThoai,
                'anhDaiDien' => $user->anhDaiDien
                    ? url($user->anhDaiDien)
                    : asset('default_avatar.png'),
            ]
        ]);
    }

    /**
     * 🔹 Cập nhật thông tin sinh viên (không cho đổi email)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'hoTen' => 'nullable|string|max:255',
            'soDienThoai' => 'nullable|string|max:15',
        ]);

        $user->update($request->only('hoTen', 'soDienThoai'));

        Log::info("[UpdateProfile] SV {$user->maSV} cập nhật thông tin cá nhân.");

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $user
        ]);
    }

    /**
     * 🔹 Cập nhật ảnh đại diện sinh viên
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 🔹 Xóa ảnh cũ nếu có
        if ($user->anhDaiDien && Storage::exists('public/sinhvien/' . basename($user->anhDaiDien))) {
            Storage::delete('public/sinhvien/' . basename($user->anhDaiDien));
        }

        // 🔹 Lưu ảnh mới vào storage/app/public/sinhvien
        $file = $request->file('avatar');
        $fileName = $user->maSV . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/sinhvien', $fileName);

        // 🔹 Lưu vào DB chỉ phần relative path (public/)
        $user->anhDaiDien = 'public/sinhvien/' . $fileName;
        $user->save();

        // 🔹 Log để dễ debug
        Log::info("[UpdateAvatar] Sinh viên {$user->maSV} upload ảnh mới => {$user->anhDaiDien}");

        // 🔹 Tạo URL public hiển thị cho client
        $avatarUrl = asset('storage/sinhvien/' . $fileName);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công',
            'avatar_url' => $avatarUrl
        ]);
    }
}
