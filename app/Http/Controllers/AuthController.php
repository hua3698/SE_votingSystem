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
        if($request->session()->get('administrator')) {
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

            if (Auth::guard('admin')->attempt($credentials)) {
                // 登入成功
                session()->put('administrator', $credentials['email']);
                return redirect()->intended('/outstand');
            }

            // 登入失敗
            return back()->withErrors([
                'email' => '帳號或密碼錯誤。',
            ]);
        }
        catch (\Exception $e) 
        {
            echo $e->getMessage();
            return view('hello');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->forget('administrator');
        return redirect()->route('login.form');
    }

    public function showUserLogin(Request $request)
    {
        if($request->session()->get('frontuser')) {
            return redirect()->intended('/user'); 
        }
        return view('front.user.login');
    }

    public function userLogin(Request $request)
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

            if (Auth::guard('user')->attempt($credentials)) {
                // 登入成功
                session()->put('frontuser', $credentials['email']);
                return redirect()->intended('/user');
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

    public function userLogout()
    {
        Auth::guard('web')->logout();
        session()->forget('frontuser');
        return redirect()->route('user.login.form');
    }

    public function userRegister()
    {
        // return view('');
    }
}
