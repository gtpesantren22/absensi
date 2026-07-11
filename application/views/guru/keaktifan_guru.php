<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keaktifan Saya - Portal Guru</title>
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
            <button id="menuToggle" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-lg font-bold tracking-wide">Keaktifan Saya</h1>
        </div>

        <div class="flex items-center space-x-1">
            <button id="themeToggle" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition">
                <i id="themeIcon" class="fas fa-moon"></i>
            </button>

            <button onclick="confirmLogout(event)" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition" title="Keluar Aplikasi">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="px-4 py-6 space-y-6">

        <!-- Tabs Selector -->
        <div class="flex bg-slate-100 dark:bg-slate-800/60 p-1.5 rounded-2xl shadow-inner">
            <button onclick="switchTab('attendance')" id="tabBtn_attendance"
                class="w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white dark:bg-slate-700 text-primary-600 dark:text-white shadow-sm">
                <i class="fas fa-calendar-check mr-2"></i>Kehadiran
            </button>
            <button onclick="switchTab('journal')" id="tabBtn_journal"
                class="w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 text-slate-500 dark:text-slate-400">
                <i class="fas fa-book-open mr-2"></i>Jurnal KBM
            </button>
        </div>

        <!-- ATTENDANCE TAB PANEL -->
        <div id="panel_attendance" class="space-y-6">
            <!-- Month Selector Card (Attendance Only) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="flex flex-col space-y-2">
                    <label class="text-sm font-extrabold text-slate-600 dark:text-slate-400">Pilih Bulan Kehadiran</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                        <input type="month" id="reportMonth"
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            value="<?= date('Y-m') ?>">
                    </div>
                </div>
            </div>

            <!-- Summary Statistics Widget -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Hadir %</span>
                    <span id="kpiPresentRate" class="text-xl font-extrabold text-emerald-600 dark:text-emerald-450">-</span>
                </div>
                <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Total Hadir</span>
                    <span id="kpiHadirCount" class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">-</span>
                </div>
                <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Absen/Lain</span>
                    <span id="kpiOtherCount" class="text-xl font-extrabold text-amber-600 dark:text-amber-400">-</span>
                </div>
            </div>

            <!-- List Title -->
            <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-200 flex items-center">
                <i class="fas fa-list-ul mr-2 text-primary-500"></i>Daftar Riwayat Kehadiran
            </h3>

            <!-- Attendance List Table (Tabular format matching Jurnal list) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-xs font-extrabold uppercase text-slate-400">
                                <th class="py-4.5 px-5">Tanggal</th>
                                <th class="py-4.5 px-5">Jam Presensi</th>
                                <th class="py-4.5 px-5 text-center">Status</th>
                                <th class="py-4.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500 font-bold">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- JOURNAL TAB PANEL -->
        <div id="panel_journal" class="space-y-6 hidden">
            <!-- Jurnal KBM Comparison KPI -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex flex-col space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-550 flex items-center justify-center text-xl">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">Prosentase KBM</span>
                            <span id="kpiJournalCount" class="text-base font-extrabold text-slate-800 dark:text-slate-100">-</span>
                        </div>
                    </div>
                    <span id="kpiJournalRate" class="text-xl font-extrabold text-amber-600 dark:text-amber-400">-</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 h-3 rounded-full overflow-hidden">
                    <div id="kpiJournalProgressBar" class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>

            <!-- Interactive Filters -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm grid grid-cols-2 gap-4">
                <div class="flex flex-col space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-500 dark:text-slate-400">Filter Kelas</label>
                    <select id="filterKelas" onchange="renderJournals()"
                        class="py-3 px-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Kelas</option>
                    </select>
                </div>

                <div class="flex flex-col space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-500 dark:text-slate-400">Filter Mapel</label>
                    <select id="filterMapel" onchange="renderJournals()"
                        class="py-3 px-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Mapel</option>
                    </select>
                </div>
            </div>

            <!-- List Title -->
            <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-200 flex items-center">
                <i class="fas fa-book mr-2 text-primary-500"></i>Daftar Jurnal Pembelajaran
            </h3>

            <!-- Journal List Table (Simplified Table list layout) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-xs font-extrabold uppercase text-slate-400">
                                <th class="py-4.5 px-5">Tanggal</th>
                                <th class="py-4.5 px-5">Kelas / Mapel</th>
                                <th class="py-4.5 px-5 text-center">Jam Ke</th>
                                <th class="py-4.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="journalTableBody">
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500 font-bold">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Menu Bar -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700/80 shadow-2xl px-6 py-3 flex items-center justify-around">
        <!-- Home Button -->
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Beranda</span>
        </a>

        <!-- Floating QR Action Button -->
        <div class="relative -mt-8">
            <?php
            $ci = &get_instance();
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
        <button id="menuBottomToggle" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Menu</span>
        </button>
    </div>

    <!-- Attendance Details Modal (Pop up on list click) -->
    <div id="attendanceModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 transition-all duration-300 opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-105 dark:border-slate-750 transform scale-95 transition-transform duration-300">
            <!-- Modal Header -->
            <div class="bg-primary-600 dark:bg-slate-900 px-6 py-5 text-white flex items-center justify-between">
                <h3 class="font-extrabold text-base tracking-wide">Detail Kehadiran</h3>
                <button onclick="closeAttendanceModal()" class="p-1.5 rounded-full hover:bg-white/10 text-white transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-7 space-y-6">
                <div class="text-center pb-4 border-b border-slate-100 dark:border-slate-700">
                    <p id="modalDate" class="text-base font-extrabold text-slate-800 dark:text-slate-100 leading-relaxed">-</p>
                    <div id="modalStatusBadge" class="mt-2.5 inline-block">-</div>
                </div>

                <!-- Clock In & Out -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-50 dark:bg-slate-900/60 p-4.5 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-450 flex items-center justify-center text-lg">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-extrabold uppercase text-slate-400">Jam Hadir</span>
                            <span id="modalWaktu" class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">-</span>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/60 p-4.5 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-extrabold uppercase text-slate-400">Jam Pulang</span>
                            <span id="modalPulang" class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">-</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-slate-50 dark:bg-slate-900/60 p-4.5 rounded-2xl border border-slate-100 dark:border-slate-800 text-sm text-slate-700 dark:text-slate-350">
                    <span class="block text-[10px] font-extrabold uppercase text-slate-400 mb-1.5">Keterangan</span>
                    <p id="modalDesc" class="font-bold text-slate-700 dark:text-slate-200 leading-relaxed">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Details Modal (Pop up on list click) -->
    <div id="journalModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 transition-all duration-300 opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-105 dark:border-slate-750 transform scale-95 transition-transform duration-300 flex flex-col max-h-[85vh]">
            <!-- Modal Header -->
            <div class="bg-primary-600 dark:bg-slate-900 px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
                <div class="space-y-0.5">
                    <h3 class="font-extrabold text-base tracking-wide" id="jrModalTitle">Detail Jurnal KBM</h3>
                    <p class="text-xs font-bold text-white/85" id="jrModalSubtitle">-</p>
                </div>
                <button onclick="closeJournalModal()" class="p-1.5 rounded-full hover:bg-white/10 text-white transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-7 space-y-5 overflow-y-auto flex-grow">
                <!-- Material Section -->
                <div class="bg-slate-50 dark:bg-slate-900/60 p-5 rounded-2xl border border-slate-105 dark:border-slate-800">
                    <span class="block text-[10px] font-extrabold uppercase text-slate-400 mb-2"><i class="fas fa-book-open mr-1"></i>Materi yang disampaikan</span>
                    <p id="jrModalMaterial" class="text-sm font-semibold text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">-</p>
                </div>

                <!-- Student Attendance Section -->
                <div class="space-y-3">
                    <span class="block text-[10px] font-extrabold uppercase text-slate-400 mb-1.5"><i class="fas fa-users mr-1"></i>Kehadiran Siswa</span>

                    <div id="jrModalStudentLoader" class="text-center py-5 hidden">
                        <i class="fas fa-spinner fa-spin text-primary-600 text-lg"></i>
                        <p class="text-xs font-bold text-slate-400 mt-1">Memuat daftar siswa...</p>
                    </div>

                    <div id="jrModalStudentList" class="divide-y divide-slate-100 dark:divide-slate-700/60 max-h-56 overflow-y-auto pr-1.5">
                        <!-- Student lists go here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>
    <script>
        const API_URL = '<?= site_url("keaktifanguru/getMyKeaktifanMonthly") ?>';
        let responseData = {
            attendance: [],
            scheduled_jp: 0,
            filled_jp: 0,
            journals: []
        };

        // Sidebar interactions
        const sidebarDrawer = document.getElementById('sidebarDrawer');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeDrawer = document.getElementById('closeDrawer');
        const menuToggle = document.getElementById('menuToggle');
        const menuBottomToggle = document.getElementById('menuBottomToggle');

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

        if (menuToggle) menuToggle.addEventListener('click', openMenu);
        if (menuBottomToggle) menuBottomToggle.addEventListener('click', openMenu);
        if (closeDrawer) closeDrawer.addEventListener('click', closeMenu);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMenu);

        // Theme Toggle logic
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        themeToggleBtn.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.className = 'fas fa-moon';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.className = 'fas fa-sun';
            }
        });

        if (document.documentElement.classList.contains('dark')) {
            themeIcon.className = 'fas fa-sun';
        } else {
            themeIcon.className = 'fas fa-moon';
        }

        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: "Anda harus login kembali untuk masuk.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url("auth/logout") ?>';
                }
            });
        }

        // Switching Tabs
        let activeTab = 'attendance';

        function switchTab(tab) {
            activeTab = tab;
            const btnAttendance = document.getElementById('tabBtn_attendance');
            const btnJournal = document.getElementById('tabBtn_journal');
            const panelAttendance = document.getElementById('panel_attendance');
            const panelJournal = document.getElementById('panel_journal');

            if (tab === 'attendance') {
                btnAttendance.className = "w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white dark:bg-slate-700 text-primary-600 dark:text-white shadow-sm";
                btnJournal.className = "w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 text-slate-500 dark:text-slate-400";
                panelAttendance.classList.remove('hidden');
                panelJournal.classList.add('hidden');
            } else {
                btnJournal.className = "w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white dark:bg-slate-700 text-primary-600 dark:text-white shadow-sm";
                btnAttendance.className = "w-1/2 py-3 rounded-xl text-sm font-extrabold transition-all duration-300 text-slate-500 dark:text-slate-400";
                panelJournal.classList.remove('hidden');
                panelAttendance.classList.add('hidden');
            }
        }

        const monthInput = document.getElementById('reportMonth');
        monthInput.addEventListener('change', loadData);

        function loadData() {
            const selectedMonth = monthInput.value;
            const attendTableBody = document.getElementById('attendanceTableBody');
            const journalTableBody = document.getElementById('journalTableBody');

            attendTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-6 text-center text-slate-500 font-bold">
                        <i class="fas fa-spinner fa-spin text-primary-600 text-lg mb-2 block"></i>
                        Memuat data...
                    </td>
                </tr>`;
            journalTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-6 text-center text-slate-500 font-bold">
                        <i class="fas fa-spinner fa-spin text-primary-600 text-lg mb-2 block"></i>
                        Memuat data...
                    </td>
                </tr>`;

            fetch(`${API_URL}?month=${selectedMonth}`)
                .then(res => res.json())
                .then(data => {
                    responseData = data;
                    renderAttendanceStats(data.attendance);
                    renderAttendanceList(data.attendance);

                    renderJournalStats(data);
                    populateFilters(data.journals);
                    renderJournals();
                })
                .catch(err => {
                    console.error(err);
                    attendTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="py-6 text-center text-rose-500 font-bold">
                                <i class="fas fa-exclamation-triangle text-lg mb-2 block"></i>
                                Gagal memuat data.
                            </td>
                        </tr>`;
                    journalTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="py-6 text-center text-rose-500 font-bold">
                                <i class="fas fa-exclamation-triangle text-lg mb-2 block"></i>
                                Gagal memuat data.
                            </td>
                        </tr>`;
                });
        }

        // Attendance stats & listing
        function renderAttendanceStats(data) {
            let hadirCount = 0;
            let otherCount = 0;

            data.forEach(rec => {
                const status = rec.ket.toLowerCase();
                if (status === 'hadir') {
                    hadirCount++;
                } else if (status === 'sakit' || status === 'izin' || status === 'alpha' || status === 'alfa') {
                    otherCount++;
                }
            });

            document.getElementById('kpiHadirCount').innerText = `${hadirCount} Hari`;
            document.getElementById('kpiOtherCount').innerText = `${otherCount} Hari`;

            let rate = 0;
            let activeDays = hadirCount + otherCount;
            if (activeDays > 0) {
                rate = Math.round((hadirCount / activeDays) * 100);
            }
            document.getElementById('kpiPresentRate').innerText = `${rate}%`;
        }

        function formatIndoDate(dateStr) {
            const dateObj = new Date(dateStr);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const dayName = days[dateObj.getDay()];
            const dayNum = dateObj.getDate();
            const monthName = months[dateObj.getMonth()];
            const year = dateObj.getFullYear();

            return `${dayName}, ${dayNum} ${monthName} ${year}`;
        }

        function formatDateShort(dateStr) {
            const dateObj = new Date(dateStr);
            const months = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            const day = dateObj.getDate();
            const month = months[dateObj.getMonth()];
            return `${day} ${month}`;
        }

        function formatDayOnly(dateStr) {
            const dateObj = new Date(dateStr);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return days[dateObj.getDay()];
        }

        function renderAttendanceList(data) {
            const tbody = document.getElementById('attendanceTableBody');
            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">
                            <i class="fas fa-calendar-times text-2xl mb-2 block text-slate-350"></i>
                            Tidak ada riwayat kehadiran pada bulan ini.
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            data.forEach((rec, idx) => {
                const status = rec.ket.toLowerCase();
                let statusBadge = '';

                if (status === 'hadir') {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-450 border border-emerald-200/30">Hadir</span>`;
                } else if (status === 'sakit') {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-yellow-50 text-yellow-600 dark:bg-yellow-950/40 dark:text-yellow-450 border border-yellow-200/30">Sakit</span>`;
                } else if (status === 'izin') {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-450 border border-blue-200/30">Izin</span>`;
                } else {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-455 border border-rose-200/30">${rec.ket}</span>`;
                }

                const jamHadir = rec.waktu ? rec.waktu.slice(0, 5) : '--:--';
                const jamPulang = rec.pulang ? rec.pulang.slice(0, 5) : '--:--';

                html += `
                    <tr class="border-b border-slate-100 dark:border-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-700/30 active:scale-[0.99] transition cursor-pointer"
                        onclick="showAttendanceModal(${idx})">
                        <td class="py-5.5 px-5 font-bold text-slate-650 dark:text-slate-300 text-sm whitespace-nowrap">
                            ${formatDateShort(rec.tanggal)}
                        </td>
                        <td class="py-5.5 px-5">
                            <span class="font-extrabold text-slate-800 dark:text-slate-100 block text-[15px]">${formatDayOnly(rec.tanggal)}</span>
                            <span class="text-xs font-bold text-slate-450 block mt-0.5 leading-relaxed">${jamHadir} - ${jamPulang}</span>
                        </td>
                        <td class="py-5.5 px-5 text-center font-bold">
                            ${statusBadge}
                        </td>
                        <td class="py-5.5 px-5 text-right">
                            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-slate-600 transition">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            tbody.innerHTML = html;
        }

        // Attendance details modal triggers
        function showAttendanceModal(idx) {
            const rec = responseData.attendance[idx];
            const modal = document.getElementById('attendanceModal');
            const modalBox = modal.querySelector('.transform');

            document.getElementById('modalDate').innerText = formatIndoDate(rec.tanggal);

            const status = rec.ket.toLowerCase();
            let statusBadge = '';
            let statusLabel = '';
            if (status === 'hadir') {
                statusBadge = `<span class="px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-450 border border-emerald-200/30">Hadir</span>`;
                statusLabel = 'Hadir';
            } else if (status === 'sakit') {
                statusBadge = `<span class="px-3.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-600 dark:bg-yellow-950/40 dark:text-yellow-450 border border-yellow-200/30">Sakit</span>`;
                statusLabel = 'Sakit';
            } else if (status === 'izin') {
                statusBadge = `<span class="px-3.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-450 border border-blue-200/30">Izin</span>`;
                statusLabel = 'Izin';
            } else {
                statusBadge = `<span class="px-3.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-450 border border-rose-200/30">${rec.ket}</span>`;
                statusLabel = rec.ket;
            }
            document.getElementById('modalStatusBadge').innerHTML = statusBadge;
            document.getElementById('modalWaktu').innerText = rec.waktu || 'Belum Absen';
            document.getElementById('modalPulang').innerText = rec.pulang || 'Belum Absen';
            document.getElementById('modalDesc').innerHTML = `Status presensi Anda adalah: <span class="capitalize text-primary-500 font-extrabold">${statusLabel}</span>`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalBox.classList.remove('scale-95');
            }, 50);
        }

        function closeAttendanceModal() {
            const modal = document.getElementById('attendanceModal');
            const modalBox = modal.querySelector('.transform');

            modal.classList.add('opacity-0');
            modalBox.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Jurnal stats, filters & listing
        function renderJournalStats(data) {
            const sched = data.scheduled_jp;
            const filled = data.filled_jp;
            const rate = sched > 0 ? Math.round((filled / sched) * 100) : 0;

            document.getElementById('kpiJournalCount').innerText = `${filled} / ${sched} JP Terisi`;
            document.getElementById('kpiJournalRate').innerText = `${rate}%`;
            document.getElementById('kpiJournalProgressBar').style.width = `${rate}%`;
        }

        function populateFilters(journals) {
            const filterKelas = document.getElementById('filterKelas');
            const filterMapel = document.getElementById('filterMapel');

            const prevKelas = filterKelas.value;
            const prevMapel = filterMapel.value;

            const uniqueKelas = [...new Set(journals.map(j => j.kelas))].sort();
            const uniqueMapel = [...new Set(journals.map(j => j.mapel))].sort();

            filterKelas.innerHTML = '<option value="">Semua Kelas</option>';
            uniqueKelas.forEach(k => {
                filterKelas.innerHTML += `<option value="${k}">${k}</option>`;
            });

            filterMapel.innerHTML = '<option value="">Semua Mapel</option>';
            uniqueMapel.forEach(m => {
                filterMapel.innerHTML += `<option value="${m}">${m}</option>`;
            });

            if (uniqueKelas.includes(prevKelas)) filterKelas.value = prevKelas;
            if (uniqueMapel.includes(prevMapel)) filterMapel.value = prevMapel;
        }

        function renderJournals() {
            const tbody = document.getElementById('journalTableBody');
            const valKelas = document.getElementById('filterKelas').value;
            const valMapel = document.getElementById('filterMapel').value;

            let filtered = responseData.journals;

            if (valKelas) {
                filtered = filtered.filter(j => j.kelas === valKelas);
            }
            if (valMapel) {
                filtered = filtered.filter(j => j.mapel === valMapel);
            }

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">
                            <i class="fas fa-book text-2xl mb-2 block text-slate-350"></i>
                            Jurnal tidak ditemukan.
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            filtered.forEach((j, idx) => {
                html += `
                    <tr class="border-b border-slate-100 dark:border-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-700/30 active:scale-[0.99] transition cursor-pointer"
                        onclick="showJournalModal('${j.kode}', '${j.kelas}', '${j.mapel}', '${j.tanggal}', '${j.jam_ke}')">
                        <td class="py-5.5 px-5 font-bold text-slate-650 dark:text-slate-300 text-sm whitespace-nowrap">
                            ${formatDateShort(j.tanggal)}
                        </td>
                        <td class="py-5.5 px-5">
                            <span class="font-extrabold text-slate-800 dark:text-slate-100 block text-[15px]">${j.kelas}</span>
                            <span class="text-xs font-bold text-slate-400 block mt-0.5 leading-relaxed">${j.mapel}</span>
                        </td>
                        <td class="py-5.5 px-5 text-center font-bold text-slate-500 dark:text-slate-400 text-sm">
                            ${j.jam_ke}
                        </td>
                        <td class="py-5.5 px-5 text-right">
                            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-slate-600 transition">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            tbody.innerHTML = html;
        }

        // Journal details modal triggers
        function showJournalModal(kode, kelas, mapel, tanggal, jam_ke) {
            const modal = document.getElementById('journalModal');
            const modalBox = modal.querySelector('.transform');

            document.getElementById('jrModalTitle').innerText = `${kelas} - ${mapel}`;
            document.getElementById('jrModalSubtitle').innerText = `${formatIndoDate(tanggal)} • Jam ke ${jam_ke}`;

            const materialContainer = document.getElementById('jrModalMaterial');
            const listContainer = document.getElementById('jrModalStudentList');
            const loader = document.getElementById('jrModalStudentLoader');

            materialContainer.innerText = '-';
            listContainer.innerHTML = '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalBox.classList.remove('scale-95');
            }, 50);

            loader.classList.remove('hidden');

            fetch(`<?= site_url("keaktifanguru/getJournalDetailData") ?>?kode=${kode}`)
                .then(res => res.json())
                .then(data => {
                    loader.classList.add('hidden');
                    materialContainer.innerText = data.isi_jurnal || '-';

                    if (!data.students || data.students.length === 0) {
                        listContainer.innerHTML = `
                            <div class="text-center py-4 text-slate-400 font-bold text-xs">
                                Tidak ada data presensi siswa untuk sesi ini.
                            </div>`;
                        return;
                    }

                    let html = '';
                    data.students.forEach(st => {
                        const status = st.ket.toLowerCase();
                        let badgeColor = '';
                        if (status === 'hadir') {
                            badgeColor = 'text-emerald-650 dark:text-emerald-450';
                        } else if (status === 'sakit') {
                            badgeColor = 'text-yellow-600 dark:text-yellow-450';
                        } else if (status === 'izin') {
                            badgeColor = 'text-blue-600 dark:text-blue-450';
                        } else if (status === 'alpha' || status === 'alfa') {
                            badgeColor = 'text-rose-600 dark:text-rose-455';
                        } else if (status === 'telat') {
                            badgeColor = 'text-purple-650 dark:text-purple-400';
                        } else {
                            badgeColor = 'text-slate-500';
                        }

                        html += `
                            <div class="flex items-center justify-between py-3 text-sm border-b border-slate-50 dark:border-slate-700/40 last:border-none">
                                <span class="font-bold text-slate-705 dark:text-slate-200 capitalize">${st.nama_siswa.toLowerCase()}</span>
                                <span class="font-extrabold uppercase text-xs ${badgeColor}">${st.ket}</span>
                            </div>`;
                    });
                    listContainer.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    loader.classList.add('hidden');
                    materialContainer.innerText = 'Gagal memuat detail jurnal.';
                });
        }

        function closeJournalModal() {
            const modal = document.getElementById('journalModal');
            const modalBox = modal.querySelector('.transform');

            modal.classList.add('opacity-0');
            modalBox.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>

</html>