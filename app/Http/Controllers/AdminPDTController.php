<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhongDaoTao;
use Illuminate\Support\Facades\Hash;

class AdminPDTController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            PhongDaoTao::when($request->search, fn($q)=>$q->where('hoTen','like','%'.$request->search.'%')
                ->orWhere('email','like','%'.$request->search.'%'))
            ->paginate(min((int)$request->per_page ?: 20, 100))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:phongdaotao,email',
            'matKhau'=> 'required|min:6',
            'soDienThoai'=>'nullable|string|max:20',
        ]);
        $data['matKhau'] = Hash::make($data['matKhau']);
        $data['maAdmin'] = $request->user()->maAdmin;
        $pdt = PhongDaoTao::create($data);
        return response()->json($pdt, 201);
    }

    public function update(Request $request, $id)
    {
        $pdt = PhongDaoTao::findOrFail($id);
        $data = $request->validate([
            'hoTen' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:phongdaotao,email,'.$pdt->maPDT.',maPDT',
            'matKhau'=> 'nullable|min:6',
            'soDienThoai'=>'nullable|string|max:20',
        ]);
        if (!empty($data['matKhau'])) $data['matKhau'] = Hash::make($data['matKhau']);
        $pdt->update($data);
        return response()->json($pdt);
    }

    public function destroy($id)
    {
        PhongDaoTao::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'loai' => 'required|in:pdt,giangvien,sinhvien',
            'id'   => 'required|integer',
            'new_password' => 'required|min:6'
        ]);
        $map = [
            'pdt'       => \App\Models\PhongDaoTao::class,
            'giangvien' => \App\Models\GiangVien::class,
            'sinhvien'  => \App\Models\SinhVien::class,
        ];
        $m = $map[$data['loai']];
        $u = $m::findOrFail($data['id']);
        $u->matKhau = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        $u->save();
        return response()->json(null, 204);
    }
}
