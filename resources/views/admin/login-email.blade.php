<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#e0f2f7] font-sans h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 max-w-md w-full">
        <div class="text-center mb-6">
            <h2 class="text-[#2a8b9d] font-bold text-2xl tracking-widest">ADMIN PANEL</h2>
            <p class="text-sm text-gray-500 mt-1">Enter your credentials to receive a login code</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login.send') }}" method="POST" class="space-y-4">
            @csrf  ```
            
            <div>
                <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Admin Email</label>
                <input type="email" name="email" placeholder="admin@example.com" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required autofocus>
            </div>

            <div>
                <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
            </div>
            
            <button type="submit" class="w-full bg-[#2a8b9d] hover:bg-[#1c4e63] transition-colors text-white py-3 rounded-lg font-bold tracking-widest mt-6">
                LOGIN & SEND CODE
            </button>
        </form>
    </div>

</body>
</html>