<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Siswa - Portal Guru</title>
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

    <!-- Sidebar Drawer Menu -->
    <?php $this->load->view('guru/sidebar'); ?>

    <!-- Top Sticky Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 py-4 shadow-lg sticky top-0 z-50 flex items-center justify-between text-white">
        <div class="flex items-center space-x-3">
            <a href="<?= base_url() ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Lapor Pelanggaran</h1>
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

    <!-- Main Container -->
    <div class="px-4 py-5 max-w-xl mx-auto space-y-5">

        <!-- Status Connection Badge -->
        <div class="flex items-center justify-between px-4 py-2.5 rounded-2xl border text-xs font-semibold <?= $api_connected ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400' ?>">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full <?= $api_connected ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
                <span><?= $api_connected ? 'API Pelanggaran Terhubung' : 'Server API Standby' ?></span>
            </div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400"><?= date('d M Y') ?></span>
        </div>

        <?php if (!$api_connected && !empty($api_error)): ?>
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5 text-amber-600 dark:text-amber-400"></i>
                <div>
                    <span class="font-bold">Info API:</span> <?= htmlspecialchars($api_error) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="glass-card rounded-2xl p-5 shadow-xl space-y-5">
            <form id="formPelanggaranGuru" class="space-y-5">

                <!-- 1. IDENTITAS SANTRI -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        1. Pilih Santri / Siswa <span class="text-rose-500">*</span>
                    </label>

                    <input type="hidden" name="santri_id" id="santri_id_guru" required>

                    <!-- Search Box -->
                    <div id="searchSantriBoxGuru" class="relative">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-search text-sm"></i>
                            </div>
                            <input type="text" id="inputSearchSantriGuru" autocomplete="off"
                                placeholder="Cari nama santri atau NIS..."
                                class="w-full pl-10 pr-10 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm">
                            <div id="loadingSearchGuru" class="absolute inset-y-0 right-0 pr-3.5 flex items-center hidden">
                                <i class="fas fa-circle-notch fa-spin text-primary-500 text-sm"></i>
                            </div>
                        </div>

                        <!-- Dropdown Results -->
                        <div id="dropdownSantriGuru" class="absolute left-0 right-0 top-full mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 max-h-60 overflow-y-auto z-50 hidden divide-y divide-slate-100 dark:divide-slate-700/60">
                        </div>
                    </div>

                    <!-- Selected Preview -->
                    <div id="selectedSantriCardGuru" class="hidden p-4 rounded-xl bg-primary-50/60 dark:bg-slate-800 border border-primary-200 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-primary-600 text-white font-extrabold text-base flex items-center justify-center shadow-md shadow-primary-500/20" id="previewAvatarGuru">
                                S
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm" id="previewNamaGuru">-</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300" id="previewNisGuru">-</span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span id="previewKelasGuru">-</span> • <span id="previewKamarGuru">-</span>
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="resetSelectedSantriGuru()" class="p-2 text-xs font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/40 rounded-lg transition">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- 2. JENIS PELANGGARAN -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        2. Jenis Pelanggaran &amp; Poin <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="point_id" id="point_id_guru" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition appearance-none shadow-sm">
                            <option value="">-- Pilih Jenis Pelanggaran --</option>
                            <?php if (!empty($points)): ?>
                                <?php foreach ($points as $p): ?>
                                    <?php 
                                        $pt = (int)($p['point'] ?? ($p['jumlah_poin'] ?? ($p['jumlah'] ?? 0)));
                                        $kat = $p['kategori'] ?? ($p['jenis'] ?? '');
                                        $kode = !empty($p['kode']) ? '[' . $p['kode'] . '] ' : '';
                                    ?>
                                    <option value="<?= $p['id'] ?>" data-point="<?= $pt ?>" data-kategori="<?= htmlspecialchars($kat) ?>" data-kode="<?= htmlspecialchars($p['kode'] ?? '') ?>">
                                        <?= htmlspecialchars($kode . $p['nama']) ?> (<?= $pt ?> Poin<?= !empty($kat) ? ' - ' . htmlspecialchars($kat) : '' ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>

                    <div id="pointInfoGuru" class="hidden mt-2 p-2.5 rounded-lg bg-slate-100 dark:bg-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400" id="pointKategoriGuru">Kategori: -</span>
                        <span class="font-extrabold text-amber-600 dark:text-amber-400" id="pointBadgeGuru">+0 Poin</span>
                    </div>
                </div>

                <!-- 3. TANGGAL, LOKASI & PELAPOR -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            3. Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal_guru" value="<?= date('Y-m-d') ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            4. Lokasi Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi" id="lokasi_guru" list="listLokasiGuru" required
                            placeholder="Lokasi kejadian..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm">
                        <datalist id="listLokasiGuru">
                            <option value="Gerbang Utama"></option>
                            <option value="Kamar / Asrama"></option>
                            <option value="Masjid"></option>
                            <option value="Kantin"></option>
                            <option value="Area Kelas / Gedung"></option>
                            <option value="Luar Lingkungan Pondok"></option>
                        </datalist>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            5. Nama Pelapor <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="pelapor" id="pelapor_guru" required
                            value="<?= htmlspecialchars($this->session->userdata('nama_user') ?? '') ?>"
                            placeholder="Nama pelapor (contoh: Ustadz Husein)..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm">
                    </div>
                </div>

                <!-- 4. KRONOLOGI -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        6. Kronologi Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="kronologi" id="kronologi_guru" rows="3" required
                        placeholder="Uraikan kronologi kejadian pelanggaran..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm"></textarea>
                </div>

                <!-- 5. SAKSI -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        7. Saksi-Saksi (Opsional)
                    </label>
                    <div class="flex gap-2 mb-2.5">
                        <input type="text" id="inputSaksiGuru" placeholder="Ketik nama saksi..."
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-sm">
                        <button type="button" onclick="addSaksiGuru()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs transition">
                            Tambah
                        </button>
                    </div>

                    <div id="saksiTagsContainerGuru" class="flex flex-wrap gap-2 p-2 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30 min-h-[36px] items-center">
                        <span class="text-xs text-slate-400 italic">Belum ada saksi tambahan.</span>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-2">
                    <button type="submit" id="btnSubmitGuru" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-extrabold text-sm shadow-lg shadow-primary-500/20 hover:shadow-primary-500/40 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Laporan Pelanggaran</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Bottom Navigation Bar (Consistent 3-Menu Layout) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700/80 shadow-2xl px-6 py-3 flex items-center justify-around">
        <!-- Home Button -->
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 w-16 font-extrabold transition">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Beranda</span>
        </a>

        <!-- Floating QR Action Button -->
        <div class="relative -mt-8">
            <a href="<?= base_url('qrcode/scan/masuk') ?>" 
               class="w-16 h-16 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-800 hover:scale-105 active:scale-95 transition-transform duration-200"
               title="Scan QR Absensi">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-slate-700 dark:text-slate-400 whitespace-nowrap">Scan QR</span>
        </div>

        <!-- Menu Button (Trigger Sidebar Drawer) -->
        <button id="menuBottomToggle" onclick="openMenu()" class="flex flex-col items-center space-y-1 text-primary-600 dark:text-primary-400 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Menu</span>
        </button>
    </div>

    <!-- SweetAlert2 Script -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>

    <script>
        // Drawer toggle logic
        function openMenu() {
            const drawer = document.getElementById('sidebarDrawer');
            const overlay = document.getElementById('sidebarOverlay');
            if (drawer && overlay) {
                drawer.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            }
        }

        function closeMenu() {
            const drawer = document.getElementById('sidebarDrawer');
            const overlay = document.getElementById('sidebarOverlay');
            if (drawer && overlay) {
                drawer.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        const closeDrawerBtn = document.getElementById('closeDrawer');
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeMenu);
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMenu);

        // Standard Theme switching logic matching other pages
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

        // Standard SweetAlert2 Logout confirmation matching other pages
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

        // Live Santri Search
        let searchTimerGuru = null;
        let saksiListGuru = [];

        document.querySelectorAll('.saksi-tag-guru').forEach(el => {
            const name = el.getAttribute('data-name');
            if (name && !saksiListGuru.includes(name)) {
                saksiListGuru.push(name);
            }
        });

        const inputSearchGuru = document.getElementById('inputSearchSantriGuru');
        const dropdownGuru = document.getElementById('dropdownSantriGuru');
        const loadingGuru = document.getElementById('loadingSearchGuru');
        const santriIdInputGuru = document.getElementById('santri_id_guru');
        const searchBoxGuru = document.getElementById('searchSantriBoxGuru');
        const selectedCardGuru = document.getElementById('selectedSantriCardGuru');

        inputSearchGuru.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(searchTimerGuru);

            if (q.length < 2) {
                dropdownGuru.classList.add('hidden');
                dropdownGuru.innerHTML = '';
                loadingGuru.classList.add('hidden');
                return;
            }

            loadingGuru.classList.remove('hidden');

            searchTimerGuru = setTimeout(() => {
                fetch(`<?= site_url('pelanggaran/search_santri?search=') ?>` + encodeURIComponent(q))
                    .then(res => res.json())
                    .then(res => {
                        loadingGuru.classList.add('hidden');
                        if (res.status && Array.isArray(res.data) && res.data.length > 0) {
                            renderDropdownGuru(res.data);
                        } else {
                            dropdownGuru.innerHTML = `
                                <div class="p-4 text-center text-xs text-slate-400">
                                    Tidak ditemukan santri dengan nama "${q}"
                                </div>
                            `;
                            dropdownGuru.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        loadingGuru.classList.add('hidden');
                        dropdownGuru.innerHTML = `
                            <div class="p-3 text-center text-xs text-rose-500">
                                Gagal menghubungi API Santri.
                            </div>
                        `;
                        dropdownGuru.classList.remove('hidden');
                    });
            }, 300);
        });

        function renderDropdownGuru(items) {
            dropdownGuru.innerHTML = '';
            items.forEach(item => {
                const row = document.createElement('div');
                row.className = 'p-3 hover:bg-primary-50 dark:hover:bg-slate-700/60 cursor-pointer flex items-center justify-between transition';
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 font-bold text-xs flex items-center justify-center">
                            ${item.nama ? item.nama.charAt(0).toUpperCase() : 'S'}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-sm">${item.nama}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                NIS: ${item.nis || '-'} ${item.kelas ? ' • ' + item.kelas : ''} ${item.kamar ? ' • ' + item.kamar : ''}
                            </div>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 font-bold text-slate-600 dark:text-slate-300">Pilih</span>
                `;
                row.onclick = () => selectSantriGuru(item);
                dropdownGuru.appendChild(row);
            });
            dropdownGuru.classList.remove('hidden');
        }

        function selectSantriGuru(item) {
            santriIdInputGuru.value = item.id;
            document.getElementById('previewNamaGuru').textContent = item.nama;
            document.getElementById('previewNisGuru').textContent = 'NIS: ' + (item.nis || '-');
            document.getElementById('previewKelasGuru').textContent = item.kelas ? 'Kelas ' + item.kelas : 'Santri';
            document.getElementById('previewKamarGuru').textContent = item.kamar ? item.kamar : 'Pondok';
            document.getElementById('previewAvatarGuru').textContent = item.nama ? item.nama.charAt(0).toUpperCase() : 'S';

            searchBoxGuru.classList.add('hidden');
            selectedCardGuru.classList.remove('hidden');
            dropdownGuru.classList.add('hidden');
            inputSearchGuru.value = '';
        }

        function resetSelectedSantriGuru() {
            santriIdInputGuru.value = '';
            selectedCardGuru.classList.add('hidden');
            searchBoxGuru.classList.remove('hidden');
            inputSearchGuru.focus();
        }

        document.addEventListener('click', function(e) {
            if (!searchBoxGuru.contains(e.target)) {
                dropdownGuru.classList.add('hidden');
            }
        });

        // Point selection
        const selectPointGuru = document.getElementById('point_id_guru');
        const pointInfoGuru = document.getElementById('pointInfoGuru');
        const pointKategoriGuru = document.getElementById('pointKategoriGuru');
        const pointBadgeGuru = document.getElementById('pointBadgeGuru');

        selectPointGuru.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                const pt = opt.getAttribute('data-point') || 0;
                const kat = opt.getAttribute('data-kategori') || 'Pelanggaran';
                pointKategoriGuru.textContent = 'Kategori: ' + kat;
                pointBadgeGuru.textContent = '+' + pt + ' Poin';
                pointInfoGuru.classList.remove('hidden');
            } else {
                pointInfoGuru.classList.add('hidden');
            }
        });

        // Saksi Management
        const inputSaksiGuru = document.getElementById('inputSaksiGuru');
        inputSaksiGuru.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSaksiGuru();
            }
        });

        function addSaksiGuru() {
            const val = inputSaksiGuru.value.trim();
            if (!val) return;

            if (saksiListGuru.includes(val)) {
                inputSaksiGuru.value = '';
                return;
            }

            saksiListGuru.push(val);
            renderSaksiGuru();
            inputSaksiGuru.value = '';
            inputSaksiGuru.focus();
        }

        function removeSaksiGuru(btn) {
            const tag = btn.closest('.saksi-tag-guru');
            const name = tag.getAttribute('data-name');
            saksiListGuru = saksiListGuru.filter(s => s !== name);
            renderSaksiGuru();
        }

        function renderSaksiGuru() {
            const container = document.getElementById('saksiTagsContainerGuru');
            container.innerHTML = '';
            if (saksiListGuru.length === 0) {
                container.innerHTML = '<span class="text-xs text-slate-400 italic">Belum ada saksi tambahan.</span>';
                return;
            }

            saksiListGuru.forEach(name => {
                const tag = document.createElement('div');
                tag.className = 'saksi-tag-guru inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800 text-xs font-bold';
                tag.setAttribute('data-name', name);
                tag.innerHTML = `
                    <i class="fas fa-user-check text-[10px]"></i>
                    <span>${name}</span>
                    <button type="button" onclick="removeSaksiGuru(this)" class="ml-1 text-primary-400 hover:text-primary-700">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                `;
                container.appendChild(tag);
            });
        }

        // Submit form
        document.getElementById('formPelanggaranGuru').addEventListener('submit', function(e) {
            e.preventDefault();

            const santri_id = document.getElementById('santri_id_guru').value;
            const point_id  = document.getElementById('point_id_guru').value;
            const tanggal   = document.getElementById('tanggal_guru').value;
            const lokasi    = document.getElementById('lokasi_guru').value;
            const kronologi = document.getElementById('kronologi_guru').value;
            const pelapor   = document.getElementById('pelapor_guru').value;

            if (!santri_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Santri',
                    text: 'Silakan cari dan pilih santri yang melakukan pelanggaran terlebih dahulu.',
                    confirmButtonColor: '#2563eb'
                });
                inputSearchGuru.focus();
                return;
            }

            if (!point_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Poin Pelanggaran',
                    text: 'Silakan tentukan jenis pelanggaran tata tertib.',
                    confirmButtonColor: '#2563eb'
                });
                selectPointGuru.focus();
                return;
            }

            if (!pelapor) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pelapor Wajib Diisi',
                    text: 'Silakan masukkan nama pelapor kejadian.',
                    confirmButtonColor: '#2563eb'
                });
                document.getElementById('pelapor_guru').focus();
                return;
            }

            const payload = {
                santri_id: santri_id,
                point_id: parseInt(point_id),
                tanggal: tanggal,
                lokasi: lokasi,
                kronologi: kronologi,
                pelapor: pelapor,
                saksi: saksiListGuru
            };

            Swal.fire({
                title: 'Kirim Laporan?',
                text: 'Data pelanggaran akan dicatat ke sistem pembinaan santri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b'
            }).then((res) => {
                if (res.isConfirmed) {
                    const btn = document.getElementById('btnSubmitGuru');
                    const orig = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengirim...';

                    fetch('<?= site_url('pelanggaran/submit') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json().then(data => ({ ok: res.ok, data: data })))
                    .then(result => {
                        btn.disabled = false;
                        btn.innerHTML = orig;

                        if (result.ok && result.data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Terkirim!',
                                text: result.data.message || 'Laporan pelanggaran berhasil disimpan.',
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                document.getElementById('formPelanggaranGuru').reset();
                                resetSelectedSantriGuru();
                                pointInfoGuru.classList.add('hidden');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengirim',
                                text: result.data.message || 'Terjadi kesalahan saat memproses laporan.',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = orig;
                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi Gagal',
                            text: 'Tidak dapat terhubung ke server API.',
                            confirmButtonColor: '#2563eb'
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>
