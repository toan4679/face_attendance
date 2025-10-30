<?php

namespace App\Http\Controllers;

use App\Models\LopHocPhan;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role;
        $userId = $request->user()->user_id;

        if ($role === 'GV') {
            $classes = LopHocPhan::where('giangvien_chinh_id', $userId)
                ->with(['hocphan', 'buoihoc'])
                ->get();
        } elseif ($role === 'SV') {
            $classes = $request->user()
                ->enrollment()
                ->with('hocphan')
                ->get();
        } else {
            $classes = LopHocPhan::with('hocphan')->get();
        }

        return response()->json($classes);
    }

    public function show($id)
    {
        $class = LopHocPhan::with(['hocphan', 'buoihoc', 'sinhvien'])->findOrFail($id);
        return response()->json($class);
    }
}
