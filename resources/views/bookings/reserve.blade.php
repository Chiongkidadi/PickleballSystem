<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Reservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom animation for the pulsing "Tap" button */
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        .animate-pulse-soft { animation: pulse-soft 2s infinite ease-in-out; }
    </style>
</head>
<body class="bg-[#e0f2f7] font-sans h-screen flex flex-col overflow-hidden">

    {{-- 1. WELCOME BOARD OVERLAY (With Your New Logo) --}}
    @if(!isset($isAdmin) || !$isAdmin)
    <div id="welcome-board" class="fixed inset-0 z-[100] bg-[#165166] flex flex-col items-center justify-center text-white cursor-pointer transition-all duration-700 ease-in-out">
        
        <div class="mb-6">
            <img src="{{ asset('image/island-central-logo.png') }}" alt="Island Central" class="h-16 w-auto object-contain opacity-90 drop-shadow-lg">
        </div>

        <div class="relative w-56 h-56 mb-8 flex items-center justify-center">
            <div class="absolute inset-0 bg-[#2a8b9d] rounded-full shadow-2xl border-4 border-[#86c5d6] opacity-40 animate-pulse"></div>
            
            <img src="{{ asset('image/pickleball-logo.jpeg') }}" 
                 alt="Pickleball Logo" 
                 class="z-10 w-48 h-48 object-cover rounded-full shadow-2xl border-4 border-[#165166]">
        </div>

        <h1 class="text-5xl md:text-7xl font-black mb-2 tracking-widest text-center">PICKLEBALL</h1>
        <h2 class="text-2xl md:text-3xl font-bold tracking-[0.3em] text-[#86c5d6] mb-16 text-center uppercase">Reservation Kiosk</h2>
        
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
    @endif

    <header class="bg-[#2a8b9d] text-white py-6 px-10 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-6">
            @if(isset($isAdmin) && $isAdmin)
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-[#1c4e63] text-white px-5 py-2.5 rounded-lg shadow-sm font-bold flex items-center gap-2 hover:bg-[#11303d] transition-colors duration-300"
               style="text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
            @endif
            <div>
                <h1 class="text-3xl font-bold tracking-wider text-white">PICKLEBALL RESERVATION</h1>
                <p class="text-xs uppercase opacity-80 mt-1">Island Central Mactan - MEPZ Ecozone, ML Quezon Hwy, Lapu-Lapu City</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase opacity-80" id="current-date"></p>
            <p class="text-4xl font-bold font-mono tracking-widest" id="clock">00:00:00 AM</p>
        </div>
    </header>

    <main class="flex-1 p-8 grid grid-cols-3 gap-6 max-w-7xl mx-auto w-full">
        <div class="col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col p-6">
            <h2 class="text-[#2a8b9d] font-bold text-sm tracking-widest mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                SELECT A DATE TO RESERVE
            </h2>
            <div class="flex-1 flex flex-col mt-4">
                <div class="flex justify-between items-center mb-6 px-2">
                    <div class="flex gap-2">
                        <button id="prev-month" class="bg-[#2a8b9d] hover:bg-[#1c4e63] text-white px-3 py-1 rounded-md transition-colors">&lt;</button>
                        <button id="next-month" class="bg-[#2a8b9d] hover:bg-[#1c4e63] text-white px-3 py-1 rounded-md transition-colors">&gt;</button>
                        <button id="today-btn" class="bg-[#b2d9e2] text-[#1c4e63] hover:bg-[#8ebcc9] px-4 py-1 rounded-md font-bold text-sm uppercase transition-colors">today</button>
                    </div>
                    <h3 id="month-year-display" class="font-bold text-[#1c4e63] text-2xl uppercase tracking-widest"></h3>
                </div>
                <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-t-lg overflow-hidden">
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Sun</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Mon</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Tue</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Wed</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Thu</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Fri</div>
                    <div class="bg-white py-2 text-center text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">Sat</div>
                </div>
                <div id="calendar-days" class="grid grid-cols-7 gap-px bg-gray-200 border-x border-b border-gray-200 rounded-b-lg overflow-hidden"></div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-[#2a8b9d] px-4 py-3 flex justify-between items-center text-white">
                    <h3 class="font-bold text-sm tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        BOOKED SLOTS
                    </h3>
                    <span class="text-xs">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                </div>
                <div class="p-6 text-center text-sm text-[#2a8b9d] font-medium bg-[#f0f9fb]">
                    @if($bookedSlots->isEmpty())
                        <p class="flex items-center justify-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            All slots are open!
                        </p>
                    @else
                        @foreach($bookedSlots as $slot)
                            <p class="mb-1">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</p>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-[#1c4e63] tracking-widest text-lg">NEW BOOKING</h3>
                </div>
                
                <form action="{{ route('reserve.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="bookingForm">
                    @csrf
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-center font-bold text-sm mb-2">
                            🎉 {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-center font-bold text-sm mb-2">
                            🚨 {{ session('error') }}
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Selected Date</label>
                        <input type="date" name="reservation_date" id="reservation_date" value="{{ $date }}" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none cursor-not-allowed" readonly required>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Player Name</label>
                        <input type="text" name="player_name" id="player_name" placeholder="Enter Name" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                    </div>

                    @if(!isset($isAdmin) || !$isAdmin)
                    <div id="emailContainer">
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Email Address</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" id="email" placeholder="example@gmail.com" class="flex-1 bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d] @error('email') border-red-500 @enderror" required>
                            <button type="button" id="sendOtpBtn" onclick="sendOtp()" class="bg-[#2a8b9d] text-white px-4 rounded-lg font-bold text-sm hover:bg-[#1c4e63] transition-colors whitespace-nowrap">Verify</button>
                        </div>
                        <div id="otpSection" class="hidden mt-2 flex gap-2">
                            <input type="text" id="otpInput" placeholder="Enter 6-digit code" class="flex-1 bg-[#fffde7] border border-yellow-300 rounded-lg p-3 text-sm text-yellow-800 font-bold outline-none text-center tracking-widest">
                            <button type="button" id="verifyOtpBtn" onclick="verifyOtp()" class="bg-green-600 text-white px-4 rounded-lg font-bold text-sm hover:bg-green-700 transition-colors">Confirm Code</button>
                        </div>
                        <span id="emailStatus" class="text-xs font-bold mt-1 block">
                            @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                        </span>
                        <input type="hidden" id="isEmailVerified" value="false">
                    </div>
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
                            <select name="duration" id="duration" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                                @for ($i = 1; $i <= 9; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Hour' : 'Hours' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Paddle & Ball Rental</label>
                        <select name="rent_equipment" id="rent_equipment" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]">
                            <option value="0">None</option>
                            <option value="100">1 Set (Paddle & Ball) - ₱100</option>
                            <option value="200">2 Sets (Paddle & Ball) - ₱200</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Payment Method</label>
                        <select name="payment_method" id="paymentMethod" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" onchange="toggleQR()" required>
                            <option value="Cash">Cash</option>
                            @if(!isset($isAdmin) || !$isAdmin)
                                <option value="Online">Online (GCash)</option>
                            @endif
                        </select>
                    </div>

                    <div id="qrCodeSection" style="display: none;" class="mt-2 p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-3">
                        <div class="text-center">
                            <p class="text-[10px] font-bold text-blue-800 uppercase mb-2 leading-none">Scan to Pay (GCash)</p>
                            <img src="{{ asset('image/qr-code.jpeg') }}" alt="QR" class="w-24 h-24 mx-auto border-2 border-white rounded-lg shadow-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block mb-1">GCash Reference Number</label>
                            <input type="text" name="reference_number" placeholder="Enter 13-digit number" class="w-full p-2 text-sm border border-blue-200 rounded-md focus:outline-none focus:border-blue-400">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block mb-1">Upload Receipt Image</label>
                            <input type="file" name="proof_of_payment" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center bg-[#1c4e63] text-white p-4 rounded-lg mt-4 shadow-inner">
                        <span class="font-bold tracking-widest text-sm uppercase">Total Amount</span>
                        <span id="live_total" class="font-bold text-xl">₱0</span>
                    </div>

                    <button type="submit" class="w-full bg-[#2a8b9d] hover:bg-[#1c4e63] transition-colors text-white py-3 rounded-lg font-bold tracking-widest mt-2 shadow-md">
                        RESERVE NOW
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const globalDiscount = {{ $global_discount ?? 0 }};

        // --- KIOSK WELCOME & AUTO-RESET LOGIC ---
        const welcomeBoard = document.getElementById('welcome-board');
        let idleTimer;

        function hideWelcomeBoard() {
            if (welcomeBoard && !welcomeBoard.classList.contains('hidden')) {
                welcomeBoard.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => { welcomeBoard.classList.add('hidden'); }, 700);
            }
        }

        function showWelcomeBoard() {
            if (welcomeBoard) {
                const form = document.getElementById('bookingForm');
                if (form) form.reset();
                document.getElementById('live_total').innerText = '₱0';
                if(document.getElementById('qrCodeSection')) document.getElementById('qrCodeSection').style.display = 'none';
                
                welcomeBoard.classList.remove('hidden');
                setTimeout(() => { welcomeBoard.classList.remove('opacity-0', 'pointer-events-none'); }, 50);
            }
        }

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(showWelcomeBoard, 120000); 
        }

        if (welcomeBoard) {
            welcomeBoard.addEventListener('click', () => {
                hideWelcomeBoard();
                resetIdleTimer();
            });
            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => 
                window.addEventListener(evt, resetIdleTimer, false)
            );
        }

        // --- LOGIC ---
        function calculateTotal() {
            const timeVal = document.getElementById('start_time').value;
            const duration = parseInt(document.getElementById('duration').value) || 1;
            const equipmentFee = parseInt(document.getElementById('rent_equipment').value) || 0;
            if (!timeVal) return;
            const hour = parseInt(timeVal.split(':')[0]);
            let hourlyRate = (hour < 12) ? 300 : 400;
            let total = hourlyRate * duration;
            if (globalDiscount > 0) total -= (globalDiscount / 100) * total;
            total += equipmentFee;
            document.getElementById('live_total').innerText = '₱' + Math.round(total);
        }

        function updateClock() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-US', { hour12: true });
        }
        setInterval(updateClock, 1000);
        updateClock();

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
            const calendarDays = document.getElementById('calendar-days');
            const monthYearDisplay = document.getElementById('month-year-display');
            const urlParams = new URLSearchParams(window.location.search);
            let activeDate = urlParams.get('date') ? new Date(urlParams.get('date')) : new Date();
            let currentMonth = activeDate.getMonth();
            let currentYear = activeDate.getFullYear();
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            function renderCalendar(month, year) {
                calendarDays.innerHTML = '';
                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const today = new Date();
                today.setHours(0,0,0,0);
                for (let i = 0; i < firstDay; i++) { calendarDays.innerHTML += `<div class="bg-gray-50 h-24"></div>`; }
                for (let i = 1; i <= daysInMonth; i++) {
                    const checkDate = new Date(year, month, i);
                    const dayDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    const activeDateStr = `${activeDate.getFullYear()}-${String(activeDate.getMonth() + 1).padStart(2, '0')}-${String(activeDate.getDate()).padStart(2, '0')}`;
                    const isSelected = activeDateStr === dayDateStr;
                    const isPast = checkDate < today;
                    let dayClass = isPast ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : (isSelected ? 'bg-[#fffde7] font-bold text-[#1c4e63] shadow-inner' : 'bg-white hover:bg-[#f0f9fb] text-gray-700 cursor-pointer');
                    let clickHandler = isPast ? '' : `onclick="selectDate('${dayDateStr}')"`;
                    calendarDays.innerHTML += `<div class="p-3 h-24 transition-colors border-t border-l border-gray-100 flex justify-end items-start ${dayClass}" ${clickHandler}>${i}</div>`;
                }
            }

            window.selectDate = function(dateStr) { window.location.href = "?date=" + dateStr; }
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

        window.toggleQR = function() {
            var method = document.getElementById('paymentMethod').value;
            var qrSection = document.getElementById('qrCodeSection');
            if(qrSection) qrSection.style.display = (method === 'Online') ? 'block' : 'none';
        };

        async function sendOtp() {
            const email = document.getElementById('email').value;
            const status = document.getElementById('emailStatus');
            const btn = document.getElementById('sendOtpBtn');
            const csrfToken = document.querySelector('input[name="_token"]').value;
            if (!email.match(/^[\w\-\.]+@gmail\.com$/i)) {
                status.textContent = 'Valid @gmail.com required.';
                status.className = 'text-xs font-bold mt-1 block text-red-500';
                return;
            }
            btn.disabled = true; btn.textContent = 'Sending...';
            try {
                let response = await fetch('/send-otp', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ email: email }) });
                let result = await response.json();
                if (result.success) {
                    document.getElementById('otpSection').classList.replace('hidden', 'flex');
                    status.textContent = 'Code sent!'; status.className = 'text-xs font-bold mt-1 block text-blue-500';
                    btn.textContent = 'Sent';
                } else { status.textContent = result.message; btn.disabled = false; btn.textContent = 'Verify'; }
            } catch (e) { status.textContent = 'Network error.'; btn.disabled = false; }
        }

        async function verifyOtp() {
            const email = document.getElementById('email').value;
            const otp = document.getElementById('otpInput').value;
            const status = document.getElementById('emailStatus');
            const btn = document.getElementById('verifyOtpBtn');
            const csrfToken = document.querySelector('input[name="_token"]').value;
            if (!otp) return;
            btn.disabled = true; btn.textContent = '...';
            try {
                let response = await fetch('/verify-otp', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ email: email, otp: otp }) });
                let result = await response.json();
                if (result.success) {
                    status.textContent = '✅ Verified!'; status.className = 'text-xs font-bold mt-1 block text-green-600';
                    document.getElementById('otpSection').classList.add('hidden');
                    document.getElementById('sendOtpBtn').classList.add('hidden');
                    document.getElementById('email').readOnly = true;
                    document.getElementById('isEmailVerified').value = "true";
                } else { status.textContent = '❌ Invalid code.'; btn.disabled = false; btn.textContent = 'Confirm'; }
            } catch (e) { status.textContent = 'Error.'; btn.disabled = false; }
        }
    </script>
</body>
</html>