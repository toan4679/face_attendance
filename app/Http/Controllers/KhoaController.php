<?php

namespace App\Http\Controllers;

use App\Models\Khoa;

class KhoaController extends BaseCrudController
{
    protected $model = Khoa::class;
    protected $searchable = ['tenKhoa','moTa'];
    protected $rulesCreate = [
        'tenKhoa' => 'required|string|max:100',
        'moTa'    => 'nullable|string'
    ];
}
