<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hasil Pembiasaan - Portal Guru</title>
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
            <a href="<?= site_url('qrcode/pembiasaan_siswa_hasil') ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Detail Absensi</h1>
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
        
        <!-- Header Info Card -->
        <div class="bg-gradient-to-br from-primary-600 to-blue-700 text-white rounded-3xl p-5 shadow-lg space-y-4">
            <div class="space-y-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 uppercase tracking-wider">
                    <?= $lembaga_selected ? htmlspecialchars($lembaga_selected->nama) : 'Lembaga Aktif' ?>
                </span>
                <h2 class="text-xl font-black leading-tight"><?= tanggal_indo($date) ?></h2>
                <p class="text-xs text-primary-100 dark:text-slate-350">
                    Menampilkan log kehadiran siswa yang di-scan atau diinput hari ini.
                </p>
            </div>
            
            <a href="<?= site_url('qrcode/download_pembiasaan_siswa_screen/' . $date . ($id_lembaga ? '/' . $id_lembaga : '')) ?>" 
               class="w-full flex items-center justify-center gap-2 py-2.5 bg-white text-primary-600 font-extrabold text-xs rounded-xl hover:bg-slate-100 transition shadow">
                <i class="fas fa-download"></i>
                <span>Download Laporan Screen</span>
            </a>
        </div>

        <!-- Rincian Kehadiran Per Kelas Card (Dynamic Container) -->
        <div id="classBreakdownCard" class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-3 hidden">
            <h3 class="font-extrabold text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <i class="fas fa-chart-pie text-primary-500 mr-1"></i> Rincian Per Kelas
            </h3>
            <div id="classBreakdownList" class="space-y-2 divide-y divide-slate-100 dark:divide-slate-700/50">
                <!-- Appended dynamically -->
            </div>
        </div>

        <!-- Student Attendance List (Simplified Dynamic Table) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50">
                <h3 class="font-extrabold text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <i class="fas fa-list text-primary-500 mr-1"></i> Daftar Kehadiran Siswa
                </h3>
            </div>
            
            <div id="noAttendanceMsg" class="text-center p-8 text-slate-500 dark:text-slate-400 hidden">
                <i class="fas fa-user-slash text-2xl mb-2 text-slate-350"></i>
                <p class="text-xs font-bold">Tidak ada santri yang terabsen pada tanggal ini.</p>
            </div>

            <div id="attendanceTableContainer" class="overflow-x-auto hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-700/50 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider">
                            <th class="py-3 px-3 text-center w-8">No</th>
                            <th class="py-3 px-3">Nama</th>
                            <th class="py-3 px-3 text-center w-16">Masuk</th>
                            <th class="py-3 px-3 text-center w-16">Pulang</th>
                        </tr>
                    </thead>
                    <tbody id="studentListBody" class="divide-y divide-slate-100 dark:divide-slate-700/50 text-slate-700 dark:text-slate-300">
                        <!-- Appended dynamically -->
                    </tbody>
                </table>
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
        // Theme switching logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function applyTheme() {
            const isDark = localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                if (themeIcon) themeIcon.className = 'fa-regular fa-sun text-lg';
            } else {
                document.documentElement.classList.remove('dark');
                if (themeIcon) themeIcon.className = 'fa-regular fa-moon text-lg';
            }
        }

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isCurrentlyDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isCurrentlyDark ? 'light' : 'dark');
                applyTheme();
            });
        }

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
            if (sidebarOverlay && sidebarDrawer) {
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebarOverlay.classList.add('opacity-100');
                    sidebarDrawer.classList.remove('-translate-x-full');
                }, 50);
            }
        }

        function closeMenu() {
            if (sidebarOverlay && sidebarDrawer) {
                sidebarOverlay.classList.remove('opacity-100');
                sidebarDrawer.classList.add('-translate-x-full');
                setTimeout(() => {
                    sidebarOverlay.classList.add('hidden');
                }, 300);
            }
        }

        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMenu);
        if (closeDrawer) closeDrawer.addEventListener('click', closeMenu);

        // Fetch attendance details and update UI dynamically
        function fetchDetailData() {
            fetch("<?= site_url('qrcode/ajaxGetPembiasaanSiswaDetail/' . $date) ?>")
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        // 1. Render class breakdown
                        const card = document.getElementById("classBreakdownCard");
                        const list = document.getElementById("classBreakdownList");
                        if (res.data_kelas && res.data_kelas.length > 0) {
                            let listHtml = "";
                            res.data_kelas.forEach(cls => {
                                listHtml += `
                                    <div class="flex items-center justify-between pt-2 first:pt-0">
                                        <span class="text-xs font-extrabold text-slate-800 dark:text-white">${cls.nama_kelas}</span>
                                        <div class="flex items-center space-x-2 text-[11px] font-bold">
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400">
                                                Hadir: ${cls.hadir}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-455">
                                                Belum: ${cls.belum_hadir}
                                            </span>
                                        </div>
                                    </div>
                                `;
                            });
                            list.innerHTML = listHtml;
                            card.classList.remove("hidden");
                        } else {
                            card.classList.add("hidden");
                        }

                        // 2. Render student list table
                        const tbody = document.getElementById("studentListBody");
                        const noMsg = document.getElementById("noAttendanceMsg");
                        const tableContainer = document.getElementById("attendanceTableContainer");

                        if (!res.list || res.list.length === 0) {
                            noMsg.classList.remove("hidden");
                            tableContainer.classList.add("hidden");
                        } else {
                            noMsg.classList.add("hidden");
                            let tbodyHtml = "";
                            res.list.forEach((row, index) => {
                                tbodyHtml += `
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                        <td class="py-3 px-3 text-center font-bold text-slate-400">${index + 1}</td>
                                        <td class="py-3 px-3 font-extrabold text-slate-900 dark:text-white">${row.nama}</td>
                                        <td class="py-3 px-3 text-center font-mono font-extrabold text-emerald-600 dark:text-emerald-400">${row.jam_masuk}</td>
                                        <td class="py-3 px-3 text-center font-mono font-extrabold text-blue-600 dark:text-blue-400">${row.jam_pulang}</td>
                                    </tr>
                                `;
                            });
                            tbody.innerHTML = tbodyHtml;
                            tableContainer.classList.remove("hidden");
                        }
                    }
                })
                .catch(err => console.error("Error fetching detail:", err));
        }

        // Fetch immediately and poll every 2 seconds
        fetchDetailData();
        setInterval(fetchDetailData, 2000);
    </script>
</body>
</html>
