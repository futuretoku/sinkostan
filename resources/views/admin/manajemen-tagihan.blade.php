<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tagihan - Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .transition-all-300 { transition: all 0.3s ease; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">

    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    {{-- Gunakan 'partial' sesuai folder di screenshot kamu --}}
    @include('partials.navbar')

    <div class="flex pt-20">
        @include('partials.sidebar')

        <main id="mainContent" class="flex-1 p-6 md:p-10 transition-all-300">
            <div class="min-h-screen">
                <div class="flex justify-between items-center mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9] transition-all">
                        &larr; Kembali
                    </a>
                </div>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <h1 class="text-2xl font-bold text-[#344054] mb-8">Manajemen Tagihan</h1>

                    {{-- Dropdown Pilih Cabang --}}
                    <div class="relative mb-10">
                        <select id="branch-select" class="w-full appearance-none bg-white border border-gray-200 rounded-2xl py-4 px-6 text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                            <option value="">Pilih Cabang Kost</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    {{-- Tabel Tagihan --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-gray-400 text-sm">
                                    <th class="pb-4 px-6 font-medium">Nama Penyewa</th>
                                    <th class="pb-4 px-6 font-medium text-center">No. Kamar</th>
                                    <th class="pb-4 px-6 font-medium text-center">Tagihan Aktif</th>
                                    <th class="pb-4 px-6 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bill-table-body">
                                <tr>
                                    <td colspan="4" class="py-20 text-center text-gray-400 italic">Silahkan pilih cabang terlebih dahulu...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Modal Rincian Tagihan --}}
    <div id="billModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[1050] flex items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 uppercase tracking-tight">Nama Penyewa</h2>
                    <p id="modalSubtitle" class="text-purple-600 font-medium"></p>
                </div>
                <button onclick="closeModal()" class="p-2 bg-white rounded-full text-gray-400 hover:text-red-500 shadow-sm transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto flex-1 bg-white" id="modalContent">
                </div>

            <div class="p-6 bg-gray-50 border-t border-gray-100 text-center">
                <button onclick="closeModal()" class="text-gray-500 font-medium hover:text-gray-700">Tutup Jendela</button>
            </div>
        </div>
    </div>

    <script>
        let branchData = [];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Ambil Data Tagihan
        document.getElementById('branch-select').addEventListener('change', function() {
            const branchId = this.value;
            const body = document.getElementById('bill-table-body');
            
            if (!branchId) {
                body.innerHTML = '<tr><td colspan="4" class="py-20 text-center text-gray-400 italic">Silahkan pilih cabang terlebih dahulu...</td></tr>';
                return;
            }

            body.innerHTML = '<tr><td colspan="4" class="py-20 text-center text-purple-600 font-medium animate-pulse">Sedang mengambil data...</td></tr>';

            fetch(`{{ url('/admin/get-bills') }}/${branchId}`)
                .then(res => res.json())
                .then(data => {
                    branchData = data;
                    body.innerHTML = '';
                    
                    if (data.length === 0) {
                        body.innerHTML = '<tr><td colspan="4" class="py-20 text-center text-gray-400">Tidak ada data tagihan di cabang ini.</td></tr>';
                        return;
                    }

                    data.forEach((item, index) => {
                        body.innerHTML += `
                            <tr class="bg-[#F9FAFB] rounded-2xl overflow-hidden shadow-sm border border-gray-50">
                                <td class="py-5 px-6 font-bold text-gray-700 rounded-l-2xl">${item.tenant_name}</td>
                                <td class="py-5 px-6 text-center text-gray-600 font-medium">Kamar ${item.room_number}</td>
                                <td class="py-5 px-6 text-center">
                                    <span class="bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-black">
                                        ${item.total_unpaid} TAGIHAN AKTIF
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-center rounded-r-2xl">
                                    <button onclick="openModal(${index})" class="bg-[#7C3AED] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#6D28D9] transition-all shadow-md active:scale-95">
                                        Rincian Tagihan
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                });
        });

        // 2. Fungsi Modal
        function openModal(index) {
            const tenant = branchData[index];
            document.getElementById('modalTitle').innerText = tenant.tenant_name;
            document.getElementById('modalSubtitle').innerText = `Nomor Kamar: ${tenant.room_number}`;
            
            let content = '';
            tenant.all_bills.forEach(bill => {
                const isPaid = bill.status === 'paid';
                const statusColor = isPaid ? 'text-green-600 bg-green-50' : (bill.status === 'pending' ? 'text-orange-600 bg-orange-100' : 'text-red-600 bg-red-50');
                const statusLabel = isPaid ? 'Lunas' : (bill.status === 'pending' ? 'Menunggu Verifikasi' : 'Belum Bayar');
                const nominal = new Intl.NumberFormat('id-ID').format(bill.amount);
                
                content += `
                <div class="flex items-center justify-between p-5 mb-4 border border-gray-100 rounded-[1.5rem] hover:border-purple-200 hover:bg-purple-50/30 transition-all shadow-sm">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Periode</p>
                        <p class="font-bold text-lg text-gray-800">${bill.month}</p>
                        <p class="text-purple-600 font-black mb-2">Rp ${nominal}</p>
                        <span class="text-[10px] uppercase font-black px-3 py-1 rounded-full ${statusColor}">${statusLabel}</span>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2">
                        ${!isPaid ? `
                            <button onclick="sendWAReminder(event, ${bill.id})" class="p-3 text-green-600 border border-green-200 rounded-2xl hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Kirim Pengingat WA">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                            </button>
                        ` : ''}
                        <button onclick="viewProof('${bill.proof_path}')" class="p-3 text-blue-500 border border-gray-200 rounded-2xl hover:bg-blue-50 transition-all" title="Lihat Bukti Transfer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        ${!isPaid ? `
                            <button onclick="confirmBill(${bill.id})" class="p-3 text-green-500 border border-green-200 rounded-2xl hover:bg-green-50 transition-all" title="Konfirmasi Lunas">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        ` : ''}
                        <button onclick="deleteBill(${bill.id})" class="p-3 text-red-500 border border-red-100 rounded-2xl hover:bg-red-50 transition-all" title="Hapus Tagihan">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                `;
            });

            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('billModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('billModal').classList.add('hidden');
        }

        // 3. Aksi Buttons
        function viewProof(path) {
            if (!path || path === 'null' || path === 'undefined') {
                alert('Penyewa belum mengunggah bukti pembayaran.');
                return;
            }
            window.open(`{{ asset('storage') }}/${path}`, '_blank');
        }

        function confirmBill(id) {
            if (!confirm('Tandai tagihan ini sebagai LUNAS?')) return;
            fetch(`{{ url('/admin/bill-confirm') }}/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                closeModal();
                document.getElementById('branch-select').dispatchEvent(new Event('change'));
            });
        }

        function deleteBill(id) {
            if (!confirm('Hapus tagihan ini? Data tidak bisa dikembalikan.')) return;
            fetch(`{{ url('/admin/bill-delete') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                closeModal();
                document.getElementById('branch-select').dispatchEvent(new Event('change'));
            });
        }

        function sendWAReminder(e, id) {
            const btn = e.currentTarget;
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<svg class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            btn.disabled = true;

            fetch(`{{ url('/admin/bill-reminder') }}/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            })
            .catch(() => {
                alert('Gagal mengirim WA. Periksa server bot Anda.');
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            });
        }

        // Sidebar Toggle Script
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const overlay = document.getElementById('menuOverlay');
            
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('left-[-260px]');
            overlay.classList.toggle('opacity-0');
            overlay.classList.toggle('pointer-events-none');
        });
    </script>
</body>
</html>