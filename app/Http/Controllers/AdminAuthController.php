<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdminLoginOtpMail;
use Carbon\Carbon;

class AdminAuthController extends Controller
{
    // 1. Show the email & password input form
    public function showEmailForm()
    {
        // If they are already logged in, send them straight to the dashboard!
        // This prevents them from accidentally ending up on the public kiosk.
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login-email');
    }

    // 2. Verify Credentials, Generate, and Send the OTP
    public function sendOtp(Request $request)
    {
        // Require both email and password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if the email and password match the database BEFORE doing anything else
        if (!Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            return back()->with('error', 'Invalid email or password.');
        }

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Save to database with 10-minute expiration
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send Email using your custom Mailable class
        Mail::to($user->email)->send(new AdminLoginOtpMail($otp));

        // Store email in session to carry over to the next step
        session(['admin_login_email' => $user->email]);

        return redirect()->route('admin.otp.form')->with('success', 'Credentials verified! A login code has been sent to your email.');
    }

    // 3. Show the OTP input form
    public function showOtpForm()
    {
        if (!session('admin_login_email')) {
            return redirect()->route('admin.login');
        }
        return view('admin.login-otp');
    }

    // 4. Verify OTP and login
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp_code' => 'required|numeric']);
        $email = session('admin_login_email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Try again.');
        }

        // Check if OTP matches and is not expired
        if ($user->otp_code == $request->otp_code && Carbon::now()->lessThanOrEqualTo($user->otp_expires_at)) {
            
            // Clear OTP data so it can't be reused
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            // Log the user in
            Auth::login($user);
            session()->forget('admin_login_email');

            // 🚀 THE FIX: This explicitly forces the user to the Admin Dashboard!
            return redirect()->route('admin.dashboard')->with('success', 'Welcome to the Admin Dashboard'); 
        }

        return back()->with('error', 'Invalid or expired code. Please try again.');
    }

    // 5. Log the admin out
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session and regenerate the security token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been securely logged out.');
    }
}