<?php

namespace App\Http\Controllers;
use App\Models\User;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $adminUser = new Admin();
            $adminUser->name = $validatedData['name'];
            $adminUser->email = $validatedData['email'];
            $adminUser->password = Hash::make($validatedData['password']);
            $adminUser->save();

            return response()->json(['message' => '新增成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '新增失敗，請重試', 'error' => $e->getMessage()], 500);
        }
    }

    public function userPage()
    {
        return view('front.user.info');
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

}