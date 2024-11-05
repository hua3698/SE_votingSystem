<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 顯示登入畫面
    public function showLogin(Request $request)
    {
        if($request->session()->get('email')) {
            return redirect()->intended('/outstand'); 
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        try
        {
            if(!isset($request->email) || !isset($request->password)) {
                return view('hello');
            }

            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if (Auth::attempt($credentials)) {
                // 登入成功
                session()->put('email', $credentials['email']);
                return redirect()->intended('/outstand');
            }

            // 登入失敗
            return back()->withErrors([
                'email' => '帳號或密碼錯誤。',
            ]);
        }
        catch (\Exception $e) 
        {
            return view('hello');
        }
    }

    public function logout()
    {
        session()->put('email', '');
        return redirect()->route('login.form');
    }
}
