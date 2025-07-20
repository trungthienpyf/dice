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
    // Đăng nhập API
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kiểm tra ngày hết hạn
            if ($user->expired_at && Carbon::parse($user->expired_at)->isPast()) {
                Auth::logout();

                return back()->withErrors([
                    'username' => 'Tài khoản đã hết hạn.',
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
