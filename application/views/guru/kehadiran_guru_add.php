<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Kehadiran - Portal Guru</title>
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
            <a href="<?= base_url('kehadiranguru') ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Tambah Kehadiran</h1>
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
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Tanggal Kehadiran</span>
                <span class="text-base font-extrabold text-slate-800 dark:text-slate-200">
                    <?= tanggal_indo($tanggal, true) ?>
                </span>
            </div>
        </div>

        <!-- Attendance Form (Auto Saves via Ajax) -->
        <?php $isReadOnly = ($tanggal !== date('Y-m-d')); ?>
        <form action="" method="post" class="space-y-4">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">

            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        Absensi Kehadiran
                    </h3>
                    <?php if ($isReadOnly): ?>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-eye"></i> Hanya Lihat (Read-Only)
                        </span>
                    <?php else: ?>
                        <span class="text-xs font-bold text-primary-500 dark:text-primary-400 flex items-center gap-1.5">
                            <i class="fas fa-cloud-upload-alt animate-pulse"></i> Auto Save
                        </span>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm" id="datatable">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-extrabold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/50">
                                <th class="py-3.5 px-4 w-12 text-center">No</th>
                                <th class="py-3.5 px-4 min-w-[150px]">Nama Guru</th>
                                <th class="py-3.5 px-4 text-center w-48">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="3" class="py-8 px-4 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                            <i class="fas fa-exclamation-circle text-2xl text-amber-500 mb-2"></i>
                                            <p class="text-sm font-bold">
                                                Tidak ada guru terdaftar hari ini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $no  = 1;
                                $nou = 1;
                                foreach ($data as $row):
                                ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                                        <td class="py-3.5 px-4 font-bold text-slate-400 text-center">
                                            <?= $nou++ ?>
                                        </td>
                                        <td class="py-3.5 px-4 font-extrabold text-slate-800 dark:text-slate-200">
                                            <?= $row['nama'] ?>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- HADIR -->
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="ket_<?= $row['id_guru'] ?>" data-id="<?= $row['id_guru'] ?>" value="hadir" class="peer hidden radio-penyesuaian" <?= $row['ket'] === 'hadir' ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-emerald-500/30 text-xs font-bold text-emerald-600 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:text-white peer-disabled:opacity-60 transition shadow-sm">
                                                        H
                                                    </span>
                                                </label>

                                                <!-- IZIN -->
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="ket_<?= $row['id_guru'] ?>" data-id="<?= $row['id_guru'] ?>" value="izin" class="peer hidden radio-penyesuaian" <?= $row['ket'] === 'izin' ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-amber-500/30 text-xs font-bold text-amber-600 peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:text-white peer-disabled:opacity-60 transition shadow-sm">
                                                        I
                                                    </span>
                                                </label>

                                                <!-- ALPHA -->
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="ket_<?= $row['id_guru'] ?>" data-id="<?= $row['id_guru'] ?>" value="alpha" class="peer hidden radio-penyesuaian" <?= $row['ket'] === 'alpha' ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-rose-500/30 text-xs font-bold text-rose-600 peer-checked:bg-rose-600 peer-checked:border-rose-600 peer-checked:text-white peer-disabled:opacity-60 transition shadow-sm">
                                                        A
                                                    </span>
                                                </label>

                                                <!-- CUTI -->
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="ket_<?= $row['id_guru'] ?>" data-id="<?= $row['id_guru'] ?>" value="cuti" class="peer hidden radio-penyesuaian" <?= $row['ket'] === 'cuti' ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-purple-500/30 text-xs font-bold text-purple-650 dark:text-purple-400 peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-disabled:opacity-60 transition shadow-sm">
                                                        C
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach;
                                ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

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
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-slate-700 dark:text-slate-355 whitespace-nowrap">Scan QR</span>
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
        // AJAX Auto-Save on radio change
        $('.radio-penyesuaian').on('change', function() {
            let value = $(this).val();
            let id = $(this).data('id');
            const tgl = '<?= $tanggal ?>';

            $.ajax({
                url: '<?= base_url('kehadiranguru/saveHadirGuru') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    value: value,
                    tanggal: tgl
                },
                success: function(res) {
                    if (res.success) {
                        console.log('Update berhasil');
                    } else {
                        console.log('Gagal:', res.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: res.message || 'Terjadi kesalahan saat menyimpan data.'
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Koneksi',
                        text: 'Gagal terhubung ke server.'
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
