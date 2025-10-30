<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;

class MonHocController extends BaseCrudController
{
    protected $model = MonHoc::class;
    protected $searchable = ['tenMon','maSoMon'];
    protected $rulesCreate = [
        'maNganh'  => 'required|exists:nganh,maNganh',
        'maSoMon'  => 'required|string|max:20|unique:monhoc,maSoMon',
        'tenMon'   => 'required|string|max:100',
        'soTinChi' => 'required|integer|min:1',
        'moTa'     => 'nullable|string'
    ];
}
