<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function loginPage(){
        return view('login');
    }


    public function login(Request $request) {
        $credential = $request->only('email', 'password');

        if (Auth::attempt($credential)) {
            $request->session()->regenerate();
            return redirect()->route('products.index');
        }
        return back()->with('error', 'Email atau Password salah');
    }


    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}
