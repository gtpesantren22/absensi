<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Absensi KBM - Portal Guru</title>
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
            <h1 class="text-lg font-bold tracking-wide">Hasil Absensi</h1>
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
        
        <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-200 flex items-center mb-2">
            <i class="fas fa-history mr-2 text-primary-500"></i>Status Mengajar Hari Ini
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
            <div class="space-y-6">
                <?php 
                $days = date('l');
                $dateDays = date('Y-m-d');
                foreach ($lmb as $lmb_item):
                    $jadwalKelas = $this->db->query("SELECT * FROM jadwal WHERE hari = '$days' AND id_guru = '$idguru' AND id_lembaga = '$lmb_item->id_lembaga' ORDER BY jam_dari ASC ");
                ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-md border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                        <!-- Institution Header -->
                        <div class="bg-slate-100 dark:bg-slate-800/90 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
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
                                $cek_row = $queryCek->row();
                            ?>
                                <div class="p-5">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-lg font-extrabold text-slate-900 dark:text-white">
                                                <?= $dtl->id_kelas ?>
                                            </div>
                                            <div class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">
                                                <?= $dtl->id_mapel ?>
                                            </div>
                                            <div class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1 bg-slate-100 dark:bg-slate-900 px-2 py-0.5 rounded inline-block">
                                                Jam: <?= $hasil->jam_dari ?> - <?= $hasil->jam_sampai ?>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col items-end space-y-3">
                                            <?php if ($cek_row): ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                    <i class="fas fa-check-circle mr-1"></i> Sudah Absen
                                                </span>
                                                
                                                <!-- Action Buttons (Large Touch Target) -->
                                                <div class="flex items-center gap-2">
                                                    <a href="<?= base_url('kbm/edit/' . $cek_row->kode) ?>"
                                                       class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/60 dark:text-blue-200 dark:hover:bg-blue-800 transition"
                                                       title="Edit Absensi">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <button onclick="confirmDelete(event, '<?= base_url('kbm/hapus_hasil/' . $cek_row->kode) ?>')"
                                                            class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/60 dark:text-rose-200 dark:hover:bg-rose-800 transition"
                                                            title="Hapus Absensi">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-105 text-rose-800 dark:bg-rose-950 dark:text-rose-300 bg-rose-100">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> Belum Absen
                                                </span>
                                                
                                                <a href="<?= site_url('kbm/absensi') ?>"
                                                   class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow transition">
                                                    Mulai Absen
                                                </a>
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
        // Confirm Delete using SweetAlert2
        function confirmDelete(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: 'Hapus Absensi?',
                text: "Absensi siswa untuk jam kelas ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
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
        const menuToggle = document.getElementById('menuToggle');
        const closeDrawer = document.getElementById('closeDrawer');

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
