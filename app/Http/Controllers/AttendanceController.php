<?php

namespace App\Http\Controllers;

use App\Models\DiemDanhLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'buoihoc_id' => 'required|integer',
            'sinhvien_id' => 'required|integer',
            'confidence' => 'nullable|numeric',
            'image_capture_url' => 'nullable|string'
        ]);

        $log = DiemDanhLog::updateOrCreate(
            ['buoihoc_id' => $request->buoihoc_id, 'sinhvien_id' => $request->sinhvien_id],
            [
                'thoi_gian_diem_danh' => Carbon::now(),
                'trang_thai' => 'Co mat',
                'confidence' => $request->confidence ?? 1.0,
                'image_capture_url' => $request->image_capture_url
            ]
        );

        return response()->json(['message' => 'Điểm danh thành công', 'data' => $log]);
    }

    public function history($student_id)
    {
        $logs = DiemDanhLog::where('sinhvien_id', $student_id)
            ->with(['buoihoc.lophocphan'])
            ->orderByDesc('thoi_gian_diem_danh')
            ->get();
        return response()->json($logs);
    }
}
