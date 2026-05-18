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
        /* This strictly kills the horizontal scroll */
        html { scroll-behavior: smooth; overflow-x: hidden; }
        body { overflow-x: hidden; }
        
        .interface-view { display: none; }
        .interface-view.active { display: block; animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media print {
            aside, .no-print, button, form, nav { display: none !important; }
            main { width: 100% !important; margin: 0 !important; padding: 0 !important; display: block !important; }
            .interface-view { display: block !important; }
            .analytics-grid { display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #2a8b9d; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#e4f6f9] font-sans h-screen flex overflow-hidden">

    <aside class="w-64 bg-[#165166] text-white flex flex-col justify-between no-print shadow-xl shrink-0">
        <div class="p-6 overflow-y-auto">
            <h1 class="text-2xl font-black tracking-wider">ADMIN PANEL</h1>
            <nav class="mt-8 space-y-2">
                <button onclick="showInterface('dashboard')" id="btn-dash" class="w-full flex items-center px-4 py-3 bg-[#2a8b9d] rounded-xl font-medium shadow-md transition-all nav-btn">
                    <i class="fa-solid fa-chart-pie w-5 h-5 mr-3 text-center"></i>
                    Dashboard
                </button>
                
                <a href="{{ route('admin.reserve') }}" class="flex items-center px-4 py-3 text-white hover:bg-[#2a8b9d] rounded-xl font-medium transition-all">
                    <i class="fa-solid fa-calendar-plus w-5 h-5 mr-3 text-center"></i>
                    Walk-in Booking
                </a>

                <button onclick="showInterface('bookings')" id="btn-book" class="w-full flex items-center px-4 py-3 text-white hover:bg-[#2a8b9d] rounded-xl font-medium transition-all nav-btn">
                    <i class="fa-solid fa-list-check w-5 h-5 mr-3 text-center"></i>
                    Booked Slots
                </button>

                <form action="{{ route('admin.updatePromo') }}" method="POST" class="mt-6 border-t border-[#0f3d4d] pt-6 pb-2">
                    @csrf 
                    <h3 class="text-[10px] font-bold text-[#86c5d6] uppercase tracking-wider mb-4 px-4">
                        <i class="fa-solid fa-tags mr-1"></i> Pricing Mode
                    </h3>
                    
                    <div class="px-4 space-y-4">
                        <div>
                            <label class="text-[10px] text-[#86c5d6] font-bold uppercase mb-2 block tracking-widest">
                                Discount Percent (%)
                            </label>
                            <div class="flex gap-2">
                                <input type="number" name="global_discount" value="{{ $global_discount ?? 0 }}" min="0" max="100" class="w-full bg-[#0f3d4d] border border-[#1c4e63] rounded-lg px-3 py-2 text-white font-bold outline-none focus:border-[#2a8b9d] text-sm shadow-inner">
                                <button type="submit" class="bg-[#2a8b9d] hover:bg-[#35a7bc] text-white px-3 py-2 rounded-lg text-[10px] font-black uppercase transition-all shadow-md">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.logout') }}" method="POST" class="mt-6 border-t border-[#0f3d4d] pt-4">
                    @csrf 
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-red-300 hover:bg-red-500 hover:text-white rounded-xl transition-all text-left">
                        <i class="fa-solid fa-right-from-bracket w-5 h-5 mr-3 text-center"></i>
                        Log Out
                    </button>
                </form>
            </nav>
        </div>
        <div class="p-6 bg-[#0f3d4d] bg-opacity-50 mt-auto">
            <p class="text-[10px] font-bold uppercase text-[#86c5d6]" id="admin-date">Loading date...</p>
            <p class="text-xl font-mono font-bold" id="admin-clock">00:00:00</p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto overflow-x-hidden">
        <header class="px-8 py-6 flex justify-between items-center no-print border-b border-[#b2d9e2] bg-white bg-opacity-50">
            <div>
                <h2 id="header-title" class="text-2xl font-bold text-[#1c4e63]">DASHBOARD</h2>
                <p class="text-xs text-[#2a8b9d] font-bold uppercase tracking-widest">Island Central Mactan</p>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500 text-white rounded-xl shadow-lg flex items-center animate-bounce">
                    <i class="fa-solid fa-circle-check mr-3"></i>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-3"></i>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <div id="view-dashboard" class="interface-view active">
                <div class="grid grid-cols-4 gap-6 mb-8 analytics-grid">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#2a8b9d] flex justify-between items-center relative overflow-hidden">
                        <div class="z-10">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Bookings</p>
                            <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $total ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-address-book text-5xl text-[#2a8b9d] opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-green-500 flex justify-between items-center relative overflow-hidden">
                        <div class="z-10">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Paid</p>
                            <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $paid ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-circle-check text-5xl text-green-500 opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-yellow-400 flex justify-between items-center relative overflow-hidden">
                        <div class="z-10">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pending</p>
                            <h3 class="text-4xl font-bold text-[#1c4e63]">{{ $pending ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-clock-rotate-left text-5xl text-yellow-400 opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#165166] flex justify-between items-center relative overflow-hidden">
                        <div class="z-10">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue</p>
                            <h3 class="text-4xl font-bold text-[#1c4e63]">₱{{ number_format($revenue ?? 0, 2) }}</h3>
                        </div>
                        <i class="fa-solid fa-sack-dollar text-5xl text-[#165166] opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 border border-white">
                    <h4 class="text-[10px] font-bold text-[#2a8b9d] uppercase mb-6 tracking-[0.2em]">Booking Activity Trend</h4>
                    <div class="h-80">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="view-bookings" class="interface-view">
                
                <div class="flex justify-end mb-4 no-print">
                    <form action="{{ route('admin.bookings.print') }}" method="GET" target="_blank" class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-[#b2d9e2]">
                        <div class="flex flex-col">
                            <label class="text-[8px] font-black text-[#2a8b9d] uppercase tracking-tighter">From Date</label>
                            <input type="date" name="start_date" class="text-[10px] font-bold text-[#1c4e63] border-none p-0 focus:ring-0 cursor-pointer">
                        </div>
                        <div class="h-6 w-[1px] bg-gray-100"></div>
                        <div class="flex flex-col">
                            <label class="text-[8px] font-black text-[#2a8b9d] uppercase tracking-tighter">To Date</label>
                            <input type="date" name="end_date" class="text-[10px] font-bold text-[#1c4e63] border-none p-0 focus:ring-0 cursor-pointer">
                        </div>
                        <button type="submit" class="bg-[#1c4e63] text-white h-10 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#2a8b9d] transition-all flex items-center gap-2">
                            <i class="fa-solid fa-print"></i> Print Report
                        </button>
                    </form>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-[#b2d9e2] mb-6 no-print">
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="hidden" name="active_tab" value="bookings">
                        
                        <div>
                            <label class="text-[10px] font-black text-[#2a8b9d] uppercase mb-1 block tracking-widest">Search Player</label>
                            <input type="text" name="search_name" value="{{ request('search_name') }}" placeholder="Enter name..." 
                                class="w-full bg-[#f7fcfd] border border-[#e4f6f9] rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a8b9d]">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-[#2a8b9d] uppercase mb-1 block tracking-widest">Status</label>
                            <select name="filter_status" class="w-full bg-[#f7fcfd] border border-[#e4f6f9] rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a8b9d]">
                                <option value="">All Status</option>
                                <option value="paid" {{ request('filter_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('filter_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-[#2a8b9d] uppercase mb-1 block tracking-widest">Payment Method</label>
                            <select name="filter_mop" class="w-full bg-[#f7fcfd] border border-[#e4f6f9] rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a8b9d]">
                                <option value="">All MOP</option>
                                <option value="CASH" {{ request('filter_mop') == 'CASH' ? 'selected' : '' }}>Cash</option>
                                <option value="GCASH" {{ request('filter_mop') == 'GCASH' ? 'selected' : '' }}>GCash</option>
                                <option value="ONLINE" {{ request('filter_mop') == 'ONLINE' ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-[#2a8b9d] text-white py-2 rounded-lg text-xs font-bold uppercase hover:bg-[#1c4e63] transition-all shadow-md">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.dashboard') }}?active_tab=bookings" class="bg-gray-100 text-gray-500 px-3 py-2 rounded-lg text-xs font-bold uppercase text-center hover:bg-gray-200 transition-all flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                @if(request()->hasAny(['search_name', 'filter_status', 'filter_mop']))
                    @php 
                        $sumSales = isset($reservations) ? $reservations->sum('price') : 0; 
                    @endphp
                    <div class="bg-[#1c4e63] px-6 py-4 rounded-xl shadow-md flex justify-between items-center mb-6 no-print">
                        <p class="text-[#86c5d6] text-[10px] font-black uppercase tracking-widest">
                            <i class="fa-solid fa-filter mr-2"></i> Total Sales for Current Filter
                        </p>
                        <p class="text-white text-2xl font-black">₱{{ number_format($sumSales, 2) }}</p>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm border border-[#b2d9e2] overflow-hidden">
                    <div class="bg-[#f7fcfd] border-b border-[#e4f6f9] px-6 py-3 grid grid-cols-6 gap-4 text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest hidden md:grid">
                        <div>Time & Date</div>
                        <div class="col-span-2">Player Name</div>
                        <div>Amount & MOP</div>
                        <div>Status</div>
                        <div class="text-right">Action</div>
                    </div>

                    <div class="divide-y divide-[#e4f6f9]">
                        @if(isset($reservations) && $reservations->count() > 0)
                            @foreach($reservations as $res)
                            <div class="px-6 py-3 grid grid-cols-1 md:grid-cols-6 gap-4 items-center hover:bg-[#fcfefe] transition-colors">
                                
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-[#1c4e63]">
                                        <i class="fa-regular fa-clock text-[#2a8b9d] mr-1"></i> {{ $res->start_time }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">
                                        {{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}
                                    </span>
                                </div>

                                <div class="col-span-2 font-bold text-[#1c4e63] text-sm truncate">
                                    {{ $res->player_name }}
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-[#1c4e63]">₱{{ number_format($res->price, 2) }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $res->payment_method ?? 'CASH' }}</span>
                                </div>

                                <div>
                                    <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase inline-block {{ $res->status == 'paid' || $res->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $res->status }}
                                    </span>
                                </div>

                                <div class="text-right no-print">
                                    @if($res->status == 'pending')
                                        <form action="{{ route('admin.reservations.approve', $res->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="bg-[#2a8b9d] hover:bg-[#1c4e63] text-white px-4 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-wider transition-all shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-300 text-[10px] font-bold uppercase">
                                            <i class="fa-solid fa-check"></i> Done
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-list-ul text-4xl mb-3 opacity-20"></i>
                                <p class="text-xs font-medium">No reservations found matching your filters.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($reservations) && method_exists($reservations, 'links'))
                    <div class="mt-6 no-print">
                        {{ $reservations->appends(request()->query())->links() }}
                    </div>
                @endif

            </div>
        </div>
    </main>

    <script>
        function showInterface(view) {
            document.querySelectorAll('.interface-view').forEach(v => v.classList.remove('active'));
            
            const viewElement = document.getElementById('view-' + view);
            if(viewElement) {
                viewElement.classList.add('active');
            }

            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('bg-[#2a8b9d]', 'shadow-md'));
            
            const activeBtn = view === 'dashboard' ? 'btn-dash' : 'btn-book';
            const btnElement = document.getElementById(activeBtn);
            if(btnElement) {
                btnElement.classList.add('bg-[#2a8b9d]', 'shadow-md');
            }

            const headerTitle = document.getElementById('header-title');
            if(headerTitle) {
                headerTitle.innerText = view.toUpperCase();
            }

            localStorage.setItem('activeAdminTab', view);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Check for explicit tab request or filter parameters
            const explicitTab = urlParams.get('active_tab');
            const hasFilters = urlParams.has('search_name') || 
                               urlParams.has('filter_status') || 
                               urlParams.has('filter_mop') || 
                               urlParams.has('page');
            
            let tabToShow = localStorage.getItem('activeAdminTab') || 'dashboard';

            if (explicitTab) {
                tabToShow = explicitTab;
            } else if (hasFilters) {
                tabToShow = 'bookings';
            }
            
            showInterface(tabToShow);
        });

        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            if(document.getElementById('admin-clock')) document.getElementById('admin-clock').innerText = timeStr;
            if(document.getElementById('admin-date')) document.getElementById('admin-date').innerText = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        const chartLabels = {!! json_encode($days ?? ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"]) !!};
        const chartData = {!! json_encode($counts ?? [0,0,0,0,0,0,0]) !!};

        const canvasElement = document.getElementById('mainChart');
        if(canvasElement) {
            const ctx = canvasElement.getContext('2d');
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
                        pointBackgroundColor: '#1c4e63'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, grid: { color: '#f0f9fb' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
</body>
</html>