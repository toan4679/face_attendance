<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    public function index(Request $request)
    {
        $q = ThongBao::query();
        if ($t = $request->get('nguoiNhanLoai')) $q->where('nguoiNhanLoai',$t);
        if ($id = $request->get('maNguoiNhan')) $q->where('maNguoiNhan',$id);
        return response()->json($q->latest()->paginate(min((int)$request->per_page?:20,100)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tieuDe' => 'required|string|max:100',
            'noiDung'=> 'required|string',
            'nguoiNhanLoai' => 'required|in:SinhVien,GiangVien',
            'maNguoiNhan' => 'required|integer'
        ]);
        $data['nguoiGui'] = 'Phòng Đào Tạo';
        $tb = ThongBao::create($data);
        return response()->json($tb, 201);
    }

    public function destroy($id)
    {
        ThongBao::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
