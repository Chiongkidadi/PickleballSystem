<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Reservation Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pulse animation for the background circle */
        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.2; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        .animate-ring { animation: pulse-ring 4s cubic-bezier(0, 0, 0.2, 1) infinite; }

        html, body { height: 100%; overflow: hidden; }
        .hidden-tab { display: none; }
    </style>
</head>
<body class="bg-[#e4f6f9] font-sans antialiased text-[#1c4e63] flex flex-col h-screen overflow-hidden">

    <div id="welcome-board" 
         class="fixed inset-0 z-50 bg-[#165166] flex flex-col items-center justify-center text-white cursor-pointer transition-all duration-700 ease-in-out">
        
        <div class="mb-12">
            <img src="{{ asset('image/island-central-logo.jpeg') }}" alt="Island Central Logo" class="h-16 w-auto object-contain drop-shadow-lg opacity-80">
        </div>

        <div class="flex flex-row items-center gap-12 mb-20">
            <div class="relative w-56 h-56 flex items-center justify-center shrink-0">
                <div class="absolute inset-0 animate-ring bg-[#2a8b9d] rounded-full"></div>
                <div class="absolute inset-2 bg-[#2a8b9d] rounded-full shadow-lg border-2 border-[#86c5d6]"></div>
                
                <img src="{{ asset('image/pickleball-logo.jpeg') }}" 
                     alt="Pickleball Logo" 
                     class="z-10 w-48 h-48 object-contain rounded-full shadow-2xl bg-white border-4 border-[#165166]">
            </div>

            <div class="text-left">
                <h1 class="text-6xl md:text-8xl font-black mb-1 tracking-widest uppercase leading-none">Pickleball</h1>
                <h2 class="text-3xl md:text-4xl font-bold tracking-[0.4em] text-[#86c5d6] uppercase">Reservation Kiosk</h2>
            </div>
        </div>
        
        <div class="animate-bounce flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
            </svg>
            <p class="text-xl font-bold tracking-widest uppercase bg-[#2a8b9d] px-12 py-4 rounded-full shadow-2xl border border-[#86c5d6]">
                Tap Screen to Begin
            </p>
        </div>

        <div class="absolute bottom-8 text-center opacity-40">
            <p class="text-xs uppercase tracking-[0.5em]">Island Central Mactan • MEPZ Ecozone</p>
        </div>
    </div>

    <div id="main-application" class="hidden-tab flex flex-col h-screen transition-opacity duration-700 ease-in-out opacity-0">
        
        <header class="bg-white px-8 py-4 flex justify-between items-center shadow-md border-b border-[#b2d9e2]">
            <div class="flex items-center gap-4">
                <div class="bg-white p-1 rounded-full border-2 border-[#165166] shadow-sm">
                    <img src="{{ asset('image/pickleball-logo.jpeg') }}" class="w-12 h-12 object-contain rounded-full">
                </div>
                <div class="flex flex-col">
                    <h1 class="text-2xl font-black tracking-wider text-[#1c4e63]">PickleballReservation.com</h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-[-2px]">Island Central Mactan</p>
                </div>
            </div>
            
            <div class="text-right">
                <p id="clock-date" class="text-xs uppercase text-gray-500 font-bold tracking-widest mb-1">DATE</p>
                <p id="clock-time" class="text-3xl font-mono font-bold tracking-wider text-[#2a8b9d]">00:00:00</p>
            </div>
        </header>

        <main class="flex-1 p-8 grid grid-cols-2 gap-8 items-stretch max-w-[1400px] mx-auto w-full h-[calc(100%-100px)]">
            <section class="bg-white p-8 rounded-3xl shadow-sm border border-[#b2d9e2]">
                <h2 class="text-[#2a8b9d] font-bold text-sm tracking-widest uppercase mb-4">Book Your Slot</h2>
                <div class="h-[calc(100%-36px)] bg-[#f7fcfd] rounded-2xl border border-gray-100 p-6 flex items-center justify-center">
                    <p class="text-gray-400">Loading...</p>
                </div>
            </section>
            
            <section class="bg-white p-8 rounded-3xl shadow-sm border border-[#b2d9e2]">
                <h2 class="text-[#2a8b9d] font-bold text-sm tracking-widest uppercase mb-4">Current Status</h2>
                <div class="h-[calc(100%-36px)] bg-[#f7fcfd] rounded-2xl border border-gray-100 p-6 flex items-center justify-center">
                    <p class="text-gray-400">Active Courts</p>
                </div>
            </section>
        </main>
    </div>

    <script>
        const welcomeBoard = document.getElementById('welcome-board');
        const mainApplication = document.getElementById('main-application');

        const urlParams = new URLSearchParams(window.location.search);
        const hasDate = urlParams.has('date');
        const isRescheduling = {{ session('reschedule_id') ? 'true' : 'false' }};

        if (hasDate || isRescheduling) {
            welcomeBoard.classList.add('hidden-tab');
            mainApplication.classList.remove('hidden-tab');
            mainApplication.classList.add('flex');
            mainApplication.style.opacity = '1';
        }

        welcomeBoard.addEventListener('click', () => {
            welcomeBoard.style.opacity = '0';
            welcomeBoard.style.pointerEvents = 'none';
            setTimeout(() => {
                welcomeBoard.classList.add('hidden-tab');
                mainApplication.classList.remove('hidden-tab');
                mainApplication.classList.add('flex');
                requestAnimationFrame(() => {
                    mainApplication.style.opacity = '1';
                });
            }, 700);
        });

        function updateClock() {
            const now = new Date();
            const timeElement = document.getElementById('clock-time');
            const dateElement = document.getElementById('clock-date');
            if (timeElement) timeElement.innerText = now.toLocaleTimeString('en-GB');
            if (dateElement) {
                dateElement.innerText = now.toLocaleDateString('en-US', { 
                    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' 
                }).toUpperCase();
            }
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>