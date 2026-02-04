<html>
    <head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .transition-all-300 { transition: all 0.3s ease; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<nav class="fixed top-0 w-full bg-white px-6 py-4 flex items-center justify-between shadow-sm z-[1001]">

    <body>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    const menuOverlay = document.getElementById("menuOverlay");

    if(sidebarToggle) {
        sidebarToggle.onclick = () => {
            const isOpen = sidebar.style.left === "0px";
            sidebar.style.left = isOpen ? "-260px" : "0px";
            if (window.innerWidth > 768 && mainContent) {
                mainContent.style.marginLeft = isOpen ? "0" : "260px";
            }
            menuOverlay.classList.toggle("opacity-0");
            menuOverlay.classList.toggle("pointer-events-none");
        };
    }
});
</script>
</body>