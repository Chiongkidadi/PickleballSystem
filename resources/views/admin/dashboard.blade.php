<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Print Layout Rules */
        @media print {
            aside, .no-print, button, form, nav, a.walkin-btn { display: none !important; }
            main { width: 100% !important; margin: 0 !important; padding: 0 !important; display: block !important; }
            .analytics-grid { display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
            .bg-white { border: 1px solid #eee !important; box-shadow: none !important; }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #2a8b9d; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#e4f6f9] font-sans h-screen flex overflow-hidden">

    <aside class="w-64 bg-[#165166] text-white flex flex-col justify-between no-print shadow-xl">
        <div class="p-6">
            <h1 class="text-2xl font-black tracking-wider">ADMIN PANEL</h1>
            <nav class="mt-8 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 bg-[#2a8b9d] rounded-xl font-medium shadow-md">
                    <i class="fa-solid fa-chart-pie w-5 h-5 mr-3 text-center"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.reserve') }}" class="flex items-center px-4 py-3 text-white hover:bg-[#2a8b9d] rounded-xl font-medium transition-all">
                    <i class="fa-solid fa-calendar-plus w-5 h-5 mr-3 text-center"></i>
                    Walk-in Booking
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="mt-4 border-t border-[#0f3d4d] pt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-red-300 hover:bg-red-500 hover:text-white rounded-xl transition-all text-left">
                        <i class="fa-solid fa-right-from-bracket w-5 h-5 mr-3 text-center"></i>
                        Log Out
                    </button>
                </form>
            </nav>
        </div>
        <div class="p-6 bg-[#0f3d4d] bg-opacity-50">
            <p class="text-[10px] font-bold uppercase text-[#86c5d6]" id="admin-date">Loading date...</p>
            <p class="text-xl font-mono font-bold" id="admin-clock">00:00:00</p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        
        <header class="px-8 py-6 flex justify-between items-end no-print">
            <div>
                <h2 class="text-2xl font-bold text-[#1c4e63]">RESERVATIONS</h2>
                <p class="text-xs text-[#2a8b9d] font-bold uppercase tracking-widest">Island Central Mactan</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-white border border-[#b2d9e2] rounded-full px-2 py-1 flex gap-1 shadow-sm">
                    <a href="?report=day" class="px-3 py-1 {{ request('report') == 'day' ? 'bg-[#2a8b9d] text-white' : 'text-[#1c4e63]' }} rounded-full text-[10px] font-bold transition-all">Daily</a>
                    <a href="?report=month" class="px-3 py-1 {{ request('report') == 'month' ? 'bg-[#2a8b9d] text-white' : 'text-[#1c4e63]' }} rounded-full text-[10px] font-bold transition-all">Monthly</a>
                    <a href="?report=year" class="px-3 py-1 {{ request('report') == 'year' ? 'bg-[#2a8b9d] text-white' : 'text-[#1c4e63]' }} rounded-full text-[10px] font-bold transition-all">Yearly</a>
                    <a href="?report=all" class="px-3 py-1 {{ request('report') == 'all' || !request('report') ? 'bg-[#2a8b9d] text-white' : 'text-[#1c4e63]' }} rounded-full text-[10px] font-bold transition-all">All</a>
                </div>

                <button onclick="window.print()" class="bg-[#1c4e63] text-white px-5 py-2 rounded-full text-xs font-bold shadow-md hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
        </header>

        <div class="px-8 pb-8">
            <div class="hidden print:block mb-8 border-b-2 border-[#1c4e63] pb-4">
                <h1 class="text-3xl font-black text-[#1c4e63] uppercase">{{ $reportTitle ?? 'Summary Report' }}</h1>
                <p class="text-sm text-gray-500">Pickleball Court System • {{ now()->format('F d, Y') }}</p>
            </div>

            <div class="grid grid-cols-4 gap-6 mb-8 analytics-grid">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#2a8b9d] flex justify-between items-center overflow-hidden relative">
                    <div class="z-10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Bookings</p>
                        <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $total ?? 0 }}</h3>
                    </div>
                    <i class="fa-solid fa-address-book text-5xl text-[#2a8b9d] opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-green-500 flex justify-between items-center overflow-hidden relative">
                    <div class="z-10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Paid</p>
                        <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $paid ?? 0 }}</h3>
                    </div>
                    <i class="fa-solid fa-circle-check text-5xl text-green-500 opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-yellow-400 flex justify-between items-center overflow-hidden relative">
                    <div class="z-10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pending</p>
                        <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $pending ?? 0 }}</h3>
                    </div>
                    <i class="fa-solid fa-clock-rotate-left text-5xl text-yellow-400 opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#165166] flex justify-between items-center overflow-hidden relative">
                    <div class="z-10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue</p>
                        <h3 class="text-4xl font-bold text-[#1c4e63]">₱{{ number_format($revenue ?? 0, 0) }}</h3>
                    </div>
                    <i class="fa-solid fa-sack-dollar text-5xl text-[#165166] opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 no-print border border-white">
                <h4 class="text-[10px] font-bold text-[#2a8b9d] uppercase mb-6 tracking-[0.2em]">Booking Activity Trend</h4>
                <div class="h-64">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#b2d9e2]">
                <table class="w-full text-left">
                    <thead class="bg-[#f7fcfd] border-b border-[#e4f6f9]">
                        <tr class="text-[10px] uppercase text-[#2a8b9d] font-black tracking-widest">
                            <th class="px-6 py-5">Player Name</th>
                            <th class="px-6 py-5">Date & Time</th>
                            <th class="px-6 py-5 text-center">Status</th>
                            <th class="px-6 py-5 text-right">Fee & Payment</th>
                            <th class="px-6 py-5 text-center no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @if(isset($reservations) && $reservations->count() > 0)
                            @foreach($reservations as $res)
                            <tr class="hover:bg-[#fcfdfd] transition-colors">
                                <td class="px-6 py-4 font-bold text-[#1c4e63]">{{ $res->player_name }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}
                                    <br><small class="text-gray-400 font-bold"><i class="fa-regular fa-clock"></i> {{ $res->start_time }}</small>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm {{ $res->status == 'paid' || $res->status == 'approved' ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-yellow-100 text-yellow-600 border border-yellow-200' }}">
                                        {{ $res->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-[#1c4e63]">
                                    ₱{{ number_format($res->price, 0) }}
                                    <br><small class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $res->payment_method ?? 'N/A' }}</small>
                                </td>
                                
                                <td class="px-6 py-4 text-center no-print">
                                    @if($res->status == 'pending')
                                        <form action="{{ route('admin.reservations.approve', $res->id) ?? '#' }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-2 mx-auto">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest"><i class="fa-solid fa-lock text-gray-200"></i> Done</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 flex flex-col items-center">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-20"></i>
                                    <p>No reservations found for this period.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Real-time Clock logic
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            if(document.getElementById('admin-clock')) document.getElementById('admin-clock').innerText = timeStr;
            if(document.getElementById('admin-date')) document.getElementById('admin-date').innerText = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Chart.js 
        const ctx = document.getElementById('mainChart').getContext('2d');
        const chartLabels = JSON.parse('{!! json_encode($days ?? ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"]) !!}');
        const chartData = JSON.parse('{!! json_encode($counts ?? [0,0,0,0,0,0,0]) !!}');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Bookings',
                    data: chartData,
                    borderColor: '#2a8b9d',
                    backgroundColor: 'rgba(42, 139, 157, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1c4e63',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f0f9fb' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>