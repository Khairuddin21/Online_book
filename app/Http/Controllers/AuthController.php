<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Nampilin form login
     */
    public function showLoginForm()
    {
        // Redirect kalo udah login
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * Nampilin form register
     */
    public function showRegisterForm()
    {
        // Redirect kalo udah login
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.register');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Siapin data login
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Opsi ingat saya
        $remember = $request->has('remember');

        // Coba login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Arahkan sesuai role user
            return $this->redirectBasedOnRole();
        }

        // Login gagal
        return redirect()->back()
            ->withErrors(['email' => 'Email atau kata sandi salah.'])
            ->withInput($request->only('email'));
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:3|max:150',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Bikin user baru
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user', // Role default-nya user
                'alamat' => null,
                'no_hp' => null,
            ]);

            // Auto login abis daftar
            Auth::login($user);

            // Bikin session baru
            $request->session()->regenerate();

            // Arahkan ke halaman user dengan pesan sukses
            return redirect()->route('user.home')
                ->with('success', 'Registrasi berhasil! Selamat datang di Toko Buku Online.');

        } catch (\Exception $e) {
            // Catat error buat debugging
            Log::error('Registration error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Arahkan user sesuai role-nya
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.home');
    }

    // ===== LOGIN PAKE GOOGLE =====
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'nama'     => $socialUser->getName(),
                'email'    => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'role'     => 'user',
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole();
    }

}
