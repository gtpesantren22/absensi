<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - <?= htmlspecialchars($app_name) ?></title>
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
            background: rgba(255, 255, 255, 0.9);
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

    <!-- Top Solid Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 pt-6 pb-24 rounded-b-[2rem] shadow-lg relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 text-white">
                <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                    <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="w-10 h-10 rounded-lg object-contain">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-school text-lg"></i>
                    </div>
                <?php endif; ?>
                <h1 class="text-xl font-bold tracking-wide"><?= htmlspecialchars($app_name) ?></h1>
            </div>
            
            <div class="flex items-center space-x-1.5">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="p-2 text-white/90 hover:text-white hover:scale-110 active:scale-95 transition-all duration-200" title="Ubah Tema">
                    <i id="themeIcon" class="fa-regular fa-moon text-lg"></i>
                </button>
                
                <!-- Logout Trigger -->
                <button onclick="confirmLogout(event)" class="p-2 text-white/90 hover:text-white hover:scale-110 active:scale-95 transition-all duration-200" title="Keluar Aplikasi">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="px-4 -mt-16 space-y-6">
        
        <!-- Profile Card (Optimized for Senior Visibility) -->
        <div onclick="window.location.href='<?= base_url('profile') ?>'" class="glass-card rounded-2xl p-5 shadow-xl cursor-pointer hover:scale-[1.01] active:scale-95 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-md shrink-0 border border-slate-200 dark:border-slate-700 bg-primary-600 text-white flex items-center justify-center">
                    <?php if (isset($user) && $user->foto && file_exists('./uploads/profile/' . $user->foto)): ?>
                        <img src="<?= base_url('uploads/profile/' . $user->foto) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-2xl font-extrabold"><?= inisial($guru->nama ?? 'Nama Guru') ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight truncate">
                        <?= $guru->nama ?? 'Nama Guru' ?>
                    </h2>
                    <p class="text-sm text-primary-700 dark:text-primary-300 font-bold tracking-wide uppercase mt-1">
                        <?= $guru->mapel ?? 'Guru Pengajar' ?>
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 truncate">
                        <i class="fas fa-university mr-1 text-slate-400"></i><?= $lembaga->nama ?? '-' ?>
                    </p>
                </div>
                <div class="text-slate-400">
                    <i class="fas fa-chevron-right text-base"></i>
                </div>
            </div>
            
            <div class="border-t border-slate-200 dark:border-slate-700 mt-4 pt-3 flex justify-between items-center text-sm">
                <span class="text-slate-600 dark:text-slate-400 font-medium">
                    Semester Aktif:
                </span>
                <span class="font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                    <?= $this->session->userdata('nama_tahun_aktif') ?> - <?= $this->session->userdata('nama_semester_aktif') ?>
                </span>
            </div>
        </div>

        <!-- Clock & Presence Actions (Large Text & Easy Tapping Targets) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-lg border border-slate-200/60 dark:border-slate-700/60">
            <div class="flex flex-col items-center justify-center text-center pb-4 border-b border-slate-100 dark:border-slate-700/50">
                <div class="text-sm font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-900 px-3 py-1 rounded-full">
                    <?= tanggal_indo(date('d-m-Y'), true) ?>
                </div>
                <!-- Large Digital Clock -->
                <div id="digitalClock" class="text-4xl font-extrabold text-slate-800 dark:text-white mt-3 font-mono tracking-widest">
                    00:00:00
                </div>
            </div>

            <!-- Attendance Check-in / Out Rows -->
            <div class="grid grid-cols-2 gap-4 mt-5">
                <!-- Check-in Datang -->
                <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200/50 dark:border-slate-800 text-center">
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Absen Datang</div>
                    <div class="text-xl font-extrabold text-slate-800 dark:text-white mt-1.5">
                        <?= $hadir ? date('H:i', strtotime($hadir->waktu)) : '--:--' ?>
                    </div>
                    <?php if (!$hadir): ?>
                        <a href="<?= base_url('qrcode/scan/masuk') ?>" 
                           class="inline-block w-full mt-3 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition">
                            <i class="fas fa-qrcode mr-1"></i> Scan Absen
                        </a>
                    <?php else: ?>
                        <span class="inline-block mt-3 py-1 text-xs font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-3 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i> Sudah Absen
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Check-out Pulang -->
                <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200/50 dark:border-slate-800 text-center">
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Absen Pulang</div>
                    <div class="text-xl font-extrabold text-slate-800 dark:text-white mt-1.5">
                        <?= ($hadir && $hadir->pulang !== null) ? date('H:i', strtotime($hadir->pulang)) : '--:--' ?>
                    </div>
                    <?php if ($hadir && $hadir->pulang === null): ?>
                        <a href="<?= base_url('qrcode/scan/pulang') ?>" 
                           class="inline-block w-full mt-3 py-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition">
                            <i class="fas fa-qrcode mr-1"></i> Scan Pulang
                        </a>
                    <?php elseif ($hadir && $hadir->pulang !== null): ?>
                        <span class="inline-block mt-3 py-1 text-xs font-extrabold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/30 px-3 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i> Sudah Absen
                        </span>
                    <?php else: ?>
                        <span class="inline-block mt-3 py-1.5 text-xs text-slate-400 dark:text-slate-500 font-bold">
                            Menunggu Masuk
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Schedule Section with Enhanced Visibility -->
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-slate-100 mb-3.5 flex items-center">
                <i class="fas fa-calendar-day mr-2 text-primary-600"></i>Jadwal Mengajar Hari Ini
            </h3>

            <?php if (empty($lmb)): ?>
                <div class="glass-card rounded-2xl p-6 text-center text-slate-600 dark:text-slate-400 shadow-md">
                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-bed text-xl"></i>
                    </div>
                    <p class="text-sm font-extrabold">Tidak ada jadwal KBM hari ini.</p>
                    <p class="text-xs mt-1">Selamat beristirahat!</p>
                </div>
            <?php else: ?>
                <div class="space-y-5">
                    <?php 
                    $days = date('l');
                    $dateDays = date('Y-m-d');
                    $id_semester_aktif = $this->session->userdata('id_semester_aktif');
                    foreach ($lmb as $lmb_item):
                        $jadwalKelas = $this->db->query("SELECT * FROM jadwal WHERE hari = '$days' AND id_guru = '$idguru' AND id_lembaga = '$lmb_item->id_lembaga' AND id_semester = '$id_semester_aktif' ORDER BY jam_dari ASC ");
                    ?>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-md border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                            <!-- Institution Header -->
                            <div class="bg-slate-100 dark:bg-slate-800/90 px-4 py-3.5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                <span class="text-xs font-extrabold tracking-wider text-slate-700 dark:text-slate-300 uppercase">
                                    <?= $lmb_item->nama_lembaga ?>
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                                    <?= $jadwalKelas->num_rows() ?> Kelas
                                </span>
                            </div>

                            <!-- List of classes -->
                            <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                <?php 
                                foreach ($jadwalKelas->result() as $hasil):
                                    $dtl = $this->db->query("SELECT * FROM jadwal_dtl WHERE id_jadwal = '$hasil->id_jadwal' ")->row();
                                    $queryCek = $this->db->query("
                                        SELECT *
                                        FROM harian
                                        WHERE tanggal = '$dateDays'
                                          AND id_kelas = '$hasil->id_kelas'
                                          AND id_guru = '$idguru'
                                          AND dari = '$hasil->jam_dari'
                                          AND id_lembaga = '$lmb_item->id_lembaga'
                                    ");
                                    $is_done = $queryCek->num_rows() > 0;
                                ?>
                                    <!-- Tall clickable tap target for older users -->
                                    <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-700/20 active:bg-slate-100 dark:active:bg-slate-700/30 transition cursor-pointer"
                                         onclick="window.location.href='<?= site_url('kbm/absensi') ?>'">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-lg font-extrabold text-slate-900 dark:text-white">
                                                    <?= $dtl->id_kelas ?>
                                                </div>
                                                <div class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">
                                                    <?= $dtl->id_mapel ?>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col items-end space-y-2">
                                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-900 px-2 py-0.5 rounded font-mono">
                                                    Jam: <?= $hasil->jam_dari ?> - <?= $hasil->jam_sampai ?>
                                                </span>
                                                <?php if ($is_done): ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                        <i class="fas fa-check-circle mr-1"></i> Sudah Absen
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 animate-pulse">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> Mulai Absen
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Navigation Bar (Restored 3-Menu Layout with Drawer Trigger) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700/80 shadow-2xl px-6 py-3 flex items-center justify-around">
        <!-- Home Button -->
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-primary-600 dark:text-primary-400 w-16 font-extrabold">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Beranda</span>
        </a>

        <!-- Floating QR Action Button -->
        <div class="relative -mt-8">
            <?php 
            $scan_url = base_url('qrcode/scan/masuk');
            if (isset($hadir) && $hadir && $hadir->pulang === null) {
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
        // Real-time clock updater
        function updateClock() {
            const clockEl = document.getElementById('digitalClock');
            if (!clockEl) return;
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

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