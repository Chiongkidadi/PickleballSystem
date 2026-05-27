<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Reservation Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom animation for the pulsing "Tap" button */
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        .animate-pulse-soft { animation: pulse-soft 2s infinite ease-in-out; }
        
        /* Custom scrollbar to look clean on the right panel */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #b2d9e2; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #2a8b9d; }
    </style>
</head>
<body class="bg-[#e0f2f7] font-sans h-screen flex flex-col overflow-hidden">

    @if(!isset($isAdmin) || !$isAdmin)
    {{-- 1. WELCOME BOARD OVERLAY --}}
    <div id="welcome-board" class="fixed inset-0 z-[100] bg-[#165166] flex flex-col items-center justify-center text-white cursor-pointer transition-all duration-700 ease-in-out">
        
        <div class="flex flex-row items-center gap-8 mb-16">
            <div class="relative w-48 h-48 flex items-center justify-center shrink-0">
                <div class="absolute inset-0 bg-[#2a8b9d] rounded-full shadow-2xl border-4 border-[#86c5d6] opacity-40 animate-pulse"></div>
                <img src="{{ asset('image/pickleball_logo.png') }}" alt="Pickleball Logo" class="z-10 w-40 h-40 object-contain rounded-full bg-white shadow-2xl border-4 border-[#165166]">
            </div>
            
            <div class="text-left">
                <h1 class="text-5xl md:text-7xl font-black mb-2 tracking-widest leading-none">PICKLEBALL</h1>
                <h2 class="text-2xl md:text-3xl font-bold tracking-[0.3em] text-[#86c5d6] uppercase">Reservation Kiosk</h2>
            </div>
        </div>
        
        <div class="animate-pulse-soft flex flex-col items-center">
            <div class="relative group">
                <div class="absolute -inset-1 bg-[#86c5d6] rounded-full blur opacity-20"></div>
                <p class="relative text-xl md:text-2xl font-bold tracking-widest uppercase bg-[#2a8b9d] px-10 py-4 rounded-full shadow-2xl border-2 border-[#86c5d6] flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" />
                    </svg>
                    Tap Screen to Begin
                </p>
            </div>
        </div>
        <div class="absolute bottom-8 text-center opacity-40">
            <p class="text-xs uppercase tracking-[0.4em]">Island Central Mactan • MEPZ Ecozone</p>
        </div>
    </div>

    <div id="guidelines-modal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-[#165166]/90 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden border-4 border-[#2a8b9d] animate-[pulse-soft_0.3s_ease-out_1]">
            
            <div class="bg-[#165166] p-6 text-center">
                <h2 class="text-3xl font-bold text-white tracking-widest uppercase">Court Guidelines</h2>
                <p class="text-[#86c5d6] text-sm mt-1 uppercase font-bold tracking-wider">Please read and agree to continue</p>
            </div>

            <div class="p-8 overflow-y-auto text-[#1c4e63] space-y-4 text-base leading-relaxed custom-scrollbar bg-[#f0f9fb]">
                <ul class="list-decimal pl-5 space-y-3 font-medium">
                    <li><strong>First Come, First Served:</strong> Reservations are prioritized by booking order. Rescheduling is subject to court availability.</li>
                    <li><strong>Payment Policy:</strong> Payments must match the booked date and time only. Bulk payments for multiple dates are not allowed and may void the booking.</li>
                    <li><strong>No Refunds:</strong> Strictly no refunds. Rescheduling may be accommodated depending on availability.</li>
                    <li><strong>Recreational Use:</strong> The court is for recreational use only. Current court dimensions are non-standard and not intended for tournaments.</li>
                    <li><strong>Rates:</strong> Court rental rate is <strong>PHP 200/hour (9AM–10PM)</strong>. Payments may be made online or on-site.</li>
                    <li><strong>Equipment:</strong> Rentals may be claimed at Island Skybar & Lounge and paid together with the booking or via cash at Island Central Mactan Cinema.</li>
                    <li><strong>Conduct:</strong> Players are encouraged to wear proper sports attire and observe cleanliness and respectful conduct at all times.</li>
                    <li><strong>Liability:</strong> Management is not liable for injuries, lost belongings, or damages incurred during court use.</li>
                    <li><strong>Rights:</strong> Management reserves the right to cancel or reschedule bookings due to maintenance, safety, or policy violations.</li>
                </ul>
            </div>

            <div class="p-6 bg-white border-t border-[#b2d9e2] flex flex-col gap-3">
                <button id="agree-btn" class="w-full py-5 bg-[#2a8b9d] hover:bg-[#165166] text-white font-bold rounded-xl transition-colors shadow-md uppercase tracking-widest text-xl flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    I Agree & Proceed
                </button>
            </div>
        </div>
    </div>
    @endif

    <header class="bg-[#2a8b9d] text-white py-4 px-10 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-6">
            @if(isset($isAdmin) && $isAdmin)
            <a href="{{ route('admin.dashboard') }}" class="bg-[#1c4e63] text-white px-5 py-2.5 rounded-lg shadow-sm font-bold flex items-center gap-2 hover:bg-[#11303d] transition-colors duration-300" style="text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
            @endif
            
            <div class="flex items-center gap-4">
                <img src="{{ asset('image/pickleball_logo.png') }}" alt="Logo" class="w-14 h-14 object-contain rounded-full border-2 border-white bg-white shadow-sm shrink-0">
                <div>
                    <h1 class="text-2xl font-bold tracking-wider text-white leading-tight">PICKLEBALL RESERVATION</h1>
                    <p class="text-[10px] uppercase opacity-90 font-medium">Island Central Mactan - MEPZ Ecozone, ML Quezon Hwy, Lapu-Lapu City</p>
                </div>
            </div>
        </div>

        <div class="text-right">
            <p class="text-[10px] uppercase opacity-80" id="current-date"></p>
            <p class="text-3xl font-bold font-mono tracking-widest" id="clock">00:00:00 AM</p>
        </div>
    </header>

    <main class="flex-1 p-8 grid grid-cols-3 gap-6 max-w-7xl mx-auto w-full min-h-0">
        
        <div class="col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col p-6 min-h-0">
            <h2 class="text-[#2a8b9d] font-bold text-sm tracking-widest mb-4 flex items-center shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                SELECT A DATE TO RESERVE
            </h2>
            <div class="flex-1 flex flex-col mt-4 min-h-0">
                <div class="flex justify-between items-center mb-6 px-2 shrink-0">
                    <div class="flex gap-2">
                        <button id="prev-month" class="bg-[#2a8b9d] hover:bg-[#1c4e63] text-white px-3 py-1 rounded-md transition-colors">&lt;</button>
                        <button id="next-month" class="bg-[#2a8b9d] hover:bg-[#1c4e63] text-white px-3 py-1 rounded-md transition-colors">&gt;</button>
                        <button id="today-btn" class="bg-[#b2d9e2] text-[#1c4e63] hover:bg-[#8ebcc9] px-4 py-1 rounded-md font-bold text-sm uppercase transition-colors">today</button>
                    </div>
                    <h3 id="month-year-display" class="font-bold text-[#1c4e63] text-2xl uppercase tracking-widest"></h3>
                </div>
                <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-t-lg overflow-hidden shrink-0">
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Sun</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Mon</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Tue</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Wed</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Thu</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Fri</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Sat</div>
                </div>
                <div id="calendar-days" class="grid grid-cols-7 gap-px bg-gray-200 border-x border-b border-gray-200 rounded-b-lg overflow-y-auto custom-scrollbar flex-1"></div>
            </div>
        </div>

        <div class="relative min-h-0">
            <div class="absolute inset-0 overflow-y-auto pr-3 pb-6 flex flex-col gap-6 custom-scrollbar">
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden shrink-0">
                    <div class="bg-[#2a8b9d] px-4 py-3 flex justify-between items-center text-white">
                        <h3 class="font-bold text-sm tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            BOOKED SLOTS
                        </h3>
                        <span class="text-xs">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                    </div>
                    <div class="p-6 text-center text-sm text-[#2a8b9d] font-medium bg-[#f0f9fb]">
                        @if($bookedSlots->isEmpty())
                            <p class="flex items-center justify-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span>All slots are open!</p>
                        @else
                            @foreach($bookedSlots as $slot)
                                <p class="mb-1">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden shrink-0">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-[#1c4e63] tracking-widest text-lg">
                            {{ session('reschedule_id') ? 'RESCHEDULE BOOKING' : 'NEW BOOKING' }}
                        </h3>
                    </div>
                    
                    <form action="{{ route('reserve.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="bookingForm">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ session('reschedule_id') ?? '' }}">
                        @if(isset($isAdmin) && $isAdmin)
                            <input type="hidden" name="isAdmin" value="1">
                        @endif

                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-center font-bold text-sm mb-2">🎉 {{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-center font-bold text-sm mb-2">🚨 {{ session('error') }}</div>
                        @endif

                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Selected Date</label>
                            <input type="date" name="reservation_date" id="reservation_date" value="{{ $date }}" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none cursor-not-allowed" readonly required>
                        </div>
                        
                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Player Name</label>
                            <input type="text" name="player_name" id="player_name" placeholder="Enter Name" 
                                value="{{ session('prefill_name') ?? old('player_name') }}" 
                                class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d] {{ session('reschedule_id') ? 'opacity-70 cursor-not-allowed' : '' }}" 
                                {{ session('reschedule_id') ? 'readonly' : 'required' }}>
                        </div>

                        @if(!isset($isAdmin) || !$isAdmin)
                            @if(session('reschedule_id'))
                                <div>
                                    <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Email Address</label>
                                    <input type="email" name="email" value="{{ session('prefill_email') }}" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none opacity-70 cursor-not-allowed" readonly>
                                    <input type="hidden" id="isEmailVerified" value="true">
                                </div>
                            @else
                                <div id="emailContainer">
                                    <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Email Address</label>
                                    <div class="flex gap-2">
                                        <input type="email" name="email" id="email" placeholder="example@gmail.com" class="flex-1 bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                                        <button type="button" id="sendOtpBtn" onclick="sendOtp()" class="bg-[#2a8b9d] text-white px-4 rounded-lg font-bold text-sm hover:bg-[#1c4e63] transition-colors whitespace-nowrap">Verify</button>
                                    </div>
                                    <div id="otpSection" class="hidden mt-2 flex gap-2">
                                        <input type="text" id="otpInput" placeholder="Enter 6-digit code" class="flex-1 bg-[#fffde7] border border-yellow-300 rounded-lg p-3 text-sm text-yellow-800 font-bold outline-none text-center tracking-widest">
                                        <button type="button" id="verifyOtpBtn" onclick="verifyOtp()" class="bg-green-600 text-white px-4 rounded-lg font-bold text-sm hover:bg-green-700 transition-colors">Confirm Code</button>
                                    </div>
                                    <span id="emailStatus" class="text-xs font-bold mt-1 block"></span>
                                    <input type="hidden" id="isEmailVerified" value="false">
                                </div>
                            @endif
                        @endif
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Start Time</label>
                                @php
                                    $allTimes = ['09:00' => '9:00 AM','10:00' => '10:00 AM','11:00' => '11:00 AM','12:00' => '12:00 PM','13:00' => '1:00 PM','14:00' => '2:00 PM','15:00' => '3:00 PM','16:00' => '4:00 PM','17:00' => '5:00 PM','18:00' => '6:00 PM','19:00' => '7:00 PM','20:00' => '8:00 PM','21:00' => '9:00 PM','22:00' => '10:00 PM'];
                                    $bookedHours = [];
                                    if(isset($bookedSlots)) {
                                        foreach($bookedSlots as $slot) {
                                            $start = \Carbon\Carbon::parse($slot->start_time);
                                            $end = \Carbon\Carbon::parse($slot->end_time);
                                            while($start->lt($end)) {
                                                $bookedHours[] = $start->format('H:i');
                                                $start->addHour();
                                            }
                                        }
                                    }
                                @endphp
                                <select name="start_time" id="start_time" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                                    <option value="" disabled selected>Select a time</option>
                                    @foreach($allTimes as $value => $label)
                                        @if(in_array($value, $bookedHours))
                                            <option value="{{ $value }}" disabled style="color: #999; background: #eee;">{{ $label }} (Booked)</option>
                                        @else
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Duration</label>
                                <select name="{{ session('reschedule_id') ? 'duration_locked' : 'duration' }}" id="duration" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none {{ session('reschedule_id') ? 'opacity-70 cursor-not-allowed' : 'focus:border-[#2a8b9d]' }}" {{ session('reschedule_id') ? 'disabled' : 'required' }}>
                                    @for ($i = 1; $i <= 9; $i++)
                                        <option value="{{ $i }}" {{ (int)session('prefill_duration') === $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Hour' : 'Hours' }}</option>
                                    @endfor
                                </select>
                                @if(session('reschedule_id'))
                                    <input type="hidden" name="duration" value="{{ session('prefill_duration') }}">
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Paddle & Ball Rental</label>
                            <select name="{{ session('reschedule_id') ? 'rent_locked' : 'rent_equipment' }}" id="rent_equipment" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none {{ session('reschedule_id') ? 'opacity-70 cursor-not-allowed' : 'focus:border-[#2a8b9d]' }}" {{ session('reschedule_id') ? 'disabled' : '' }}>
                                <option value="0" {{ (int)session('prefill_equipment') === 0 ? 'selected' : '' }}>None</option>
                                <option value="100" {{ (int)session('prefill_equipment') === 100 ? 'selected' : '' }}>1 Set (Paddle & Ball) - ₱100</option>
                                <option value="200" {{ (int)session('prefill_equipment') === 200 ? 'selected' : '' }}>2 Sets (Paddle & Ball) - ₱200</option>
                            </select>
                            @if(session('reschedule_id'))
                                <input type="hidden" name="rent_equipment" value="{{ session('prefill_equipment') }}">
                            @endif
                        </div>

                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Payment Method</label>
                            <select name="{{ session('reschedule_id') ? 'payment_locked' : 'payment_method' }}" id="paymentMethod" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none" onchange="toggleQR()" {{ session('reschedule_id') ? 'disabled' : 'required' }}>
                                @if(isset($isAdmin) && $isAdmin)
                                    <option value="Cash" {{ strcasecmp(session('prefill_payment'), 'Cash') === 0 ? 'selected' : '' }}>Cash</option>
                                    <option value="Online" {{ strcasecmp(session('prefill_payment'), 'Online') === 0 || strcasecmp(session('prefill_payment'), 'GCash') === 0 ? 'selected' : '' }}>Online (GCash)</option>
                                @else
                                    <option value="Online" selected>Online (GCash)</option>
                                @endif
                            </select>
                            @if(session('reschedule_id'))
                                <input type="hidden" name="payment_method" value="{{ session('prefill_payment') }}">
                            @endif
                        </div>

                        @if(!session('reschedule_id'))
                            @empty($isAdmin)
                                <div id="qrCodeSection" style="display: none;" class="mt-2 p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-3">
                                    <div class="text-center">
                                        <p class="text-[12px] font-bold text-blue-800 uppercase mb-3 leading-none">Scan to Pay (GCash)</p>
                                        <img src="{{ asset('images/qr-code.jpeg') }}" alt="QR" class="w-64 h-auto mx-auto border-white rounded-lg shadow-sm">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block mb-1">GCash Reference Number</label>
                                        <input type="text" name="reference_number" placeholder="Enter 13-digit number" class="w-full p-2 text-sm border border-blue-200 rounded-md focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block mb-1">Upload Receipt Image</label>
                                        <input type="file" name="proof_of_payment" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:bg-blue-600 file:text-white">
                                    </div>
                                </div>
                            @endempty
                        @endif
                        
                        <div class="flex justify-between items-center bg-[#1c4e63] text-white p-4 rounded-lg mt-4 shadow-inner shrink-0">
                            <span class="font-bold tracking-widest text-sm uppercase">Total Amount</span>
                            <span id="live_total" class="font-bold text-xl">₱0</span>
                        </div>

                        <button type="submit" class="w-full bg-[#2a8b9d] hover:bg-[#1c4e63] transition-colors text-white py-3 rounded-lg font-bold tracking-widest mt-4 shadow-md shrink-0">
                            {{ session('reschedule_id') ? 'CONFIRM RESCHEDULE' : 'RESERVE NOW' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // --- KIOSK WELCOME, GUIDELINES & AUTO-RESET LOGIC ---
        const welcomeBoard = document.getElementById('welcome-board');
        const guidelinesModal = document.getElementById('guidelines-modal');
        const agreeBtn = document.getElementById('agree-btn');
        let idleTimer;

        function showWelcomeBoard() {
            @if(!session('reschedule_id'))
                if (welcomeBoard) {
                    const form = document.getElementById('bookingForm');
                    if (form) form.reset();
                    document.getElementById('live_total').innerText = '₱0';
                    if(document.getElementById('qrCodeSection')) document.getElementById('qrCodeSection').style.display = 'none';
                    
                    const emailInput = document.getElementById('email');
                    if (emailInput && !emailInput.readOnly) {
                        document.getElementById('isEmailVerified').value = 'false';
                        const sendOtpBtn = document.getElementById('sendOtpBtn');
                        if (sendOtpBtn) {
                            sendOtpBtn.classList.remove('hidden');
                            sendOtpBtn.textContent = 'Verify';
                            sendOtpBtn.disabled = false;
                        }
                        const otpSection = document.getElementById('otpSection');
                        if (otpSection) otpSection.classList.add('hidden');
                        const otpInput = document.getElementById('otpInput');
                        if (otpInput) otpInput.value = '';
                        const emailStatus = document.getElementById('emailStatus');
                        if (emailStatus) emailStatus.textContent = '';
                    }

                    if (guidelinesModal) {
                        guidelinesModal.classList.add('hidden');
                        guidelinesModal.classList.remove('flex');
                    }

                    welcomeBoard.classList.remove('hidden');
                    setTimeout(() => { welcomeBoard.classList.remove('opacity-0', 'pointer-events-none'); }, 50);
                }
            @endif
        }

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(showWelcomeBoard, 120000); 
        }

        if (welcomeBoard) {
            welcomeBoard.addEventListener('click', () => {
                welcomeBoard.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => { 
                    welcomeBoard.classList.add('hidden'); 
                    if (guidelinesModal) {
                        guidelinesModal.classList.remove('hidden');
                        guidelinesModal.classList.add('flex');
                    }
                }, 700);
                resetIdleTimer();
            });

            if (agreeBtn) {
                agreeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (guidelinesModal) {
                        guidelinesModal.classList.add('hidden');
                        guidelinesModal.classList.remove('flex');
                    }
                    resetIdleTimer();
                });
            }

            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => 
                window.addEventListener(evt, resetIdleTimer, false)
            );
        }

        // --- CALCULATION LOGIC ---
        function calculateTotal() {
            const timeVal = document.getElementById('start_time').value;
            const durationInput = document.querySelector('select[name="duration"]');
            const duration = durationInput ? parseInt(durationInput.value) : (parseInt(document.getElementById('duration').value) || 1);
            
            const equipmentInput = document.querySelector('select[name="rent_equipment"]');
            const equipmentFee = equipmentInput ? parseInt(equipmentInput.value) : (parseInt(document.getElementById('rent_equipment').value) || 0);
            if (!timeVal && !{{ session('reschedule_id') ? 'true' : 'false' }}) {
                document.getElementById('live_total').innerText = '₱0';
                return;
            }

            let hourlyRate = 200;
            let total = (hourlyRate * duration) + equipmentFee; 
            document.getElementById('live_total').innerText = '₱' + total;
        }

        function updateClock() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateEl = document.getElementById('current-date');
            const clockEl = document.getElementById('clock');
            if(dateEl) dateEl.innerText = now.toLocaleDateString('en-US', dateOptions);
            if(clockEl) clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: true });
        }
        setInterval(updateClock, 1000);
        updateClock();

        function toggleQR() {
            const methodSelect = document.getElementById('paymentMethod');
            const qrSection = document.getElementById('qrCodeSection');
            
            if (qrSection && methodSelect) {
                const methodHidden = document.querySelector('input[name="payment_method"]');
                const method = methodHidden ? methodHidden.value : methodSelect.value;
                if (method === 'Online' || method === 'GCash') {
                    qrSection.style.display = 'block';
                } else {
                    qrSection.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
            toggleQR();

            const calendarDays = document.getElementById('calendar-days');
            const monthYearDisplay = document.getElementById('month-year-display');
            const urlParams = new URLSearchParams(window.location.search);
            let activeDate = urlParams.get('date') ? new Date(urlParams.get('date')) : new Date();
            let currentMonth = activeDate.getMonth();
            let currentYear = activeDate.getFullYear();
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            function renderCalendar(month, year) {
                if(!calendarDays) return;
                calendarDays.innerHTML = '';
                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const today = new Date();
                today.setHours(0,0,0,0);
                for (let i = 0; i < firstDay; i++) { calendarDays.innerHTML += `<div class="bg-gray-50 h-24"></div>`;
                }
                for (let i = 1; i <= daysInMonth; i++) {
                    const checkDate = new Date(year, month, i);
                    const dayDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    const activeDateStr = `${activeDate.getFullYear()}-${String(activeDate.getMonth() + 1).padStart(2, '0')}-${String(activeDate.getDate()).padStart(2, '0')}`;
                    const isSelected = activeDateStr === dayDateStr;
                    const isPast = checkDate < today;
                    let dayClass = isPast ?
                        'bg-gray-100 text-gray-400 cursor-not-allowed' : (isSelected ? 'bg-[#fffde7] font-bold text-[#1c4e63] shadow-inner' : 'bg-white hover:bg-[#f0f9fb] text-gray-700 cursor-pointer');
                    let clickHandler = isPast ?
                        '' : `onclick="selectDate('${dayDateStr}')"`;
                    calendarDays.innerHTML += `<div class="p-3 h-24 transition-colors border-t border-l border-gray-100 flex justify-end items-start ${dayClass}" ${clickHandler}>${i}</div>`;
                }
            }

            window.selectDate = function(dateStr) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('date', dateStr);
                window.location.href = "?" + urlParams.toString(); 
            }
            document.getElementById('prev-month').addEventListener('click', () => { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(currentMonth, currentYear); });
            document.getElementById('next-month').addEventListener('click', () => { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(currentMonth, currentYear); });
            document.getElementById('today-btn').addEventListener('click', () => { const now = new Date(); selectDate(`${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`); });
            renderCalendar(currentMonth, currentYear);

            const form = document.getElementById('bookingForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const isVerified = document.getElementById('isEmailVerified');
                    if (isVerified && isVerified.value === "false") {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Verification Required', text: 'Please verify your email before reserving.', confirmButtonText: 'Got it!', confirmButtonColor: '#2a8b9d' });
                    }
                });
            }
        });

        // --- OTP Logic ---
        async function sendOtp() {
            const email = document.getElementById('email').value;
            const status = document.getElementById('emailStatus');
            const btn = document.getElementById('sendOtpBtn');
            const csrfToken = document.querySelector('input[name="_token"]').value;
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                status.textContent = 'Valid @gmail.com required.';
                status.className = 'text-xs font-bold mt-1 block text-red-500';
                return;
            }
            btn.disabled = true;
            btn.textContent = 'Sending...';
            try {
                const response = await fetch("{{ route('otp.send') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ email: email })
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('otpSection').classList.remove('hidden');
                    status.textContent = 'Code sent! Check your email.';
                    status.className = 'text-xs font-bold mt-1 block text-green-600';
                    btn.textContent = 'Resend';
                    btn.disabled = false;
                } else {
                    status.textContent = data.message || 'Failed to send OTP.';
                    status.className = 'text-xs font-bold mt-1 block text-red-500';
                    btn.textContent = 'Verify';
                    btn.disabled = false;
                }
            } catch (error) {
                status.textContent = 'Error connecting to server.';
                status.className = 'text-xs font-bold mt-1 block text-red-500';
                btn.textContent = 'Verify';
                btn.disabled = false;
            }
        }

        async function verifyOtp() {
            const email = document.getElementById('email').value;
            const otp = document.getElementById('otpInput').value;
            const status = document.getElementById('emailStatus');
            const csrfToken = document.querySelector('input[name="_token"]').value;
            if (!otp) {
                status.textContent = 'Please enter the code.';
                status.className = 'text-xs font-bold mt-1 block text-red-500';
                return;
            }
            try {
                const response = await fetch("{{ route('otp.verify') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({ email: email, otp: otp })
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('isEmailVerified').value = 'true';
                    document.getElementById('email').readOnly = true;
                    document.getElementById('otpSection').classList.add('hidden');
                    document.getElementById('sendOtpBtn').classList.add('hidden');
                    status.textContent = '✓ Email Verified';
                    status.className = 'text-xs font-bold mt-1 block text-green-600';
                } else {
                    status.textContent = data.message || 'Invalid code. Try again.';
                    status.className = 'text-xs font-bold mt-1 block text-red-500';
                }
            } catch (error) {
                status.textContent = 'Verification failed.';
                status.className = 'text-xs font-bold mt-1 block text-red-500';
            }
        }
    </script>
</body>
</html>