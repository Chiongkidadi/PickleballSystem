<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#e0f2f7] font-sans h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 max-w-md w-full">
        <div class="text-center mb-6">
            <h2 class="text-[#2a8b9d] font-bold text-2xl tracking-widest">VERIFY CODE</h2>
            <p class="text-sm text-gray-500 mt-1">We sent a 6-digit code to <strong>{{ session('admin_login_email') }}</strong></p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.otp.verify') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">6-Digit Code</label>
                <input type="text" name="otp_code" maxlength="6" placeholder="• • • • • •" class="w-full text-center tracking-[1em] text-2xl bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-4 font-bold text-[#1c4e63] outline-none focus:border-[#2a8b9d]" required autofocus>
            </div>
            
            <button type="submit" class="w-full bg-[#2a8b9d] hover:bg-[#1c4e63] transition-colors text-white py-3 rounded-lg font-bold tracking-widest mt-4">
                VERIFY & LOGIN
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('admin.login') }}" class="text-xs text-[#2a8b9d] font-bold hover:underline">Didn't receive a code? Start over</a>
        </div>
    </div>

</body>
</html>