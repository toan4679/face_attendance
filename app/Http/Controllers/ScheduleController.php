<?php

namespace App\Http\Controllers;

use App\Models\BuoiHocKeHoach;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return response()->json(BuoiHocKeHoach::with(['lophocphan', 'giangvien'])->get());
    }

    public function show($id)
    {
        $buoi = BuoiHocKeHoach::with(['lophocphan', 'giangvien', 'diemdanh'])->findOrFail($id);
        return response()->json($buoi);
    }
}
