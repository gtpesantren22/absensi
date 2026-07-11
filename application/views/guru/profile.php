<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Portal Guru</title>
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
            <h1 class="text-lg font-bold tracking-wide">Profil Saya</h1>
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
    <div class="px-4 py-6 space-y-6">

        <!-- Form Profile (Allows Photo Uploads) -->
        <form action="<?= base_url('profile/update_account') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
            
            <!-- Photo Avatar Upload Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/60 shadow-sm flex flex-col items-center text-center space-y-4">
                <!-- Avatar Preview Wrapper -->
                <div class="relative group cursor-pointer" onclick="triggerPhotoSelect()">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-slate-100 dark:border-slate-700 shadow-md bg-primary-600 text-white flex items-center justify-center">
                        <?php if ($data->foto && file_exists('./uploads/profile/' . $data->foto)): ?>
                            <img id="avatarPreview" src="<?= base_url('uploads/profile/' . $data->foto) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <img id="avatarPreview" src="https://ui-avatars.com/api/?name=<?= inisial($guru->nama ?? $data->nama_user) ?>&background=2563eb&color=fff&size=128" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <!-- Camera Edit Badge -->
                    <div class="absolute bottom-0 right-0 h-8 w-8 bg-primary-600 text-white border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center text-xs shadow-md">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                <!-- Hidden Input File -->
                <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*" onchange="previewPhoto(this)">

                <div class="space-y-1">
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white leading-tight">
                        <?= $guru->nama ?? $data->nama_user ?>
                    </h3>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">
                        <?= strtoupper($data->level) ?>
                    </p>
                </div>
            </div>

            <!-- Profile Info Form Fields -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/60 shadow-sm space-y-4">
                <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2">
                    Informasi Akun
                </h4>

                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="nama_user" value="<?= $guru->nama ?? $data->nama_user ?>" required
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Username -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Username</label>
                    <input type="text" name="username" value="<?= $data->username ?>" required
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- No HP -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">No. Telepon / WA</label>
                    <input type="tel" name="no_hp" value="<?= $guru->no_hp ?? '' ?>" placeholder="Contoh: 08123456789"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Password Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/60 shadow-sm space-y-4">
                <div class="space-y-1">
                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        Ubah Password
                    </h4>
                    <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                        Kosongkan bagian ini jika tidak ingin memperbarui password.
                    </p>
                </div>

                <!-- Password Baru -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="password_baru" name="password_baru"
                               class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="button" onclick="togglePassword('password_baru', this)" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-650 transition">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" id="password_konfirmasi" name="password_konfirmasi"
                               class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="button" onclick="togglePassword('password_konfirmasi', this)" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-650 transition">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    <!-- Validation message -->
                    <p id="password_message" class="text-xs font-bold mt-1"></p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn_submit"
                    class="w-full py-4 px-4 rounded-2xl bg-primary-600 hover:bg-primary-700 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-md transition active:scale-95">
                <i class="fas fa-save text-base"></i>
                <span>Simpan Perubahan</span>
            </button>

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
        // Trigger photo select click
        function triggerPhotoSelect() {
            document.getElementById('fotoInput').click();
        }

        // Preview photo after select
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Flash Messages Alert
        <?php if ($this->session->flashdata('ok')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $this->session->flashdata("ok") ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php elseif ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $this->session->flashdata("error") ?>'
            });
        <?php endif; ?>

        // Toggle visibility of password fields
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.className = "fas fa-eye-slash text-sm";
            } else {
                input.type = "password";
                icon.className = "fas fa-eye text-sm";
            }
        }

        // Realtime Password match checking
        const passwordBaru = document.getElementById("password_baru");
        const passwordKonfirmasi = document.getElementById("password_konfirmasi");
        const message = document.getElementById("password_message");
        const btnSubmit = document.getElementById("btn_submit");

        function cekPassword() {
            const pass1 = passwordBaru.value;
            const pass2 = passwordKonfirmasi.value;

            if (pass1 === "" && pass2 === "") {
                message.innerHTML = "";
                btnSubmit.disabled = false;
                btnSubmit.classList.remove("opacity-50", "cursor-not-allowed");
                return;
            }

            if (pass1 !== "" && pass2 === "") {
                message.innerHTML = "Konfirmasi password belum diisi.";
                message.className = "text-xs font-bold mt-1 text-amber-500";
                btnSubmit.disabled = true;
                btnSubmit.classList.add("opacity-50", "cursor-not-allowed");
                return;
            }

            if (pass1 === pass2) {
                message.innerHTML = "<i class='fas fa-check-circle mr-1'></i> Password cocok.";
                message.className = "text-xs font-bold mt-1 text-emerald-500";
                btnSubmit.disabled = false;
                btnSubmit.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                message.innerHTML = "<i class='fas fa-times-circle mr-1'></i> Konfirmasi password tidak cocok.";
                message.className = "text-xs font-bold mt-1 text-rose-500";
                btnSubmit.disabled = true;
                btnSubmit.classList.add("opacity-50", "cursor-not-allowed");
            }
        }

        passwordBaru.addEventListener("keyup", cekPassword);
        passwordKonfirmasi.addEventListener("keyup", cekPassword);

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
