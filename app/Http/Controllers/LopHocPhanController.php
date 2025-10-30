<?php

namespace App\Http\Controllers;

use App\Models\LopHocPhan;
use Illuminate\Http\Request;

class LopHocPhanController extends BaseCrudController
{
    protected $model = LopHocPhan::class;
    protected $searchable = ['maSoLopHP','hocKy','namHoc'];
    protected $rulesCreate = [
        'maMon'      => 'required|exists:monhoc,maMon',
        'maGV'       => 'required|exists:giangvien,maGV',
        'maSoLopHP'  => 'required|string|max:50|unique:lophocphan,maSoLopHP',
        'hocKy'      => 'required|string|max:20',
        'namHoc'     => 'required|string|max:20',
        'thongTinLichHoc' => 'nullable|string'
    ];

    public function byGiangVien(Request $request)
    {
        $gv = $request->user();
        return response()->json(
            LopHocPhan::where('maGV', $gv->maGV)->paginate(min((int)$request->per_page?:20, 100))
        );
    }
}
