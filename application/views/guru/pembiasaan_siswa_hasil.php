<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Absen Pembiasaan Siswa - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= base_url('assets/sw/sweetalert2.min.css') ?>">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
        if (localStorage.getItem('theme') === 'dark' || 
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen pb-28 transition-colors duration-200">

    <!-- Sidebar Drawer Menu -->
    <?php $this->load->view('guru/sidebar'); ?>

    <!-- Top Sticky Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 py-4 shadow-lg sticky top-0 z-50 flex items-center justify-between text-white max-w-xl mx-auto rounded-b-3xl">
        <div class="flex items-center space-x-3">
            <button onclick="openMenu()" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-lg font-bold tracking-wide">Hasil Pembiasaan Siswa</h1>
        </div>
        
        <div class="flex items-center space-x-1">
            <!-- Theme Toggle -->
            <button id="themeToggle" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition">
                <i id="themeIcon" class="fas fa-moon"></i>
            </button>
            
            <!-- Logout Trigger -->
            <button onclick="confirmLogout(event)" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition" title="Keluar Aplikasi">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>
 
    <!-- Main Container -->
    <div class="px-4 py-6 max-w-xl mx-auto space-y-6">
        
        <!-- Button Lakukan Absensi Baru -->
        <a href="<?= site_url('qrcode/pembiasaan_siswa_scan') ?>" 
           class="w-full flex items-center justify-center gap-2 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm rounded-2xl transition shadow-lg hover:shadow-xl hover:scale-[1.01] duration-300">
            <i class="fas fa-qrcode text-lg"></i>
            <span>Lakukan Absensi Baru (Scan Kartu)</span>
        </a>
        
        <!-- Filter Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-md border border-slate-200/60 dark:border-slate-700/60 space-y-4">
            <h3 class="font-extrabold text-sm text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <i class="fas fa-filter mr-1 text-primary-500"></i> Filter Rentang Tanggal
            </h3>
            
            <form onsubmit="event.preventDefault(); loadHasil();" class="grid grid-cols-2 gap-3 items-end">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-1">Dari</label>
                    <input type="date" id="tgl_dari" value="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-755 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-1">Sampai</label>
                    <input type="date" id="tgl_sampai" value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-755 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="col-span-2">
                    <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-extrabold text-sm rounded-xl transition shadow-md">
                        Tampilkan Hasil
                    </button>
                </div>
            </form>
        </div>

        <!-- Hasil List -->
        <div class="space-y-3" id="hasil-container">
            <div class="text-center py-12 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-xs font-bold">Memuat hasil absensi...</p>
            </div>
        </div>

    </div>

    <!-- Floating Actions & Footer Menu -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-800/80 px-6 py-4 flex items-center justify-around shadow-2xl max-w-xl mx-auto rounded-t-3xl">
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-bold tracking-wide">Beranda</span>
        </a>
        
        <div class="relative -mt-8">
            <a href="<?= site_url('qrcode/pembiasaan_siswa_scan') ?>" class="w-16 h-16 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-800 scale-105">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-primary-600 dark:text-primary-400 whitespace-nowrap">Scan Siswa</span>
        </div>

        <button onclick="openMenu()" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-bold tracking-wide">Menu</span>
        </button>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>
    <script>
        const container = document.getElementById("hasil-container");

        function loadHasil() {
            const dari = document.getElementById("tgl_dari").value;
            const sampai = document.getElementById("tgl_sampai").value;

            container.innerHTML = `
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-xs font-bold">Memuat hasil absensi...</p>
                </div>
            `;

            fetch("<?= site_url('qrcode/ajaxHasilPembiasaanSiswa') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `dari=${dari}&sampai=${sampai}`
            })
            .then(res => res.json())
            .then(res => {
                renderHasil(res.data);
            })
            .catch(err => {
                container.innerHTML = `
                    <div class="bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-455 text-center p-6 rounded-2xl border border-rose-200 dark:border-rose-900/50">
                        <i class="fas fa-exclamation-triangle text-xl mb-1"></i>
                        <p class="text-xs font-extrabold">Gagal memuat data dari server.</p>
                    </div>
                `;
            });
        }

        function renderHasil(data) {
            container.innerHTML = '';

            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-center p-8 rounded-3xl border border-slate-200/60 dark:border-slate-700/60">
                        <i class="fas fa-history text-2xl mb-2 text-slate-350"></i>
                        <p class="text-xs font-bold">Tidak ada data absensi untuk rentang tanggal terpilih.</p>
                    </div>
                `;
                return;
            }

            data.forEach(row => {
                const dateStr = formatDateIndo(row.tanggal);
                const detailUrl = `<?= site_url('qrcode/pembiasaan_siswa_hasil_detail') ?>/${row.tanggal}`;
                const downloadUrl = `<?= site_url('qrcode/download_pembiasaan_siswa_screen') ?>/${row.tanggal}`;

                container.innerHTML += `
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between hover:shadow-md transition duration-200">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">${dateStr}</h4>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-750 dark:bg-primary-950/40 dark:text-primary-300">
                                    ${row.total_siswa} Siswa Hadir
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="${downloadUrl}" 
                               title="Download Laporan Screen"
                               class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-emerald-600 dark:hover:bg-emerald-600 hover:text-white flex items-center justify-center transition duration-200 text-slate-600 dark:text-slate-350">
                                <i class="fas fa-download text-xs"></i>
                            </a>
                            <a href="${detailUrl}" 
                               title="Detail Absensi"
                               class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-primary-600 dark:hover:bg-primary-600 hover:text-white flex items-center justify-center transition duration-200 text-slate-600 dark:text-slate-350">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                `;
            });
        }

        function formatDateIndo(dateStr) {
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return parts[2] + ' ' + months[parseInt(parts[1])] + ' ' + parts[0];
        }

        // Theme switching logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function applyTheme() {
            const isDark = localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeIcon.className = 'fa-regular fa-sun text-lg';
            } else {
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'fa-regular fa-moon text-lg';
            }
        }

        themeToggle.addEventListener('click', () => {
            const isCurrentlyDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isCurrentlyDark ? 'light' : 'dark');
            applyTheme();
        });

        applyTheme();

        // Confirm logout using SweetAlert2
        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem absensi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url("auth/logout") ?>';
                }
            });
        }

        // Sidebar Drawer toggle
        const sidebarDrawer = document.getElementById('sidebarDrawer');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeDrawer = document.getElementById('closeDrawer');

        function openMenu() {
            sidebarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sidebarOverlay.classList.add('opacity-100');
                sidebarDrawer.classList.remove('-translate-x-full');
            }, 50);
        }

        function closeMenu() {
            sidebarOverlay.classList.remove('opacity-100');
            sidebarDrawer.classList.add('-translate-x-full');
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        }

        sidebarOverlay.addEventListener('click', closeMenu);
        closeDrawer.addEventListener('click', closeMenu);

        // Load on load
        window.addEventListener("load", loadHasil);
    </script>
</body>
</html>
