<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Reservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#e0f2f7] font-sans h-screen flex flex-col">

    <header class="bg-[#2a8b9d] text-white py-6 px-10 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-6">
            
            @if(isset($isAdmin) && $isAdmin)
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-[#1c4e63] text-white px-5 py-2.5 rounded-lg shadow-sm font-bold flex items-center gap-2 hover:bg-[#11303d] transition-colors duration-300"
               style="text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
            @endif
            
            <div>
                <h1 class="text-3xl font-bold tracking-wider">PICKLEBALL RESERVATION</h1>
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
                <div id="calendar-days" class="grid grid-cols-7 gap-px bg-gray-200 border-x border-b border-gray-200 rounded-b-lg overflow-hidden">
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-[#2a8b9d] px-4 py-3 flex justify-between items-center text-white">
                    <h3 class="font-bold text-sm tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        BOOKED SLOTS
                    </h3>
                    <span class="text-xs">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                </div>
                <div class="p-6 text-center text-sm text-[#2a8b9d] font-medium bg-[#f0f9fb]">
                    @if($bookedSlots->isEmpty())
                        <p class="flex items-center justify-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            All slots are open for this day!
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
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mx-6 mt-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mx-6 mt-4 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('reserve.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Selected Date</label>
                        <input type="date" name="reservation_date" id="reservation_date" value="{{ $date }}" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none cursor-not-allowed" readonly required>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Player Name</label>
                        <input type="text" name="player_name" placeholder="Enter Name" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Start Time</label>
                            <select name="start_time" id="start_time" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="22:00">10:00 PM</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Duration</label>
                            <select name="duration" id="duration" onchange="calculateTotal()" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" required>
                                <option value="1">1 Hour</option>
                                <option value="2">2 Hours</option>
                                <option value="3">3 Hours</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 flex items-center justify-between">
                        <label for="rent_equipment" class="text-sm font-bold text-[#1c4e63] flex items-center cursor-pointer">
                            <input type="checkbox" name="rent_equipment" id="rent_equipment" value="1" onchange="calculateTotal()" class="w-4 h-4 text-[#2a8b9d] bg-white border-gray-300 rounded focus:ring-[#2a8b9d] mr-3">
                            Rent Paddle & Ball
                        </label>
                        <span class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider">+ ₱100</span>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#2a8b9d] uppercase tracking-wider block mb-1">Payment Method</label>
                        <select name="payment_method" id="paymentMethod" class="w-full bg-[#f0f9fb] border border-[#b2d9e2] rounded-lg p-3 text-sm text-[#1c4e63] font-medium outline-none focus:border-[#2a8b9d]" onchange="toggleQR()" required>
                            <option value="Cash">Cash (Pay at counter)</option>
                            <option value="Online">Online (GCash/PayMaya)</option>
                        </select>
                    </div>

                    <div id="qrCodeSection" style="display: none;" class="text-center mt-2">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Scan QR to Pay</p>
                        <div class="flex justify-center">
                            <img src="{{ asset('image/qr-code.jpeg') }}" alt="QR Code" class="w-32 h-32 object-cover border-2 border-[#b2d9e2] rounded-lg shadow-sm">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center bg-[#1c4e63] text-white p-4 rounded-lg mt-4">
                        <span class="font-bold tracking-widest text-sm uppercase">Total Amount</span>
                        <span id="live_total" class="font-bold text-xl">₱150</span>
                    </div>

                    <button type="submit" class="w-full bg-[#2a8b9d] hover:bg-[#1c4e63] transition-colors text-white py-3 rounded-lg font-bold tracking-widest mt-2">
                        RESERVE NOW
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // --- 1. Clock Logic ---
        function updateClock() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-US', { hour12: true });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- 2. Live Price Calculator Logic ---
        function calculateTotal() {
            const timeVal = document.getElementById('start_time').value; // e.g., "09:00"
            const duration = parseInt(document.getElementById('duration').value); // 1, 2, or 3
            const isRenting = document.getElementById('rent_equipment').checked;

            if (!timeVal) return;

            // Extract just the hour (e.g., "09:00" becomes 9)
            const hour = parseInt(timeVal.split(':')[0]);

            // Determine Hourly Rate: 150 before 12:00 PM, 200 from 12:00 PM onward
            let hourlyRate = (hour < 12) ? 150 : 200;

            // Calculate Base Fee
            let total = hourlyRate * duration;

            // Add Equipment Fee if checked
            if (isRenting) {
                total += 100;
            }

            // Update the display
            document.getElementById('live_total').innerText = '₱' + total;
        }

        // --- 3. Calendar Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            // Run calculator on load to establish the base price
            calculateTotal();

            const calendarDays = document.getElementById('calendar-days');
            const monthYearDisplay = document.getElementById('month-year-display');
            const dateInput = document.getElementById('reservation_date');

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

                for (let i = 0; i < firstDay; i++) {
                    calendarDays.innerHTML += `<div class="bg-gray-50 h-24"></div>`;
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const dayDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    const activeDateStr = `${activeDate.getFullYear()}-${String(activeDate.getMonth() + 1).padStart(2, '0')}-${String(activeDate.getDate()).padStart(2, '0')}`;
                    
                    const isSelected = activeDateStr === dayDateStr;
                    const selectedClass = isSelected 
                        ? 'bg-[#fffde7] font-bold text-[#1c4e63] shadow-inner' 
                        : 'bg-white hover:bg-[#f0f9fb] text-gray-700';

                    calendarDays.innerHTML += `
                        <div class="p-3 h-24 cursor-pointer transition-colors flex justify-end items-start ${selectedClass}" 
                             onclick="selectDate('${dayDateStr}')">
                            ${i}
                        </div>
                    `;
                }
            }

            window.selectDate = function(dateStr) {
                dateInput.value = dateStr;
                window.location.href = "?date=" + dateStr;
            }

            document.getElementById('prev-month').addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) { currentMonth = 11; currentYear--; }
                renderCalendar(currentMonth, currentYear);
            });

            document.getElementById('next-month').addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) { currentMonth = 0; currentYear++; }
                renderCalendar(currentMonth, currentYear);
            });

            document.getElementById('today-btn').addEventListener('click', () => {
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                selectDate(todayStr);
            });

            renderCalendar(currentMonth, currentYear);
        });

        // --- 4. Payment Method QR Logic ---
        window.toggleQR = function() {
            var method = document.getElementById('paymentMethod').value;
            var qrSection = document.getElementById('qrCodeSection');
            
            if (method === 'Online') {
                qrSection.style.display = 'block';
            } else {
                qrSection.style.display = 'none';
            }
        };
    </script>
</body>
</html>