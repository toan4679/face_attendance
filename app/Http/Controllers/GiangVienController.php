<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use Illuminate\Http\Request;
use App\Models\LopHocPhan;
use Illuminate\Support\Facades\Log;
class GiangVienController extends Controller
{
    public function index()
    {
        $data = GiangVien::all();
        return response()->json(['data' => $data]);
    }
    public function getAll()
    {
        $giangviens = GiangVien::with('khoa', 'nganh')->get();
        return response()->json([
            'status' => true,
            'data' => $giangviens
        ]);
    }

    public function getDetail($id)
    {
         try {
            $giangVien = GiangVien::with(['khoa', 'nganh', 'lophocphan'])->find($id);

            if (!$giangVien) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy giảng viên'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $giangVien,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy chi tiết giảng viên: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết giảng viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:giangvien,email',
            'matKhau' => 'required|string|min:6',
            'soDienThoai' => 'nullable|string|max:20',
            'hocVi' => 'nullable|string|max:50',
        ]);

        $validated['matKhau'] = bcrypt($validated['matKhau']);
        $giangVien = GiangVien::create($validated);

        return response()->json(['message' => 'Thêm giảng viên thành công', 'data' => $giangVien]);
    }

    public function show($id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }
        return response()->json(['data' => $gv]);
    }

    public function update(Request $request, $id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }

        $gv->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $gv]);
    }

    public function destroy($id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }

        $gv->delete();
        return response()->json(['message' => 'Xóa thành công']);
    }
    public function getLopHocPhan($id)
    {
        $giangvien = GiangVien::find($id);
        if (!$giangvien) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy giảng viên'], 404);
        }

        $lopHocPhans = LopHocPhan::with([
            'monhoc',
            'dangkyhoc.sinhvien',
            'sinhviens' => function ($query) {
                $query->select('sinhvien.*', 'dangkyhoc.ma_lophocphan')
                    ->join('dangkyhoc', 'sinhvien.ma_sv', '=', 'dangkyhoc.ma_sv');
            }
        ])->where('ma_gv', $giangvien->ma_gv)->get();

        return response()->json([
            'status' => true,
            'total' => $lopHocPhans->count(),
            'data' => $lopHocPhans
        ]);
    }
}
