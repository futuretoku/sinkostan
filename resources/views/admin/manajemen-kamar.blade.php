<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar – Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .transition-all-300 { transition: all 0.3s ease; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .preview-container img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; }
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">

    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    <nav class="fixed top-0 w-full bg-white px-6 py-4 flex items-center justify-between shadow-sm z-[1001]">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="flex flex-col gap-1.5 cursor-pointer p-2 hover:bg-gray-100 rounded-lg">
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
            </button>
            <div class="flex items-center gap-2 font-extrabold text-lg text-indigo-600">
                <span class="bg-indigo-600 text-white w-9 h-9 rounded-full flex items-center justify-center">SKA</span>
                <span class="hidden md:block text-gray-700">Sin Kost An</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-600 hidden md:block">Admin Owner</span>
            <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center font-bold">A</div>
        </div>
    </nav>

    <div class="flex pt-20">
        <aside id="sidebar" class="fixed left-[-260px] top-20 bottom-0 w-[260px] bg-white border-r border-gray-200 p-6 z-[1000] transition-all-300 overflow-y-auto no-scrollbar">
            <h2 class="text-indigo-600 font-extrabold text-lg mb-8 uppercase tracking-wider">Menu Utama</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Dashboard</a>
                
                <div class="group">
                    <button class="dropdown-toggle w-full flex items-center justify-between px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                        <span>Manajemen</span>
                        <span class="arrow transition-transform duration-200 text-xs rotate-180">▼</span>
                    </button>
                    <div class="dropdown-menu pl-4 mt-2 space-y-1">
                        <a href="{{ route('admin.branches.index') }}" class="block px-4 py-2 text-sm text-indigo-600 font-bold">Cabang & Kamar</a>
                        <a href="{{ route('admin.tenants.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Penyewa</a>
                        <a href="{{ route('admin.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Tagihan</a>
                    </div>
                </div>

                <a href="{{ route('admin.maintenance.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Maintenance</a>
                <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Notifikasi</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Laporan</a>
            </nav>
        </aside>

        <main id="mainContent" class="flex-1 p-6 md:p-10 transition-all-300">
            
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <form action="{{ route('admin.branches.index') }}" method="GET" id="filterBranch">
                    <select name="kost_id" onchange="document.getElementById('filterBranch').submit()" 
                            class="bg-white border border-gray-200 p-3 rounded-xl font-bold text-sm outline-none shadow-sm cursor-pointer hover:border-indigo-300 transition-all">
                        <option value="">Semua Cabang (Filter)</option>
                        @foreach($kosts as $k)
                            <option value="{{ $k->id }}" {{ ($selectedKostId ?? '') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button onclick="openAddKostModal()" class="text-indigo-600 font-bold text-sm hover:underline">+ Tambah Cabang Baru</button>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold">Manajemen Cabang & Kamar</h1>
                    <p class="text-gray-500 text-sm">Kelola seluruh unit kamar dan lokasi kost Anda.</p>
                </div>

                <button onclick="openAddRoomModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-indigo-100">
                    <span class="text-xl">+</span> Tambah Kamar Baru
                </button>
            </div>

            <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100 min-h-[400px]">
                <h3 class="font-bold text-lg mb-6 text-gray-700">Daftar Unit Kamar</h3>
                
                @if(isset($rooms) && $rooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($rooms as $room)
                    <div class="border border-gray-100 rounded-[24px] p-5 hover:shadow-md transition-all bg-white">
                        <div class="relative w-full h-44 mb-4 overflow-hidden rounded-2xl bg-gray-100">
                            @if($room->image)
                                @php $images = explode(', ', $room->image); @endphp
                                <img src="{{ asset('uploads/rooms/' . $images[0]) }}" class="w-full h-full object-cover" alt="Kamar {{ $room->room_number }}">
                                @if(count($images) > 1)
                                    <span class="absolute bottom-2 right-2 bg-black/60 text-white text-[10px] px-2 py-1 rounded-lg font-bold">+{{ count($images)-1 }} Foto</span>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs italic">Belum ada foto</div>
                            @endif
                        </div>

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">{{ $room->kost_name ?? 'Kost' }}</span>
                                <h4 class="text-xl font-extrabold">{{ $room->room_number }}</h4>
                            </div>
                            
                            {{-- LOGIKA TAMPILAN STATUS SINKRON DENGAN DATABASE --}}
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase 
                                @if($room->status == 'available') bg-green-100 text-green-600
                                @elseif($room->status == 'booked') bg-orange-100 text-orange-600
                                @elseif($room->status == 'maintenance') bg-gray-100 text-gray-500
                                @else bg-red-100 text-red-600 @endif">
                                
                                @if($room->status == 'available') Tersedia
                                @elseif($room->status == 'booked') Dibooking
                                @elseif($room->status == 'maintenance') Maintenance
                                @else Terisi @endif
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-[11px] bg-gray-100 px-2 py-1 rounded-lg text-gray-600 font-semibold">{{ $room->type }}</span>
                            @if($room->facilities)
                                @foreach(explode(',', $room->facilities) as $f)
                                <span class="text-[11px] bg-indigo-50 px-2 py-1 rounded-lg text-indigo-600 font-semibold">{{ trim($f) }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-50 pt-4">
                            <span class="font-bold text-indigo-600 text-sm">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                            <div class="flex gap-2">
                                <button class="text-gray-400 hover:text-indigo-600 transition text-sm font-bold">Edit</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-20">
                    <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-300 text-2xl font-bold">!</div>
                    <p class="text-gray-400 italic">Belum ada data unit kamar untuk cabang ini.</p>
                </div>
                @endif
            </div>
        </main>
    </div>

    {{-- MODAL TAMBAH CABANG --}}
    <div id="addKostModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-[2001]">
        <div class="bg-white w-full max-w-2xl rounded-[32px] p-8 shadow-2xl max-h-[95vh] overflow-y-auto no-scrollbar">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-slate-700">Tambah Cabang Baru</h3>
                    <p class="text-xs text-gray-500">Masukkan detail lokasi dan foto area kost.</p>
                </div>
                <button onclick="closeAddKostModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>

            <form action="{{ route('admin.branches.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Nama Cabang Kost</label>
                        <input type="text" name="name" placeholder="Contoh: Sin Kost Merdeka" required 
                               class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-gray-50">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Link Google Maps</label>
                        <input type="url" name="location_link" placeholder="http://maps.google.com/..." 
                               class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-gray-50">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Alamat Lengkap</label>
                    <textarea name="address" rows="3" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-gray-50"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase mb-2">Harga Min (Rp)</label>
                        <input type="number" name="price_min" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none" placeholder="1500000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase mb-2">Harga Max (Rp)</label>
                        <input type="number" name="price_max" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none" placeholder="2500000">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Foto Area Cabang</label>
                    <div class="group border-2 border-dashed border-gray-200 rounded-3xl p-8 bg-gray-50 text-center relative hover:border-indigo-400 cursor-pointer">
                        <input type="file" name="kost_images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewKostImages(event)">
                        <div id="kostPlaceholder">
                            <span class="text-gray-600 font-bold block">Klik untuk Upload Foto Cabang</span>
                        </div>
                        <div id="kostPreviewContainer" class="flex flex-wrap justify-center gap-3 mt-2 hidden"></div>
                    </div>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeAddKostModal()" class="flex-1 py-3 text-gray-500 font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200">Simpan Cabang</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH UNIT KAMAR --}}
    <div id="addRoomModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-[2000]">
        <div class="bg-white w-full max-w-2xl rounded-[32px] p-8 shadow-2xl max-h-[95vh] overflow-y-auto no-scrollbar">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-slate-700">Tambah Unit Kamar</h3>
                <button onclick="closeAddRoomModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>
            
            <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Pilih Cabang</label>
                        <select name="kost_id" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-gray-50">
                            @foreach($kosts as $k)
                                <option value="{{ $k->id }}">{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Nomor Kamar</label>
                        <input type="text" name="room_number" placeholder="Contoh: 101" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Lantai</label>
                        <input type="number" name="floor" placeholder="1" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold text-sm">Harga Per Bulan (Rp)</label>
                        <input type="number" name="price" placeholder="1500000" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none">
                    </div>
                </div>

                {{-- STATUS KAMAR SINKRON DENGAN DATABASE ENUM --}}
                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Status Kamar</label>
                    <select name="status" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white font-bold">
                        <option value="available">Tersedia (Status Hijau)</option>
                        <option value="booked">Dibooking (Status Oranye)</option>
                        <option value="occupied">Terisi (Status Merah)</option>
                        <option value="maintenance">Maintenance (Status Abu-abu)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Tipe Kamar</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="Standard" class="peer hidden" checked onchange="updateFacilities('Standard')">
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center">
                                <span class="block font-bold">Standard</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="Elite" class="peer hidden" onchange="updateFacilities('Elite')">
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center">
                                <span class="block font-bold">Elite</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-3 p-5 bg-gray-50 rounded-2xl">
                    <label class="block text-slate-700 font-bold text-sm">Fasilitas</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="facilities[]" value="Wifi" checked> Wifi</label>
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="facilities[]" value="Kasur" checked> Kasur</label>
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="facilities[]" value="Lemari" checked> Lemari</label>
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" id="fac-ac" name="facilities[]" value="AC"> AC</label>
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" id="fac-km" name="facilities[]" value="KM Dalam"> KM Dalam</label>
                        <label class="flex items-center gap-3 text-sm"><input type="checkbox" id="fac-tv" name="facilities[]" value="Smart TV"> Smart TV</label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Foto Kamar (Multiple)</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 bg-gray-50 text-center relative hover:border-indigo-400">
                        <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImages(event)">
                        <div id="imagePlaceholder">
                            <span class="text-indigo-600 font-bold">Klik untuk upload foto</span>
                        </div>
                        <div id="imagePreviewContainer" class="flex flex-wrap gap-3 mt-4 hidden"></div>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeAddRoomModal()" class="flex-1 py-3 text-gray-500 font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // SIDEBAR LOGIC
        document.addEventListener('DOMContentLoaded', () => {
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const menuOverlay = document.getElementById("menuOverlay");

            sidebarToggle.onclick = () => {
                const isOpen = sidebar.style.left === "0px";
                sidebar.style.left = isOpen ? "-260px" : "0px";
                if (window.innerWidth > 768) {
                    mainContent.style.marginLeft = isOpen ? "0" : "260px";
                }
                menuOverlay.classList.toggle("opacity-0");
                menuOverlay.classList.toggle("opacity-100");
                menuOverlay.classList.toggle("pointer-events-none");
            };

            document.querySelectorAll('.dropdown-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.nextElementSibling.classList.toggle('hidden');
                    btn.querySelector('.arrow').classList.toggle('rotate-180');
                });
            });
        });

        function openAddRoomModal() { document.getElementById('addRoomModal').classList.remove('hidden'); }
        function closeAddRoomModal() { document.getElementById('addRoomModal').classList.add('hidden'); }
        function openAddKostModal() { document.getElementById('addKostModal').classList.remove('hidden'); }
        function closeAddKostModal() { document.getElementById('addKostModal').classList.add('hidden'); }

        function previewImages(event) {
            const container = document.getElementById('imagePreviewContainer');
            const placeholder = document.getElementById('imagePlaceholder');
            container.innerHTML = '';
            if (event.target.files.length > 0) {
                container.classList.remove('hidden');
                placeholder.classList.add('hidden');
                Array.from(event.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-20 h-20 object-cover rounded-xl border-2 border-white shadow-sm';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function updateFacilities(type) {
            const ac = document.getElementById('fac-ac');
            const km = document.getElementById('fac-km');
            const tv = document.getElementById('fac-tv');
            ac.checked = km.checked = tv.checked = (type === 'Elite');
        }

        function previewKostImages(event) {
            const container = document.getElementById('kostPreviewContainer');
            const placeholder = document.getElementById('kostPlaceholder');
            container.innerHTML = '';
            if (event.target.files.length > 0) {
                container.classList.remove('hidden');
                placeholder.classList.add('hidden');
                Array.from(event.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'w-20 h-20';
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl shadow-md">`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>
</html>