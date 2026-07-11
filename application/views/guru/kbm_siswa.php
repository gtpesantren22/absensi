<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi - Portal Guru</title>
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

        /* Clean and compact table style for KBM */
        #tabelData table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 0.875rem !important;
        }

        #tabelData tr {
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
            display: table-row !important;
        }

        .dark #tabelData tr {
            border-bottom-color: rgba(51, 65, 85, 0.5) !important;
        }

        /* Mapel / Jam Info card overrides */
        #tabelData tr:first-child, #tabelData tr:nth-child(2) {
            display: table-row !important;
            background: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
        }

        .dark #tabelData tr:first-child, .dark #tabelData tr:nth-child(2) {
            background: rgba(30, 41, 59, 0.8) !important;
            color: #93c5fd !important;
        }

        #tabelData tr:first-child td, #tabelData tr:nth-child(2) td {
            color: inherit !important;
            background: transparent !important;
            font-weight: inherit !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
        }

        #tabelData td {
            padding: 0.75rem 0.5rem !important;
            border: none !important;
            background: transparent !important;
            vertical-align: middle !important;
        }

        /* Student name styling */
        #tabelData td:first-child {
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            color: #0f172a !important;
            word-break: break-word !important;
        }

        .dark #tabelData td:first-child {
            color: #f8fafc !important;
        }

        /* Radio containers */
        #tabelData td:last-child {
            width: auto !important;
        }

        #tabelData .flex-wrap {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 0.375rem !important;
            justify-content: flex-end !important;
        }

        /* Large circular buttons for senior teachers (32px target) */
        #tabelData span.inline-flex {
            height: 2rem !important;
            width: 2rem !important;
            font-size: 0.875rem !important;
            font-weight: 800 !important;
            border-radius: 9999px !important;
            border: 1px solid #cbd5e1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease-in-out !important;
        }

        .dark #tabelData span.inline-flex {
            border-color: #475569 !important;
        }

        /* Peer checks */
        #tabelData input[type="radio"]:checked + span {
            transform: scale(1.05);
            border-color: transparent !important;
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
            <h1 class="text-lg font-bold tracking-wide">Absensi KBM</h1>
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
        
        <!-- Form Container -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-lg border border-slate-200/60 dark:border-slate-700/60">
            <?= form_open('kbm/save_multiple_data', ['class' => 'space-y-6', 'id' => 'formKbm']) ?>

            <!-- Nama Guru -->
            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                    Nama Guru
                </label>
                <input
                    type="text"
                    name="guru"
                    value="<?= $guru->nama ?? 'Nama Guru' ?>"
                    readonly
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 
                            bg-slate-100 dark:bg-slate-700 
                            text-slate-750 dark:text-slate-200 font-semibold
                            px-4 py-3 focus:outline-none">
            </div>

            <!-- Pilih Kelas (Large Target Dropdown) -->
            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                    Pilih Kelas / Jadwal
                </label>
                <select
                    name="kelas"
                    id="selectKelas"
                    required
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                            bg-white dark:bg-slate-700
                            text-slate-800 dark:text-slate-200 font-bold
                            px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">- pilih kelas -</option>
                    <?php foreach ($kelas as $gr) : ?>
                        <option value="<?= $gr['id_jadwal'] ?>" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                            <?= $gr['kelas'] . ' (Jam ke: ' . $gr['jam_dari'] . '-' . $gr['jam_sampai'] . ')' ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <!-- Student List Container -->
            <div id="tabelData" class="mt-4 overflow-hidden rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                <!-- Ajax results will print here as compact horizontal rows -->
            </div>

            <!-- Jurnal Materi -->
            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                    Materi yang diajarkan
                </label>
                <textarea
                    name="isi"
                    rows="3"
                    placeholder="Contoh: Mengulas fungsi aljabar linear dan latihan soal."
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                            bg-white dark:bg-slate-700
                            text-slate-800 dark:text-slate-200
                            px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <!-- Submit Button (Large & Safe Target) -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-4 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-extrabold text-base shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save text-lg"></i> Simpan Absensi Kelas
                </button>
            </div>

            <?= form_close() ?>
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
        // Fetch student list when class is selected
        document.getElementById('selectKelas').addEventListener('change', e => {
            if (!e.target.value) {
                document.getElementById('tabelData').innerHTML = '';
                return;
            }
            
            // Show loading placeholder
            document.getElementById('tabelData').innerHTML = `
                <div class="p-6 text-center text-slate-500 bg-white dark:bg-slate-850">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 text-primary-600"></i>
                    <p class="text-sm font-bold">Mengambil data siswa...</p>
                </div>
            `;

            fetch('<?= site_url('kbm/cariKelas') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_jadwal: e.target.value
                })
            })
            .then(res => res.text())
            .then(res => {
                document.getElementById('tabelData').innerHTML = res;
            })
            .catch(err => {
                document.getElementById('tabelData').innerHTML = `
                    <div class="p-6 text-center text-rose-500 font-bold text-sm bg-white dark:bg-slate-850">
                        Gagal mengambil data siswa. Coba lagi.
                    </div>
                `;
            });
        });

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
