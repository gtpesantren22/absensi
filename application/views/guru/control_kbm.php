<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol KBM Siswa - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap">
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
        // Apply theme immediately to avoid white flash
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

    <!-- Sidebar Drawer -->
    <?php $this->load->view('guru/sidebar'); ?>

    <!-- Top Sticky Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 py-4 shadow-lg sticky top-0 z-50 flex items-center justify-between text-white">
        <div class="flex items-center space-x-3">
            <a href="<?= base_url() ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Kontrol KBM Siswa</h1>
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
    <div class="px-4 py-5 space-y-6">
        
        <!-- Header Info Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-slate-900 flex items-center justify-center text-primary-600 dark:text-primary-400 shrink-0">
                <i class="fas fa-calendar-day text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Hari & Tanggal</span>
                <span class="text-base font-extrabold text-slate-850 dark:text-slate-200">
                    <?= tanggal_indo(date('Y-m-d'), true) ?>
                </span>
            </div>
        </div>

        <!-- Matrix/List Card Grouped by Class -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-slate-700/50">
            <?php if (empty($kelas) || $kelas->num_rows() === 0): ?>
                <div class="py-12 px-4 text-center text-slate-400 dark:text-slate-500">
                    <i class="fas fa-calendar-times text-3xl mb-3 text-slate-400 dark:text-slate-500 animate-pulse"></i>
                    <p class="text-sm font-bold">Tidak ada jadwal KBM hari ini.</p>
                </div>
            <?php else: ?>
                <?php
                foreach ($kelas->result() as $kelas_item) :
                    $days = $harini;
                    $dtlkelas = $this->db->query("SELECT * FROM kelas WHERE id_kelas = '$kelas_item->id_kelas'")->row();
                    $jadwalKelas = $this->db->query("SELECT jadwal.*, guru.nama as nama_guru FROM jadwal LEFT JOIN guru ON jadwal.id_guru=guru.id_guru WHERE jadwal.hari = '$days' AND jadwal.id_kelas = '$kelas_item->id_kelas' AND jadwal.id_lembaga = '$id_lembaga' ORDER BY jadwal.jam_dari ASC ");
                ?>
                    <!-- Group Section -->
                    <div class="p-5 space-y-4">
                        <!-- Header Kelas -->
                        <div class="flex items-center justify-between">
                            <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-500"></span>
                                Kelas <?= $dtlkelas ? $dtlkelas->nama : $kelas_item->id_kelas ?>
                            </h4>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-50 text-slate-500 dark:bg-slate-900 dark:text-slate-400 uppercase tracking-widest border border-slate-100 dark:border-slate-800/80">
                                <?= $jadwalKelas->num_rows() ?> Sesi
                            </span>
                        </div>

                        <!-- Jadwal List under this Kelas -->
                        <div class="space-y-3">
                            <?php 
                            foreach ($jadwalKelas->result() as $hasil) :
                                $nmmapel = $this->db->query("SELECT * FROM mapel WHERE id_mapel = $hasil->id_mapel")->row();
                                $queryCek = $this->db->query("
                                    SELECT *
                                    FROM harian
                                    WHERE tanggal = '$dateDays'
                                      AND id_kelas = '$hasil->id_kelas'
                                      AND id_guru = '$hasil->id_guru'
                                      AND dari = '$hasil->jam_dari'
                                      AND id_lembaga = '$hasil->id_lembaga'
                                ");
                                $sudah_absen = $queryCek->num_rows() > 0;
                            ?>
                                <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl flex items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 font-mono">
                                                Jam: <?= $hasil->jam_dari ?> - <?= $hasil->jam_sampai ?>
                                            </span>
                                        </div>
                                        <div class="font-extrabold text-sm text-slate-800 dark:text-white leading-tight">
                                            <?= $hasil->nama_guru ?>
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                            <?= $nmmapel ? $nmmapel->nama : 'Mata Pelajaran' ?>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <?php if ($sudah_absen): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 gap-1 shadow-sm">
                                                <i class="fas fa-check-circle"></i> Sudah
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-450 gap-1 shadow-sm">
                                                <i class="fas fa-times-circle"></i> Belum
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- Bottom Navigation Bar (Restored 3-Menu Layout with Drawer Trigger) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700/80 shadow-2xl px-6 py-3 flex items-center justify-around">
        <!-- Home Button -->
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Beranda</span>
        </a>

        <!-- Floating QR Action Button -->
        <div class="relative -mt-8">
            <?php 
            $ci =& get_instance();
            $today = date('Y-m-d');
            $user_id = $ci->session->userdata('id_user');
            $userData = $ci->db->query("SELECT * FROM user WHERE id_user = '$user_id'")->row();
            $hadir_today = null;
            if ($userData) {
                $hadir_today = $ci->db->query("SELECT * FROM kehadiran_guru WHERE id_guru = '$userData->id_guru' AND tanggal = '$today'")->row();
            }
            $scan_url = base_url('qrcode/scan/masuk');
            if ($hadir_today && $hadir_today->pulang === null) {
                $scan_url = base_url('qrcode/scan/pulang');
            }
            ?>
            <a href="<?= $scan_url ?>" 
               class="w-16 h-16 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-800 hover:scale-105 active:scale-95 transition-transform duration-200"
               title="Scan QR Absensi">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-slate-700 dark:text-slate-400 whitespace-nowrap">Scan QR</span>
        </div>

        <!-- Menu Button (Trigger Sidebar Drawer) -->
        <button id="menuBottomToggle" onclick="openMenu()" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Menu</span>
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
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url("auth/logout") ?>';
                }
            });
        }

        // Sidebar Drawer toggle logic
        const sidebarDrawer = document.getElementById('sidebarDrawer');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeDrawer = document.getElementById('closeDrawer');
        const menuToggle = document.getElementById('menuToggle');

        function openMenu() {
            sidebarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sidebarOverlay.classList.add('opacity-100');
                sidebarDrawer.classList.remove('-translate-x-full');
            }, 50);
        }

        function closeMenu() {
            sidebarDrawer.classList.add('-translate-x-full');
            sidebarOverlay.classList.remove('opacity-100');
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', openMenu);
        }
        const menuBottomToggle = document.getElementById('menuBottomToggle');
        if (menuBottomToggle) {
            menuBottomToggle.addEventListener('click', openMenu);
        }
        closeDrawer.addEventListener('click', closeMenu);
        sidebarOverlay.addEventListener('click', closeMenu);
    </script>
</body>
</html>
