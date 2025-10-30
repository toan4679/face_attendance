<?php

namespace App\Http\Controllers;

use App\Models\BoMon;

class BoMonController extends BaseCrudController
{
    protected $model = BoMon::class;
    protected $searchable = ['tenBoMon'];
    protected $rulesCreate = [
        'maKhoa'   => 'required|exists:khoa,maKhoa',
        'tenBoMon' => 'required|string|max:100',
    ];
}
