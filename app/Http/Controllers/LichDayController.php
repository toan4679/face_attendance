<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuoiHoc;
use App\Models\LopHocPhan;
use App\Models\DangKyHoc;

class LichDayController extends Controller
{
    public function index(Request $request)
    {
        $gv = $request->user();
        $from = $request->get('from');
        $to   = $request->get('to');

        $q = BuoiHoc::where('maGV', $gv->maGV);
        if ($from) $q->whereDate('ngayHoc', '>=', $from);
        if ($to)   $q->whereDate('ngayHoc', '<=', $to);

        return response()->json($q->orderBy('ngayHoc')->get());
    }
}

class LichHocController extends Controller
{
    public function index(Request $request)
    {
        $sv = $request->user();
        $lopIds = DangKyHoc::where('maSV', $sv->maSV)->pluck('maLopHP');

        $q = BuoiHoc::whereIn('maLopHP', $lopIds);
        if ($f = $request->get('from')) $q->whereDate('ngayHoc','>=',$f);
        if ($t = $request->get('to'))   $q->whereDate('ngayHoc','<=',$t);

        return response()->json($q->orderBy('ngayHoc')->get());
    }
}
