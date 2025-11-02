<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;
use Illuminate\Http\Request;

class MonHocController extends Controller
{
    public function index()
    {
        return MonHoc::with('nganh')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maNganh' => 'required|exists:nganh,maNganh',
            'maSoMon' => 'required|string|max:20|unique:monhoc,maSoMon',
            'tenMon' => 'required|string|max:100',
            'soTinChi' => 'required|integer|min:1',
            'moTa' => 'nullable|string',
        ]);

        $monHoc = MonHoc::create($data);
        return response()->json($monHoc, 201);
    }

    public function show($id)
    {
        return MonHoc::with('nganh')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $monHoc = MonHoc::findOrFail($id);
        $monHoc->update($request->all());
        return response()->json($monHoc);
    }

    public function destroy($id)
    {
        MonHoc::destroy($id);
        return response()->json(['message' => 'Xóa môn học thành công']);
    }
}
