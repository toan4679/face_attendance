<?php

namespace App\Http\Controllers;

use App\Models\Nganh;

class NganhController extends BaseCrudController
{
    protected $model = Nganh::class;
    protected $searchable = ['tenNganh','maSo'];
    protected $rulesCreate = [
        'maBoMon'  => 'required|exists:bomon,maBoMon',
        'tenNganh' => 'required|string|max:100',
        'maSo'     => 'required|string|max:20|unique:nganh,maSo',
    ];
}
