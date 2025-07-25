<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        Auth::logout();
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $latestRent = $user->latestRent(); 

            if($latestRent == null){
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Tài khoản chưa được kích hoạt. Vui lòng liên hệ quản trị viên.',
                ])->withInput();
            }

            if ($latestRent && now()->gt($latestRent->end_date)) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Tài khoản đã hết hạn thuê. Vui lòng liên hệ quản trị viên.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended('/dice');
        }

        return back()->withErrors([
            'username' => 'Tài khoản hoặc mật khẩu không đúng.',
        ])->withInput();
    }

    // Lấy thông tin user đã login
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Logout (xóa token)
    public function logout(Request $request)
    {
        Auth::logout(); // đăng xuất

        $request->session()->invalidate(); // xoá session
        $request->session()->regenerateToken(); // tránh tấn công CSRF

        return redirect('/login');
    }
}
