<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { 
            height: 100%; 
            overflow: hidden; 
            margin: 0; 
            background-color: #165166; 
        }

        /* Ensure the hidden-tab class really hides the element */
        .hidden-tab { 
            display: none !important; 
            visibility: hidden; 
            pointer-events: none; 
        }
        
        /* The main app container must be fixed to the full screen */
        #main-application { 
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 10; 
            opacity: 1;
        }

        /* The iframe must fill 100% of the fixed container */
        iframe { 
            width: 100%; 
            height: 100%; 
            border: none; 
            display: block;
        }
    </style>
</head>

<body class="bg-[#165166] font-sans antialiased overflow-hidden">

    @if(!session('reschedule_id'))
    <div id="welcome-board" 
         class="fixed inset-0 z-50 bg-[#165166] flex flex-col items-center justify-center text-white cursor-pointer transition-all duration-700 ease-in-out">
        
        <h1 class="text-6xl md:text-8xl font-black mb-2 tracking-widest text-center uppercase">Pickleball</h1>
        <h2 class="text-3xl md:text-4xl font-bold tracking-[0.4em] text-[#86c5d6] mb-20 text-center uppercase">Reservation Kiosk</h2>
        
        <div class="animate-bounce flex flex-col items-center">
            <p class="text-2xl font-bold tracking-widest uppercase bg-[#2a8b9d] px-12 py-4 rounded-full shadow-2xl border border-[#86c5d6]">
                Tap Screen to Begin
            </p>
        </div>
    </div>
    @endif

    <div id="main-application" 
         class="{{ session('reschedule_id') ? '' : 'hidden-tab' }}">
        <iframe src="{{ url('/kiosk') }}" id="reserve-iframe"></iframe>
    </div>

    <script>
        const welcomeBoard = document.getElementById('welcome-board');
        const mainApplication = document.getElementById('main-application');
        const iframe = document.getElementById('reserve-iframe');

        function startApp() {
            if (!welcomeBoard) return;

            // 1. Start the visual fade out
            welcomeBoard.style.opacity = '0';
            welcomeBoard.style.pointerEvents = 'none'; // Stop intercepting clicks immediately

            setTimeout(() => {
                // 2. LITERALLY REMOVE the board from the DOM so it cannot block the iframe
                welcomeBoard.remove(); 
                
                // 3. Make the main application container visible and clickable
                mainApplication.classList.remove('hidden-tab');
                mainApplication.style.display = 'block';
                
                // 4. Force a reload of the iframe to ensure it renders at the correct size
                if(iframe) {
                    iframe.style.height = '100vh';
                }
            }, 700);
        }

        if (welcomeBoard) {
            welcomeBoard.addEventListener('click', startApp);
        }

        // Handle Reschedule Mode: If reschedule_id is in session, the board is never rendered
        @if(session('reschedule_id'))
            console.log('Reschedule mode: Application active.');
        @endif

        // Optional: Ensure the iframe is clickable by forcing focus on it
        window.addEventListener('DOMContentLoaded', (event) => {
            if (mainApplication && !mainApplication.classList.contains('hidden-tab')) {
                iframe.focus();
            }
        });
    </script>
</body>
</html>