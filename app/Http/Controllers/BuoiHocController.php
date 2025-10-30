<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;

class BuoiHocController extends BaseCrudController
{
    protected $model = BuoiHoc::class;
    protected $searchable = ['phongHoc'];
    protected $rulesCreate = [
        'maLopHP' => 'required|exists:lophocphan,maLopHP',
        'maGV'    => 'required|exists:giangvien,maGV',
        'ngayHoc' => 'required|date',
        'gioBatDau' => 'required|date_format:H:i',
        'gioKetThuc'=> 'required|date_format:H:i|after:gioBatDau',
        'phongHoc'  => 'nullable|string|max:50',
        'maQR'      => 'nullable|string|max:255'
    ];

    public function store(Request $request)
    {
        // Cho phép batch qua field items
        if ($request->has('items') && is_array($request->items)) {
            $created = [];
            foreach ($request->items as $item) {
                $created[] = BuoiHoc::create(Validator::validate($item, $this->rulesCreate));
            }
            return response()->json($created, 201);
        }
        return parent::store($request);
    }
}
