<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">
    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    @include('partials.navbar')

    <div class="flex pt-20">
        @include('partials.sidebar')

        <main id="mainContent" class="flex-1 p-6 md:p-10 transition-all-300">
            <div class="min-h-screen">
                <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9] mb-8 transition-colors">
                    Kembali
                </a>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <h1 class="text-2xl font-bold text-[#344054] mb-8">Manajemen Penyewa</h1>

                    <div class="relative mb-8">
                        <select id="branch-select" class="w-full appearance-none bg-white border border-gray-200 rounded-2xl py-4 px-6 text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                            <option value="">Pilih Cabang Kost</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Navigasi Tab --}}
                    <div class="flex gap-6 mb-8 border-b border-gray-100">
                        <button onclick="filterList('aktif')" id="tab-aktif" class="pb-3 px-2 font-black text-xs text-[#7C3AED] border-b-2 border-[#7C3AED] transition-all uppercase tracking-widest">
                            PENYEWA AKTIF
                        </button>
                        <button onclick="filterList('riwayat')" id="tab-riwayat" class="pb-3 px-2 font-black text-xs text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-all uppercase tracking-widest">
                            RIWAYAT (SELESAI)
                        </button>
                    </div>

                    <div id="tenant-container" class="flex flex-col gap-4">
                        <div class="py-20 text-center text-gray-400 italic">Silahkan pilih cabang untuk melihat daftar penyewa...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Modal Detail --}}
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
                <div id="roomListContainer" class="space-y-3 max-h-52 overflow-y-auto pr-1 custom-scrollbar"></div>
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
    let allTenantsData = []; 
    let currentFilter = 'aktif';

    document.getElementById('branch-select').addEventListener('change', function() {
        fetchData(this.value);
    });

    function fetchData(branchId) {
        const container = document.getElementById('tenant-container');
        if (!branchId) return;

        container.innerHTML = '<div class="py-20 text-center text-purple-600 font-medium animate-pulse text-sm">Memuat data penyewa...</div>';

        fetch(`{{ url('/admin/get-tenants') }}/${branchId}`)
            .then(res => res.json())
            .then(data => {
                allTenantsData = data;
                renderList(currentFilter);
            });
    }

    function renderList(filterType) {
        const container = document.getElementById('tenant-container');
        container.innerHTML = '';

        // Filter: Tab Aktif ambil is_active true, Tab Riwayat ambil is_active false
        const filtered = allTenantsData.filter(t => filterType === 'aktif' ? t.is_active : !t.is_active);

        if (filtered.length === 0) {
            container.innerHTML = `<div class="py-32 text-center text-gray-400 text-sm italic">Tidak ada data penyewa ${filterType}.</div>`;
            return;
        }

        filtered.forEach((tenant, index) => {
            const statusClass = tenant.is_active ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500';
            window[`tenantData_${index}`] = tenant;

            container.innerHTML += `
                <div class="bg-white border border-gray-100 rounded-3xl p-5 flex flex-col md:flex-row items-center justify-between shadow-sm hover:shadow-md transition-all">
                    <div class="flex flex-col md:flex-row items-center gap-6 lg:gap-12 flex-1 text-sm">
                        <div class="w-full md:w-40 font-black text-[#101828] uppercase tracking-tight text-center md:text-left">${tenant.name}</div>
                        <div class="text-gray-500 font-bold">Kamar: <span class="text-[#7C3AED]">${tenant.rooms_summary}</span></div>
                        <div><span class="${statusClass} px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest">${tenant.status}</span></div>
                    </div>
                    <div class="flex gap-2 mt-4 md:mt-0">
                        <button onclick="openDetail(window.tenantData_${index})" class="bg-[#7C3AED] text-white px-5 py-2.5 rounded-xl font-black hover:bg-[#6D28D9] transition-all text-[10px] uppercase tracking-widest shadow-lg shadow-purple-50 whitespace-nowrap">
                            Rincian
                        </button>
                        <button onclick="deleteTenant(${tenant.user_id}, '${tenant.name}')" class="bg-red-50 text-red-600 px-5 py-3.5 rounded-xl font-black hover:bg-red-600 hover:text-white transition-all text-[10px] uppercase tracking-widest whitespace-nowrap border border-red-100">
                            Hapus
                        </button>
                    </div>
                </div>`;
        });
    }

    function filterList(type) {
        currentFilter = type;
        ['aktif', 'riwayat'].forEach(t => {
            const el = document.getElementById(`tab-${t}`);
            if(t === type) {
                el.classList.add('text-[#7C3AED]', 'border-[#7C3AED]');
                el.classList.remove('text-gray-400', 'border-transparent');
            } else {
                el.classList.remove('text-[#7C3AED]', 'border-[#7C3AED]');
                el.classList.add('text-gray-400', 'border-transparent');
            }
        });
        renderList(type);
    }

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
                </div>`;
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
    // 1. Konfirmasi
    if (!confirm(`Ubah status kamar menjadi ${newStatus}?`)) {
        return; // Cukup return saja, jangan reload halaman
    }

    console.log("Mengirim data...", { id: bookingId, status: newStatus });

    // 2. Tampilkan loading manual (opsional tapi membantu)
    // Kamu bisa tambahkan spinner kalau mau, tapi kita fokus ke fungsionalitas dulu

    fetch(`{{ route('admin.update_tenant_status') }}`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            id: bookingId, 
            status: newStatus 
        })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Server Error');
        return data;
    })
    .then(data => {
        console.log("Berhasil:", data);
        if (data.success) {
            alert('Status berhasil diperbarui!');
            // Ambil id cabang dari select box untuk refresh list
            const branchId = document.getElementById('branch-select').value;
            fetchData(branchId); 
            closeModal();
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(err => {
        console.error("Kesalahan Fetch:", err);
        alert('Terjadi kesalahan sistem: ' + err.message);
    });
}

    function deleteTenant(tenantId, name) {
        if (!confirm(`Hapus seluruh riwayat sewa "${name}"? Akun tetap ada tapi dia akan hilang dari daftar ini.`)) return;
        fetch(`/admin/delete-tenant/${tenantId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchData(document.getElementById('branch-select').value);
            }
        });
    }
    </script>
</body>