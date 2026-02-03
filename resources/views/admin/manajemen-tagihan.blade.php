<x-app-layout>
    <div class="min-h-screen bg-[#F2F4F7] p-8">
        <div class="flex justify-between items-center mb-8">
            <a href="{{ route('admin.dashboard') }}" class="bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9]">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-[#344054]">Manajemen Tagihan</h1>
            </div>

            <div class="mb-10">
                <select id="branch-select" class="w-full appearance-none bg-white border border-gray-200 rounded-2xl py-4 px-6 text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                    <option value="">Pilih Cabang Kost</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-gray-400 text-sm">
                            <th class="pb-4 px-6 font-medium">Penyewa</th>
                            <th class="pb-4 px-6 font-medium">Kamar</th>
                            <th class="pb-4 px-6 font-medium">Bulan</th>
                            <th class="pb-4 px-6 font-medium">Total</th>
                            <th class="pb-4 px-6 font-medium">Status</th>
                            <th class="pb-4 px-6 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bill-table-body">
                        <tr>
                            <td colspan="6" class="py-20 text-center text-gray-400 italic">Silahkan pilih cabang terlebih dahulu...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('branch-select').addEventListener('change', function() {
        const branchId = this.value;
        const body = document.getElementById('bill-table-body');

        if (!branchId) return;

        body.innerHTML = '<tr><td colspan="6" class="py-20 text-center text-purple-600 font-medium">Memuat data...</td></tr>';

        fetch(`{{ url('/admin/get-bills') }}/${branchId}`)
            .then(res => res.json())
            .then(data => {
                body.innerHTML = '';
                if (data.length === 0) {
                    body.innerHTML = '<tr><td colspan="6" class="py-20 text-center text-gray-400">Tidak ada data tagihan.</td></tr>';
                    return;
                }

                data.forEach(bill => {
                    const statusText = bill.status === 'paid' ? 'Sudah Bayar' : (bill.status === 'unpaid' ? 'Belum Bayar' : 'Terlambat');
                    const statusClass = bill.status === 'paid' ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF3F2] text-[#B42318]';

                    body.innerHTML += `
                        <tr class="bg-[#F9FAFB] rounded-2xl overflow-hidden shadow-sm border border-gray-50">
                            <td class="py-5 px-6 font-medium text-gray-700 rounded-l-2xl">${bill.tenant_name}</td>
                            <td class="py-5 px-6 text-gray-600 text-center">${bill.room_number}</td>
                            <td class="py-5 px-6 text-gray-600">${bill.month}</td>
                            <td class="py-5 px-6 text-gray-600 font-semibold text-center">Rp ${bill.total}</td>
                            <td class="py-5 px-6">
                                <span class="px-4 py-1.5 rounded-full text-xs font-bold ${statusClass}">${statusText}</span>
                            </td>
                            <td class="py-5 px-6 text-center rounded-r-2xl">
                                <div class="flex justify-center gap-2">
                                    <button title="Edit" class="p-2 text-orange-500 bg-white border border-gray-100 rounded-lg hover:bg-orange-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button title="Lihat Bukti" class="p-2 text-blue-500 bg-white border border-gray-100 rounded-lg hover:bg-blue-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </button>
                                    <button title="Konfirmasi" class="p-2 text-green-500 bg-white border border-gray-100 rounded-lg hover:bg-green-50 transition-colors text-center">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button title="Hapus" class="p-2 text-red-500 bg-white border border-gray-100 rounded-lg hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            });
    });
    </script>
</x-app-layout>