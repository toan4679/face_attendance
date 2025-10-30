<?php

namespace App\Http\Controllers;

use App\Models\Face;
use App\Models\User;
use Illuminate\Http\Request;

class FaceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'embedding' => 'required|array',
            'image_url' => 'nullable|string'
        ]);

        $face = Face::updateOrCreate(
            ['user_id' => $request->user_id],
            ['embedding' => json_encode($request->embedding), 'image_url' => $request->image_url]
        );

        return response()->json(['message' => 'Lưu khuôn mặt thành công', 'data' => $face]);
    }

    public function verify(Request $request)
    {
        $request->validate(['embedding' => 'required|array']);
        $faces = Face::all();

        $input = collect($request->embedding);
        $minDist = INF; $matchUser = null;

        foreach ($faces as $face) {
            $dist = sqrt(collect(json_decode($face->embedding))
                ->zip($input)
                ->map(fn($pair) => pow($pair[0] - $pair[1], 2))
                ->sum());
            if ($dist < $minDist) {
                $minDist = $dist;
                $matchUser = $face->user_id;
            }
        }

        if ($minDist < 0.6) {
            $user = User::find($matchUser);
            return response()->json(['match' => true, 'user' => $user, 'distance' => $minDist]);
        }
        return response()->json(['match' => false, 'distance' => $minDist]);
    }
}
