<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;
use App\Models\BuoiHoc;
use App\Helpers\RoleHelper;
use App\Models\DiemDanh;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SinhVienController extends Controller
{

    public function index()
    {
        try {
            $sinhViens = SinhVien::with('lop', 'nganh')->get();

            return response()->json([
                'success' => true,
                'total' => $sinhViens->count(),
                'data' => $sinhViens,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách sinh viên: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sinh viên.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function dashboard(Request $request)
    {
        $user = $request->user(); // Sinh viên đang đăng nhập
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

        return response()->json([
            'today' => $today,
            'classes' => $lichHoc
        ]);
    }



    public function lichHoc(Request $request)
    {
        $sv = $request->user();
        $lich = DangKyHoc::with('lophocphan.buoihoc')
            ->where('maSV', $sv->maSV)
            ->get();
        return response()->json($lich);
    }

    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        // ✅ Chỉ sinh viên mới được phép truy cập
        if (!($user instanceof SinhVien)) {
            return response()->json(['error' => 'Chỉ sinh viên mới được truy cập API này.'], 403);
        }

        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        // 🔹 Danh sách lớp học phần mà sinh viên đã đăng ký
        $lopDangKy = DangKyHoc::where('maSV', $user->maSV)
            ->pluck('maLopHP')
            ->toArray();

        // 🔹 Tính tổng số buổi học hôm nay
        $todayClasses = BuoiHoc::whereIn('maLopHP', $lopDangKy)
            ->whereDate('ngayHoc', $today)
            ->count();

        // 🔹 Lấy danh sách điểm danh trong tuần
        $attendanceRecords = DiemDanh::where('maSV', $user->maSV)
            ->whereBetween('ngayDiemDanh', [$weekStart, $weekEnd])
            ->get();

        // 🔹 Đếm số buổi có mặt, vắng, đi muộn
        $presentCount = $attendanceRecords->where('trangThai', 'Có mặt')->count();
        $absentCount = $attendanceRecords->where('trangThai', 'Vắng')->count();
        $lateCount = $attendanceRecords->where('trangThai', 'Đi muộn')->count();

        // 🔹 Tính tổng số buổi còn lại trong tuần
        $weekRemaining = BuoiHoc::whereIn('maLopHP', $lopDangKy)
            ->whereBetween('ngayHoc', [$today, $weekEnd])
            ->count();

        // ✅ Trả về kết quả JSON
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

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'maSV' => $user->maSV,
                'hoTen' => $user->hoTen,
                'email' => $user->email,
                'lop' => optional($user->lop)->tenLop,
                'nganh' => optional($user->nganh)->tenNganh,
                'soDienThoai' => $user->soDienThoai,
                'anhDaiDien' => $user->anhDaiDien
                    ? asset('storage/sinhvien/' . $user->anhDaiDien)
                    : asset('default_avatar.png'),
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'hoTen' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'soDienThoai' => 'nullable|string|max:15'
        ]);

        $user->update($request->only('hoTen', 'email', 'soDienThoai'));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $user
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Xóa ảnh cũ nếu có
        if ($user->anhDaiDien && Storage::exists('public/sinhvien/' . $user->anhDaiDien)) {
            Storage::delete('public/sinhvien/' . $user->anhDaiDien);
        }

        $file = $request->file('avatar');
        $fileName = $user->maSV . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/sinhvien', $fileName);

        $user->anhDaiDien = $fileName;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công',
            'avatar_url' => asset('storage/sinhvien/' . $fileName)
        ]);
    }
}
