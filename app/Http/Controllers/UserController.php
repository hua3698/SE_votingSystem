<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userPage()
    {
        return view('front.user.member_center');
    }

    // 後台
    public function userList()
    {
        $adminUser = User::all();

        $response = [];
        $response = [
            'users' => $adminUser,
        ];
        return view('admin.user_list', $response);
    }

    public function deleteUser(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if ($user) {
            $user->delete();
            return response()->json(['message' => '使用者已刪除'], 200);
        } else {
            return response()->json(['message' => '找不到使用者'], 404);
        }
    }

}