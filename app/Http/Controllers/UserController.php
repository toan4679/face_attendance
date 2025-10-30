<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function index()
    {
        return response()->json(User::with(['bomon', 'lophanhchinh'])->get());
    }

    public function show($id)
    {
        $user = User::with(['bomon', 'lophanhchinh'])->findOrFail($id);
        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'Xóa người dùng thành công']);
    }
}
