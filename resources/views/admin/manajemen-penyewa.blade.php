<x-app-layout>
    <div class="min-h-screen bg-[#F2F4F7] p-8">
        {{-- Tombol Kembali --}}
        <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9] mb-8 transition-colors">
            Kembali
        </a>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-[#344054] mb-8">Manajemen Penyewa</h1>

            {{-- Dropdown Pilih Cabang --}}
            <div class="relative mb-10">
                <select id="branch-select" class="w-full appearance-none bg-white border border-gray-200 rounded-2xl py-4 px-6 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                    <option value="">Pilih Cabang Kost</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            {{-- Container Kartu Penyewa --}}
            <div id="tenant-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full py-20 text-center">
                    <p class="text-gray-400 italic">Silahkan pilih cabang untuk melihat daftar penyewa...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('branch-select').addEventListener('change', function() {
        const branchId = this.value;
        const container = document.getElementById('tenant-container');

        if (!branchId) {
            container.innerHTML = '<div class="col-span-full py-20 text-center text-gray-400 italic">Silahkan pilih cabang...</div>';
            return;
        }

        // Tampilan Loading
        container.innerHTML = '<div class="col-span-full py-20 text-center text-purple-600 font-medium">Memuat data penyewa...</div>';

        // Fetch data dari Controller
        fetch(`{{ url('/admin/get-tenants') }}/${branchId}`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="col-span-full py-20 text-center text-gray-400">Tidak ada penyewa di cabang ini.</div>';
                    return;
                }

                data.forEach(tenant => {
                    // Penentuan warna dan label berdasarkan status dari Controller
                    let statusClass = '';
                    let statusLabel = '';

                    if (tenant.status === 'Aktif') {
                        statusClass = 'bg-[#ECFDF3] text-[#027A48]'; // Hijau
                        statusLabel = 'AKTIF';
                    } else if (tenant.status === 'Hampir Habis') {
                        statusClass = 'bg-[#FFFAEB] text-[#B54708]'; // Kuning/Oranye
                        statusLabel = `AKAN BERAKHIR (${tenant.days_left} HARI)`;
                    } else {
                        statusClass = 'bg-[#FEF3F2] text-[#B42318]'; // Merah
                        statusLabel = 'MASA SEWA HABIS';
                    }
                    
                    // Render HTML Kartu
                    container.innerHTML += `
                        <div class="bg-white border border-gray-100 rounded-[2rem] p-8 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-md transition-all">
                            <h3 class="text-xl font-bold text-[#101828] mb-4">${tenant.name}</h3>
                            <div class="space-y-2 text-sm text-[#667085] mb-6">
                                <div class="flex justify-between">
                                    <span>Nomor Kamar:</span>
                                    <span class="font-bold text-gray-800">${tenant.room_number}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Biaya Sewa:</span>
                                    <span class="font-bold text-gray-800">Rp ${tenant.price}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>${tenant.status === 'Habis' ? 'Berakhir Pada:' : 'Jatuh Tempo:'}</span>
                                    <span class="font-bold ${tenant.status === 'Habis' ? 'text-red-500' : 'text-gray-800'}">${tenant.due_date}</span>
                                </div>
                            </div>
                            <span class="inline-block px-5 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase ${statusClass}">
                                ${statusLabel}
                            </span>
                        </div>
                    `;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="col-span-full py-20 text-center text-red-500 font-medium">Gagal mengambil data penyewa.</div>';
            });
    });
    </script>
</x-app-layout>