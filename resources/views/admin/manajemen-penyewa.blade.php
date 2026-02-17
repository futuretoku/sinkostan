<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">
    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    @include('partials.navbar')

    <div class="flex pt-20">
        @include('partials.sidebar')

        <main id="mainContent" class="flex-1 p-6 md:p-10 transition-all-300">
            <div class="min-h-screen">
                {{-- Tombol Kembali --}}
                <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9] mb-8 transition-colors">
                    Kembali
                </a>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <h1 class="text-2xl font-bold text-[#344054] mb-8">Manajemen Penyewa</h1>

                    {{-- Dropdown Pilih Cabang --}}
                    <div class="relative mb-10">
                        <select id="branch-select" class="w-full appearance-none bg-white border border-gray-200 rounded-2xl py-4 px-6 text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                            <option value="">Pilih Cabang Kost</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Container List Penyewa --}}
                    <div id="tenant-container" class="flex flex-col gap-4">
                        <div class="py-20 text-center text-gray-400 italic">Silahkan pilih cabang untuk melihat daftar penyewa...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="detailModal" class="fixed inset-0 z-[1000] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl relative z-10 overflow-hidden animate-in fade-in zoom-in duration-300">
            
            <div class="p-6 pb-4 flex justify-between items-start">
                <div>
                    <h2 id="modalName" class="text-2xl font-black text-[#101828] uppercase tracking-tight leading-tight">NAMA PENYEWA</h2>
                    <p class="text-gray-400 font-bold mt-1 text-[10px] uppercase tracking-[0.2em]">Rincian Kamar Kost</p>
                </div>
                <button onclick="closeModal()" class="bg-gray-50 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 pt-0 space-y-5">
                <div id="roomListContainer" class="space-y-3 max-h-52 overflow-y-auto pr-1 custom-scrollbar">
                    </div>

                <div class="bg-[#F9FAFB] border border-gray-100 rounded-[1.5rem] p-5">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">WhatsApp</span>
                        <span id="modalPhone" class="font-bold text-gray-600 text-xs">08123456789</span>
                    </div>
                    
                    <a id="waBtn" href="#" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:opacity-90 transition-all shadow-md shadow-green-100">
                        HUBUNGI VIA WHATSAPP
                    </a>
                </div>

                <button onclick="closeModal()" class="w-full text-center text-gray-400 font-bold hover:text-gray-600 transition-colors py-1 text-[11px] uppercase tracking-tighter">
                    Tutup Jendela
                </button>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('branch-select').addEventListener('change', function() {
        const branchId = this.value;
        const container = document.getElementById('tenant-container');

        if (!branchId) {
            container.innerHTML = '<div class="py-20 text-center text-gray-400 italic text-sm">Silahkan pilih cabang...</div>';
            return;
        }

        container.innerHTML = '<div class="py-20 text-center text-purple-600 font-medium animate-pulse text-sm">Memuat data penyewa...</div>';

        fetch(`{{ url('/admin/get-tenants') }}/${branchId}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.length === 0) {
                    container.innerHTML = '<div class="py-20 text-center text-gray-400 text-sm">Tidak ada penyewa di cabang ini.</div>';
                    return;
                }

                data.forEach(tenant => {
                    const statusClass = tenant.status === 'Aktif' ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700';
                    
                    container.innerHTML += `
                        <div class="bg-white border border-gray-100 rounded-3xl p-5 flex flex-col md:flex-row items-center justify-between shadow-sm hover:shadow-md transition-all">
                            <div class="flex flex-col md:flex-row items-center gap-6 lg:gap-12 flex-1 text-sm">
                                <div class="w-full md:w-40 font-black text-[#101828] uppercase tracking-tight text-center md:text-left">${tenant.name}</div>
                                <div class="text-gray-500 font-bold">Kamar: <span class="text-[#7C3AED]">${tenant.rooms_summary}</span></div>
                                <div><span class="${statusClass} px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest">${tenant.status}</span></div>
                            </div>
                            <button onclick='openDetail(${JSON.stringify(tenant)})' class="bg-[#7C3AED] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#6D28D9] transition-all text-[10px] uppercase tracking-widest shadow-lg shadow-purple-50 mt-4 md:mt-0 whitespace-nowrap">
                                Detail & Edit
                            </button>
                        </div>`;
                });
            })
            .catch(err => {
                container.innerHTML = '<div class="py-20 text-center text-red-500 font-medium">Gagal memuat data.</div>';
            });
    });

    function openDetail(tenant) {
        document.getElementById('modalName').innerText = tenant.name;
        document.getElementById('modalPhone').innerText = tenant.phone;
        
        const listContainer = document.getElementById('roomListContainer');
        listContainer.innerHTML = '';

        tenant.room_details.forEach(room => {
            const isHabis = room.status === 'Habis';
            listContainer.innerHTML += `
                <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                    <div>
                        <h4 class="font-black text-[#101828] text-sm uppercase leading-none">Kamar ${room.room_number}</h4>
                        <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">Hingga: ${room.due_date}</p>
                    </div>
                    <select onchange="updateRoomStatus(${room.booking_id}, this.value)" class="text-[9px] font-black border-none rounded-lg focus:ring-0 py-1.5 px-2 bg-gray-50 uppercase tracking-tight cursor-pointer">
                        <option value="Aktif" ${!isHabis ? 'selected' : ''}>AKTIF</option>
                        <option value="Habis" ${isHabis ? 'selected' : ''}>HABIS</option>
                    </select>
                </div>
            `;
        });

        let phone = tenant.phone.toString().replace(/\D/g, '');
        if(phone.startsWith('0')) phone = '62' + phone.slice(1);
        document.getElementById('waBtn').href = `https://wa.me/${phone}`;

        document.getElementById('detailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function updateRoomStatus(bookingId, newStatus) {
    if (!confirm(`Ubah status kamar ini menjadi ${newStatus}?`)) {
        location.reload();
        return;
    }

    // Gunakan helper route admin.update_tenant_status
    fetch(`{{ route('admin.update_tenant_status') }}`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ id: bookingId, status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil diperbarui!');
            window.location.reload(); // Paksa refresh halaman
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan jaringan');
    });
}
    </script>
</body>