<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            aside, .no-print, button, form, nav, #mobile-menu-btn { display: none !important; }
            main { width: 100% !important; margin: 0 !important; padding: 0 !important; display: block !important; }
            .interface-view { display: block !important; }
            .analytics-grid { display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #2a8b9d; border-radius: 10px; }

        /* --- NEW AESTHETIC BUTTON STYLES --- */
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
        }

        .btn-approve { background-color: #d1fae5; color: #065f46; }
        .btn-approve:hover { background-color: #10b981; color: white; transform: translateY(-2px); }

        .btn-cancel { background-color: #fee2e2; color: #991b1b; }
        .btn-cancel:hover { background-color: #ef4444; color: white; transform: translateY(-2px); }

        .btn-view-modern { background-color: #1c4e63; color: white; }
        .btn-view-modern:hover { background-color: #2a8b9d; transform: translateY(-2px); }
    </style>
</head>
<body class="bg-[#e4f6f9] font-sans h-screen flex overflow-hidden relative">

    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="absolute md:relative z-50 w-64 h-full bg-[#165166] text-white flex flex-col justify-between no-print shadow-xl shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="p-6 overflow-y-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-black tracking-wider">ADMIN PANEL</h1>
                <button class="md:hidden text-white" onclick="toggleSidebar()">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <nav class="mt-8 space-y-2">
                <button onclick="showInterface('dashboard'); toggleSidebar();" id="btn-dash" class="w-full flex items-center px-4 py-3 bg-[#2a8b9d] rounded-xl font-medium shadow-md transition-all nav-btn">
                    <i class="fa-solid fa-chart-pie w-5 h-5 mr-3 text-center"></i>
                    Dashboard
                </button>
                
                <a href="{{ route('admin.reserve') }}" class="flex items-center px-4 py-3 text-white hover:bg-[#2a8b9d] rounded-xl font-medium transition-all">
                    <i class="fa-solid fa-calendar-plus w-5 h-5 mr-3 text-center"></i>
                    Walk-in Booking
                </a>

                <button onclick="showInterface('bookings'); toggleSidebar();" id="btn-book" class="w-full flex items-center px-4 py-3 text-white hover:bg-[#2a8b9d] rounded-xl font-medium transition-all nav-btn">
                    <i class="fa-solid fa-list-check w-5 h-5 mr-3 text-center"></i>
                    Booked Slots
                </button>

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

    <main class="flex-1 flex flex-col h-screen overflow-y-auto overflow-x-hidden w-full">
        <header class="px-4 md:px-8 py-6 flex items-center no-print border-b border-[#b2d9e2] bg-white bg-opacity-50 sticky top-0 z-30">
            <button id="mobile-menu-btn" class="md:hidden text-[#1c4e63] text-2xl mr-4" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h2 id="header-title" class="text-2xl font-bold text-[#1c4e63]">DASHBOARD</h2>
                <p class="text-xs text-[#2a8b9d] font-bold uppercase tracking-widest">Island Central Mactan</p>
            </div>
        </header>

        <div class="p-4 md:p-8">
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 analytics-grid">
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
                            <h3 class="text-3xl lg:text-4xl font-bold text-[#1c4e63]">₱{{ number_format($revenue ?? 0, 2) }}</h3>
                        </div>
                        <i class="fa-solid fa-sack-dollar text-5xl text-[#165166] opacity-10 absolute right-[-10px] bottom-[-10px]"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 border border-white">
                    <h4 class="text-[10px] font-bold text-[#2a8b9d] uppercase mb-6 tracking-[0.2em]">Booking Activity Trend</h4>
                    <div class="h-80 w-full">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="view-bookings" class="interface-view">
                <div class="flex flex-col md:flex-row justify-end mb-4 no-print gap-2">
                    <form action="{{ route('admin.bookings.print') }}" method="GET" target="_blank" class="flex flex-wrap md:flex-nowrap items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-[#b2d9e2]">
                        <div class="flex flex-col">
                            <label class="text-[8px] font-black text-[#2a8b9d] uppercase tracking-tighter">From Date</label>
                            <input type="date" name="start_date" class="text-[10px] font-bold text-[#1c4e63] border-none p-0 focus:ring-0 cursor-pointer">
                        </div>
                        <div class="h-6 w-[1px] bg-gray-100 hidden md:block"></div>
                        <div class="flex flex-col">
                            <label class="text-[8px] font-black text-[#2a8b9d] uppercase tracking-tighter">To Date</label>
                            <input type="date" name="end_date" class="text-[10px] font-bold text-[#1c4e63] border-none p-0 focus:ring-0 cursor-pointer">
                        </div>
                        <button type="submit" class="bg-[#1c4e63] text-white h-10 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#2a8b9d] transition-all flex items-center gap-2 w-full md:w-auto justify-center mt-2 md:mt-0">
                            <i class="fa-solid fa-print"></i> Print
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

                <div class="bg-white rounded-2xl shadow-sm border border-[#b2d9e2] overflow-hidden">
                    <div class="bg-[#f7fcfd] border-b border-[#e4f6f9] px-6 py-3 grid grid-cols-7 gap-4 text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest hidden lg:grid">
                        <div class="col-span-1">Time & Date</div>
                        <div class="col-span-2">Player Name</div>
                        <div class="col-span-1">Court</div>
                        <div class="col-span-1">Amount & MOP</div>
                        <div class="col-span-1">Status</div>
                        <div class="col-span-1 text-right">Action</div>
                    </div>

                    <div class="divide-y divide-[#e4f6f9]">
                        @if(isset($reservations) && $reservations->count() > 0)
                            @foreach($reservations as $res)
                            <div class="px-4 lg:px-6 py-4 grid grid-cols-1 lg:grid-cols-7 gap-4 lg:items-center hover:bg-[#fcfefe] transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-[#1c4e63]">
                                        <i class="fa-regular fa-clock text-[#2a8b9d] mr-1"></i> {{ $res->start_time }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">
                                        {{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}
                                    </span>
                                </div>

                                <div class="lg:col-span-2 font-bold text-[#1c4e63] text-sm truncate flex items-center justify-between lg:justify-start">
                                    <span>{{ $res->player_name }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-600">{{ $res->court ?? 'Court 1' }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-[#1c4e63]">₱{{ number_format($res->price, 2) }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $res->payment_method ?? 'CASH' }}</span>
                                </div>

                                <div class="hidden lg:block">
                                    <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase inline-block {{ $res->status == 'paid' || $res->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $res->status }}
                                    </span>
                                </div>

                                <div class="text-right no-print flex items-center justify-end gap-2 lg:col-span-1">
                                    <button type="button"
                                            onclick="openDetailsModal(this)"
                                            data-player="{{ $res->player_name }}"
                                            data-ref="{{ $res->reference_number ?? 'N/A' }}"
                                            data-date="{{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }} at {{ $res->start_time }}"
                                            data-amount="₱{{ number_format($res->price, 2) }} ({{ $res->payment_method ?? 'CASH' }})"
                                            data-court="{{ $res->court ?? 'Court 1' }}"
                                            data-proof="{{ !empty($res->proof_of_payment) ? asset('storage/' . $res->proof_of_payment) : '' }}"
                                            class="btn-action btn-view-modern" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    @if($res->status == 'pending')
                                        <form action="{{ route('admin.reservations.approve', $res->id) }}" method="POST" class="inline confirm-form" data-type="approve" data-name="{{ $res->player_name }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn-action btn-approve trigger-swal" title="Approve">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.reservations.cancel', $res->id) }}" method="POST" class="inline confirm-form" data-type="cancel" data-name="{{ $res->player_name }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn-action btn-cancel trigger-swal" title="Cancel">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div class="px-2">
                                            <i class="fa-solid fa-circle-check text-green-500 opacity-40"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-list-ul text-4xl mb-3 opacity-20"></i>
                                <p class="text-xs font-medium">No reservations found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="bookingDetailsModal" class="fixed inset-0 z-[9999] hidden bg-black bg-opacity-60 items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-[#f7fcfd] border-b border-[#e4f6f9] px-6 py-4 flex justify-between items-center shrink-0">
                <h2 class="text-[#1c4e63] font-bold text-lg">Booking Details</h2>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-1">Player Name</p>
                        <p id="modalPlayerName" class="font-bold text-[#1c4e63] text-sm"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-1">Court</p>
                        <p id="modalCourt" class="font-bold text-[#1c4e63] text-sm"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4 border-t border-[#e4f6f9] pt-4">
                    <div>
                        <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-1">Date & Time</p>
                        <p id="modalDateTime" class="font-bold text-[#1c4e63] text-sm"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-1">Amount</p>
                        <p id="modalAmount" class="font-bold text-green-600 text-sm"></p>
                    </div>
                </div>
                <div class="mb-4 border-t border-[#e4f6f9] pt-4">
                    <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-1">Reference Number</p>
                    <p id="modalRefNumber" class="font-mono font-bold text-[#1c4e63] bg-[#e4f6f9] px-2 py-1 rounded inline-block text-sm"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#2a8b9d] uppercase tracking-widest mb-2">Proof of Payment</p>
                    <div class="bg-[#f7fcfd] border border-[#e4f6f9] rounded-xl p-2 flex justify-center items-center min-h-[150px]">
                        <img id="modalProofImage" src="" alt="Proof of Payment" class="max-w-full max-h-[300px] rounded-lg hidden shadow-sm object-contain">
                        <div id="modalNoProofText" class="text-gray-400 text-sm font-medium italic hidden flex-col items-center">
                            <i class="fa-regular fa-image text-3xl mb-2 opacity-50"></i>
                            No image uploaded
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 text-right rounded-b-2xl border-t border-gray-100 shrink-0">
                <button onclick="closeDetailsModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase transition-colors shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // SweetAlert Confirmation Logic
        document.addEventListener('click', function (e) {
            if (e.target.closest('.trigger-swal')) {
                const button = e.target.closest('.trigger-swal');
                const form = button.closest('form');
                const type = form.getAttribute('data-type');
                const playerName = form.getAttribute('data-name');

                const config = {
                    approve: {
                        title: 'Approve Booking?',
                        text: `You are about to approve the reservation for ${playerName}.`,
                        icon: 'question',
                        confirmButtonText: 'Yes, Approve',
                        confirmButtonColor: '#10b981'
                    },
                    cancel: {
                        title: 'Cancel Booking?',
                        text: `Are you sure you want to cancel the reservation for ${playerName}?`,
                        icon: 'warning',
                        confirmButtonText: 'Yes, Cancel it',
                        confirmButtonColor: '#ef4444'
                    }
                };

                const current = config[type];

                Swal.fire({
                    title: current.title,
                    text: current.text,
                    icon: current.icon,
                    showCancelButton: true,
                    confirmButtonColor: current.confirmButtonColor,
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: current.confirmButtonText,
                    borderRadius: '15px',
                    background: '#fcfefe',
                    customClass: {
                        title: 'text-[#1c4e63] font-bold',
                        popup: 'rounded-2xl border border-[#b2d9e2]'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        // UI Interaction Functions
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('mobile-overlay').classList.toggle('hidden');
        }

        function showInterface(viewId) {
            document.querySelectorAll('.interface-view').forEach(el => el.classList.remove('active'));
            document.getElementById('view-' + viewId).classList.add('active');
        }

        function openDetailsModal(btn) {
            document.getElementById('modalPlayerName').innerText = btn.getAttribute('data-player');
            document.getElementById('modalCourt').innerText = btn.getAttribute('data-court');
            document.getElementById('modalDateTime').innerText = btn.getAttribute('data-date');
            document.getElementById('modalAmount').innerText = btn.getAttribute('data-amount');
            document.getElementById('modalRefNumber').innerText = btn.getAttribute('data-ref');
            
            const proof = btn.getAttribute('data-proof');
            if (proof && proof !== window.location.origin + '/storage/') {
                document.getElementById('modalProofImage').src = proof;
                document.getElementById('modalProofImage').classList.remove('hidden');
                document.getElementById('modalNoProofText').classList.add('hidden');
                document.getElementById('modalNoProofText').classList.remove('flex');
            } else {
                document.getElementById('modalProofImage').classList.add('hidden');
                document.getElementById('modalNoProofText').classList.remove('hidden');
                document.getElementById('modalNoProofText').classList.add('flex');
            }
            
            document.getElementById('bookingDetailsModal').classList.remove('hidden');
            document.getElementById('bookingDetailsModal').classList.add('flex');
        }

        function closeDetailsModal() {
            document.getElementById('bookingDetailsModal').classList.add('hidden');
            document.getElementById('bookingDetailsModal').classList.remove('flex');
        }
    </script>
</body>
</html>