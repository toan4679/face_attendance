<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuoiHoc;
use App\Models\DiemDanh;
use App\Models\DangKyHoc;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends Controller
{
    public function listByBuoi($maBuoi)
    {
        $rows = DiemDanh::where('maBuoi', $maBuoi)
            ->with('sinhVien:maSV,hoTen,maSo,email')
            ->get();
        return response()->json($rows);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'trangThai' => 'required|in:Có mặt,Vắng,Đi muộn'
        ]);
        $dd = DiemDanh::findOrFail($id);
        $dd->update($data);
        return response()->json($dd);
    }

    public function history(Request $request)
    {
        $sv = $request->user();
        $q = DiemDanh::where('maSV',$sv->maSV)->with('buoiHoc','buoiHoc.lopHocPhan');
        if ($f=$request->get('from')) $q->whereDate('thoiGianDiemDanh','>=',$f);
        if ($t=$request->get('to'))   $q->whereDate('thoiGianDiemDanh','<=',$t);
        return response()->json($q->latest()->paginate(min((int)$request->per_page?:20,100)));
    }

    // SV check-in bằng QR: token = signed URL
    public function checkInQR(Request $request)
    {
        $data = $request->validate(['token'=>'required|string']);
        // parse token (signed url)
        $url = $data['token'];
        // Tạo Request giả để validate signature
        $pseudo = Request::create($url, 'GET');
        if (! URL::hasValidSignature($pseudo)) {
            return response()->json(['error'=>['code'=>'FORBIDDEN','message'=>'QR hết hạn hoặc không hợp lệ']], 403);
        }
        $maBuoi = $pseudo->query('maBuoi');
        $sv = $request->user();

        // Check SV có đăng ký lớp này không
        $buoi = BuoiHoc::findOrFail($maBuoi);
        $isEnrolled = DangKyHoc::where('maLopHP',$buoi->maLopHP)->where('maSV',$sv->maSV)->exists();
        if (!$isEnrolled) {
            return response()->json(['error'=>['code'=>'FORBIDDEN','message'=>'Sinh viên không thuộc lớp']], 403);
        }

        $dd = DB::transaction(function() use ($maBuoi, $sv){
            return DiemDanh::updateOrCreate(
                ['maBuoi'=>$maBuoi,'maSV'=>$sv->maSV],
                ['trangThai'=>'Có mặt','thoiGianDiemDanh'=>now(),'hinhThuc'=>'QR','xacThucKhuonMat'=>false]
            );
        });

        return response()->json($dd);
    }

    // SV check-in bằng khuôn mặt (ảnh + maBuoi)
    public function checkInFace(Request $request)
    {
        $data = $request->validate([
            'maBuoi' => 'required|exists:buoihoc,maBuoi',
            'image'  => 'required|image|max:4096'
        ]);
        $sv = $request->user();

        $buoi = BuoiHoc::findOrFail($data['maBuoi']);
        $isEnrolled = \App\Models\DangKyHoc::where('maLopHP',$buoi->maLopHP)->where('maSV',$sv->maSV)->exists();
        if (!$isEnrolled) {
            return response()->json(['error'=>['code'=>'FORBIDDEN','message'=>'Sinh viên không thuộc lớp']], 403);
        }

        // TODO: Gửi ảnh sang service Node (face-api.js) để lấy descriptor & so khớp với DB (khuonmat.duLieuNhanDien)
        // Giả lập pass:
        $match = true; $distance = 0.38;

        if (!$match) {
            return response()->json(['error'=>['code'=>'FORBIDDEN','message'=>'Xác thực khuôn mặt thất bại']], 403);
        }

        $dd = DB::transaction(function() use ($data, $sv) {
            return \App\Models\DiemDanh::updateOrCreate(
                ['maBuoi'=>$data['maBuoi'],'maSV'=>$sv->maSV],
                ['trangThai'=>'Có mặt','thoiGianDiemDanh'=>now(),'hinhThuc'=>'Khuôn mặt','xacThucKhuonMat'=>true]
            );
        });

        return response()->json($dd);
    }
}
