<?php

namespace App\Http\Controllers;

use App\Models\DiemDanhLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary()
    {
        $tong = DiemDanhLog::count();
        $vang = DiemDanhLog::where('trang_thai', 'Vang')->count();
        $muon = DiemDanhLog::where('trang_thai', 'Muon')->count();

        return response()->json([
            'tong_luot_diem_danh' => $tong,
            'so_vang' => $vang,
            'so_muon' => $muon,
            'ti_le_tham_du' => round(($tong - $vang - $muon) / max($tong, 1) * 100, 2)
        ]);
    }
}
