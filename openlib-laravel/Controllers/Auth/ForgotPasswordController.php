<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Member\Member;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Menerima form (email), lalu mengirim link reset password (tanpa password_resets).
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Mohon masukkan alamat email.',
            'email.email' => 'Alamat email tidak valid.',
        ]);

        $member = Member::where('master_data_email', $request->email)->first();
        if (!$member) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        $token = Str::random(64);
        $expire = now()->addHour();
        $member->reset_token = $token;
        $member->reset_token_expire = $expire;
        $member->save();

        $resetLink = url('auth/reset-password?token=' . $token);

        try {
            Mail::to($member->master_data_email)->send(
                new ResetPasswordMail($member->master_data_fullname, $resetLink)
            );

            return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda.');

        } catch (\Exception $e) {
            Log::error('Error sending reset password email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email reset password.']);
        }
    }

    public function showResetForm(Request $request)
    {
        if (session('status')) {
            return view('auth.reset-password', [
                'token' => null,
                'email' => null
            ]);
        }

        $token = $request->query('token');
        if (!$token) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Token reset password tidak ditemukan.']);
        }

        $member = Member::where('reset_token', $token)->first();
        if (!$member) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Token reset password tidak valid.']);
        }

        if (!$member->reset_token_expire || $member->reset_token_expire < now()) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Token reset password sudah kadaluarsa.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $member->master_data_email
        ]);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'min:8', 'confirmed', 'regex:/^[A-Za-z0-9]+$/'],
        ], [
            'password.required' => 'Mohon masukkan password baru.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password hanya boleh huruf dan angka.',
        ]);

        $member = Member::where('reset_token', $request->token)->first();
        if (!$member) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Token reset password tidak valid.']);
        }

        if (!$member->reset_token_expire || $member->reset_token_expire < now()) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Token reset password sudah kadaluarsa.']);
        }

        $member->master_data_password = Hash::make($request->password);
        $member->reset_token = null;
        $member->reset_token_expire = null;
        $member->save();

        return back()->with('status', 'Password berhasil diperbarui! Anda akan diarahkan ke halaman login...');
    }


}
