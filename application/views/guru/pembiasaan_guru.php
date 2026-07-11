<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembiasaan Guru - Portal Guru</title>
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
            <h1 class="text-lg font-bold tracking-wide">Pembiasaan Guru</h1>
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
        
        <!-- Quick Action Grid -->
        <div class="grid grid-cols-2 gap-3.5">
            <button onclick="window.location.href='<?= base_url('absensiguru/set_pembiasaan') ?>'" 
                    class="py-3 px-4 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/80 font-bold text-sm flex items-center justify-center gap-2 shadow-sm hover:bg-slate-50 transition active:scale-95">
                <i class="fas fa-cog text-orange-500"></i>
                <span>Setting Guru</span>
            </button>
            <button onclick="window.location.href='<?= base_url('absensiguru/pembiasaan_add') ?>'" 
                    class="py-3 px-4 rounded-2xl bg-primary-600 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md hover:bg-primary-700 transition active:scale-95">
                <i class="fas fa-plus"></i>
                <span>Buat Absensi</span>
            </button>
        </div>

        <!-- Search & Control Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                    Daftar Absensi
                </h3>
                
                <!-- Per Page Selector -->
                <div class="flex items-center space-x-1.5">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Limit:</span>
                    <select id="perPage" 
                            class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="search" id="search" 
                       class="w-full pl-10 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                       placeholder="Cari tanggal...">
            </div>
        </div>

        <!-- Table container Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
            <!-- Scrollable Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="datatable">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-extrabold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/50">
                            <th onclick="sort('tanggal')" class="py-3.5 px-4 cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 whitespace-nowrap">
                                Tanggal <i class="fas fa-sort ml-1 text-slate-400"></i>
                            </th>
                            <th class="py-3.5 px-4 text-center">Jumlah</th>
                            <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50" id="tableBody">
                        <tr>
                            <td colspan="3" class="py-8 px-4 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-2 text-primary-600"></i>
                                    <p class="text-sm font-bold">Mengambil data absensi...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dynamic Entry Info & Custom Pagination -->
        <div class="space-y-4">
            <div class="text-center text-xs font-bold text-slate-500 dark:text-slate-400">
                Menampilkan <span id="startRecord">1</span> - <span id="endRecord">10</span> dari <span id="totalRecords">0</span> entri
            </div>
            
            <div class="flex items-center justify-center gap-2" id="pagination">
                <!-- pagination buttons inserted here -->
            </div>
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
        let state = {
            page: 1,
            perPage: 10,
            search: '',
            sortBy: 'tanggal',
            sortDir: 'DESC',
            total: 0
        };

        function loadData() {
            const params = new URLSearchParams(state).toString();

            fetch(`<?= site_url('absensiguru/pembiasaanData') ?>?${params}`)
                .then(res => res.json())
                .then(res => {
                    renderTable(res.data);
                    renderPagination(res);
                    state.total = res.total;
                    info(state.perPage, state.page, state.total);
                })
                .catch(err => {
                    document.getElementById('tableBody').innerHTML = `
                        <div class="p-6 text-center text-rose-500 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p class="text-sm font-bold">Gagal memuat data absensi.</p>
                        </div>
                    `;
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="py-8 px-4 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                <i class="fas fa-calendar-times text-2xl text-slate-400 mb-2"></i>
                                <p class="text-sm font-bold">Tidak ada data absensi pembiasaan.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const todayStr = '<?= date('Y-m-d') ?>';

            data.forEach(row => {
                const isToday = row.tanggal === todayStr;
                const editTitle = isToday ? 'Edit' : 'Lihat';
                const editIcon = isToday ? 'fas fa-edit' : 'fas fa-eye';
                const deleteBtn = isToday ? `
                    <button data-id="${row.tanggal}" 
                            class="h-8 w-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 dark:text-rose-455 flex items-center justify-center transition hover:scale-105 active:scale-95 tombol-hapus"
                            title="Hapus">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                ` : '';

                tbody.innerHTML += `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                    <td class="py-3.5 px-4 font-extrabold text-slate-800 dark:text-white">
                        ${tanggalIndo(row.tanggal, true)}
                    </td>
                    <td class="py-3.5 px-4 font-extrabold text-slate-700 dark:text-slate-300 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                            ${row.jumlah_guru} Guru
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="window.location.href='<?php echo base_url() ?>absensiguru/pembiasaan_add/${row.tanggal}'" 
                                    class="h-8 w-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/40 dark:hover:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center transition hover:scale-105 active:scale-95"
                                    title="${editTitle}">
                                <i class="${editIcon} text-xs"></i>
                            </button>
                            ${deleteBtn}
                            <button onclick="window.open('<?php echo base_url() ?>absensiguru/screenApelGuru/${row.tanggal}','_blank')" 
                                    class="h-8 w-8 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/40 dark:text-emerald-450 flex items-center justify-center transition hover:scale-105 active:scale-95"
                                    title="Cetak Monitor">
                                <i class="fas fa-download text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        }

        function renderPagination(meta) {
            const pag = document.getElementById('pagination');
            pag.innerHTML = '';

            for (let i = 1; i <= meta.lastPage; i++) {
                pag.innerHTML += `
                <button onclick="goPage(${i})"
                    class="h-9 w-9 text-xs font-bold rounded-xl border flex items-center justify-center transition
                    ${i === meta.page 
                        ? 'bg-primary-600 border-primary-600 text-white shadow-md' 
                        : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800'}">
                    ${i}
                </button>
            `;
            }
        }

        function goPage(page) {
            state.page = page;
            loadData();
        }

        function sort(field) {
            state.sortDir = state.sortDir === 'ASC' ? 'DESC' : 'ASC';
            state.sortBy = field;
            loadData();
        }

        function info(perpage, page, total) {
            document.getElementById('startRecord').textContent = total === 0 ? 0 : (page - 1) * perpage + 1;
            document.getElementById('endRecord').textContent = Math.min(page * perpage, total);
            document.getElementById('totalRecords').textContent = total;
        }

        /* ===== EVENTS ===== */
        document.getElementById('search').addEventListener('input', e => {
            state.search = e.target.value;
            state.page = 1;
            loadData();
        });

        document.getElementById('perPage').addEventListener('change', e => {
            state.perPage = e.target.value;
            state.page = 1;
            loadData();
        });

        function tanggalIndo(tanggal, tampilkanHari = false) {
            const hariArray = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulanArray = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const dateObj = new Date(tanggal);
            if (isNaN(dateObj)) return tanggal;

            const hari = dateObj.getDay();
            const tgl = dateObj.getDate();
            const bln = dateObj.getMonth() + 1;
            const thn = dateObj.getFullYear();

            let formatIndo = `${tgl} ${bulanArray[bln]} ${thn}`;
            if (tampilkanHari) {
                formatIndo = `${hariArray[hari]}, ${formatIndo}`;
            }

            return formatIndo;
        }

        /* INIT */
        loadData();

        // AJAX Delete using SweetAlert2
        $(document).on('click', '.tombol-hapus', function(e) {
            e.preventDefault();

            const id = $(this).data('id');
            const base_url = '<?= base_url() ?>';

            Swal.fire({
                title: 'Hapus Absensi?',
                text: 'Data absensi pembiasaan tanggal ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(base_url + 'absensiguru/hapusPembiasaan', { id })
                        .done(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Absensi pembiasaan berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadData();
                        })
                        .fail(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat menghapus data absensi.'
                            });
                        });
                }
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
