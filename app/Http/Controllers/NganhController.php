<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\Nganh;

class NganhController extends BaseCrudController
{
    protected $model = Nganh::class;
    protected $searchable = ['tenNganh', 'maSo'];
    protected $rulesCreate = [
        'tenNganh' => 'required|string|max:100',
        'maSo' => ['required', 'string', Rule::unique('nganh', 'maSo')->ignore($id, 'maNganh')],
    ];
}
