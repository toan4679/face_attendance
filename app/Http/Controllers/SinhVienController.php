<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;
use App\Models\BuoiHoc;
use App\Models\DiemDanh;
use App\Models\SinhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SinhVienController extends Controller
{
    /**
     * 📚 Danh sách sinh viên
     */
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

    /**
     * 📅 Dashboard - Lịch học hôm nay
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());

        Log::info("[Dashboard] Sinh viên {$user->maSV} xem lịch ngày {$date}");

        $lichHoc = DB::table('dangkyhoc')
            ->join('lophocphan', 'dangkyhoc.maLopHP', '=', 'lophocphan.maLopHP')
            ->join('monhoc', 'lophocphan.maMon', '=', 'monhoc.maMon')
            ->join('buoihoc', 'lophocphan.maLopHP', '=', 'buoihoc.maLopHP')
            ->leftJoin('giangvien', 'lophocphan.maGV', '=', 'giangvien.maGV')
            ->leftJoin('diemdanh', function ($join) use ($user) {
                $join->on('buoihoc.maBuoi', '=', 'diemdanh.maBuoi')
                    ->where('diemdanh.maSV', '=', $user->maSV);
            })
            ->where('dangkyhoc.maSV', $user->maSV) // ✅ đảm bảo chỉ sinh viên hiện tại
            ->whereDate('buoihoc.ngayHoc', $date)
            ->select(
                'monhoc.tenMon as monHoc',
                'buoihoc.phongHoc',
                'buoihoc.gioBatDau',
                'buoihoc.gioKetThuc',
                'buoihoc.ngayHoc', // ✅ hiển thị đúng ngày
                'giangvien.hoTen as tenGV',
                DB::raw("COALESCE(diemdanh.trangThai, 'Chưa điểm danh') as trangThai")
            )
            ->groupBy(
                'buoihoc.maBuoi',
                'buoihoc.ngayHoc',
                'monhoc.tenMon',
                'buoihoc.phongHoc',
                'buoihoc.gioBatDau',
                'buoihoc.gioKetThuc',
                'giangvien.hoTen',
                'diemdanh.trangThai'
            )
            ->orderBy('buoihoc.gioBatDau')
            ->get();

        return response()->json([
            'date' => $date,
            'classes' => $lichHoc,
        ]);
    }


    /**
     * 📘 Lấy lịch học chi tiết
     */
    public function lichHoc(Request $request)
    {
        $sv = $request->user();
        $lich = DangKyHoc::with('lophocphan.buoihoc')
            ->where('maSV', $sv->maSV)
            ->get();
        return response()->json($lich);
    }

    /**
     * 📊 Thống kê dashboard
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

        $lopDangKy = DangKyHoc::where('maSV', $user->maSV)
            ->pluck('maLopHP')
            ->toArray();

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

        Log::info("[Dashboard] Sinh viên {$user->maSV} - Thống kê tuần: Có mặt=$presentCount, Vắng=$absentCount, Đi muộn=$lateCount");

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
     * 👤 Lấy thông tin cá nhân sinh viên
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        Log::info("[Profile] Lấy thông tin sinh viên {$user->maSV}");

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
                    ? url('storage/' . $user->anhDaiDien)
                    : url('storage/default_avatar.png'),
            ]
        ]);
    }

    /**
     * ✏️ Cập nhật thông tin sinh viên
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'hoTen' => 'nullable|string|max:255',
            'soDienThoai' => 'nullable|string|max:15',
        ]);

        $user->update($request->only('hoTen', 'soDienThoai'));

        Log::info("[UpdateProfile] Sinh viên {$user->maSV} cập nhật thông tin cá nhân.");

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $user
        ]);
    }

    /**
     * 🖼️ Cập nhật ảnh đại diện
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 🔹 Xóa ảnh cũ nếu có
        if ($user->anhDaiDien && Storage::disk('public')->exists($user->anhDaiDien)) {
            Storage::disk('public')->delete($user->anhDaiDien);
        }

        // 🔹 Lưu ảnh mới vào đúng disk "public"
        $file = $request->file('avatar');
        $fileName = $user->maSV . '_' . time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs('sinhvien', $file, $fileName);

        // 🔹 Lưu đường dẫn tương đối trong DB
        $user->anhDaiDien = 'sinhvien/' . $fileName;
        $user->save();

        // 🔹 Trả URL công khai đúng
        $publicUrl = url('storage/sinhvien/' . $fileName);
        Log::info("[UpdateAvatar] Sinh viên {$user->maSV} upload ảnh mới => $publicUrl");

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công',
            'avatar_url' => $publicUrl,
        ]);
    }
}
