<?php

namespace App\Http\Controllers;

use App\Models\KhuonMat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KhuonMatController extends Controller
{
    // SV upload
    public function store(Request $request)
    {
        $sv = $request->user();
        $request->validate([
            'image' => 'required|image|max:4096',
            // Nếu đã tích hợp service encode: 'descriptor' => 'sometimes|array'
        ]);

        $path = $request->file('image')->store('faces', 'public');

        $km = KhuonMat::updateOrCreate(
            ['maSV' => $sv->maSV],
            [
                'duongDanAnh' => 'storage/'.$path,
                'duLieuNhanDien' => null, // chờ service face-api.js xử lý/ hoặc set descriptor từ request nếu có
            ]
        );

        // gợi ý: bắn job/queue sang Node service để encode descriptor rồi update lại db

        return response()->json(['maKhuonMat'=>$km->maKhuonMat,'trangThai'=>'pending'], 201);
    }

    public function showMine(Request $request)
    {
        $sv = $request->user();
        $km = KhuonMat::where('maSV',$sv->maSV)->first();
        return response()->json($km);
    }

    public function destroyMine(Request $request, $id)
    {
        $sv = $request->user();
        $km = KhuonMat::where('maSV',$sv->maSV)->where('maKhuonMat',$id)->firstOrFail();
        if ($km->duongDanAnh && str_starts_with($km->duongDanAnh, 'storage/')) {
            @Storage::disk('public')->delete(str_replace('storage/', '', $km->duongDanAnh));
        }
        $km->delete();
        return response()->json(null, 204);
    }

    // PĐT duyệt
    public function pending(Request $request)
    {
        // Bạn có thể thêm cột trạng thái nếu muốn; tạm lọc theo descriptor null
        return response()->json(
            KhuonMat::whereNull('duLieuNhanDien')->paginate(min((int)$request->per_page?:20,100))
        );
    }

    public function approve($id)
    {
        // Thực tế: sau khi Node service trả về descriptor, coi như đã approved
        // Nếu muốn cờ trạng thái -> thêm cột "trangThai"
        return response()->json(null, 204);
    }

    public function reject($id)
    {
        // Tùy chính sách: xóa hoặc đặt trạng thái rejected
        return response()->json(null, 204);
    }
}
