<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Pembiasaan - Portal Guru</title>
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
            <h1 class="text-lg font-bold tracking-wide">Set Pembiasaan</h1>
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
        
        <!-- Table container Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
            <!-- Table Header info -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50">
                <h3 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                    Daftar Jadwal Hari Pembiasaan
                </h3>
            </div>

            <!-- Scrollable Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="datatable">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-extrabold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/50">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 min-w-[150px]">Nama Guru</th>
                            
                            <!-- Saturday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Saturday">
                                    <span class="text-[10px] font-bold mt-0.5">Sab</span>
                                </label>
                            </th>

                            <!-- Sunday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Sunday">
                                    <span class="text-[10px] font-bold mt-0.5">Ahd</span>
                                </label>
                            </th>

                            <!-- Monday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Monday">
                                    <span class="text-[10px] font-bold mt-0.5">Sen</span>
                                </label>
                            </th>

                            <!-- Tuesday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Tuesday">
                                    <span class="text-[10px] font-bold mt-0.5">Sel</span>
                                </label>
                            </th>

                            <!-- Wednesday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Wednesday">
                                    <span class="text-[10px] font-bold mt-0.5">Rab</span>
                                </label>
                            </th>

                            <!-- Thursday -->
                            <th class="py-3.5 px-3 text-center">
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="check-all-day h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-day="Thursday">
                                    <span class="text-[10px] font-bold mt-0.5">Kam</span>
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        <?php foreach ($guru as $index => $row):
                            $hari_guru = array_map('trim', explode(',', $row['daftar_hari']));
                        ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                                <td class="py-3 px-4 font-bold text-slate-400 text-center">
                                    <?= $index + 1 ?>
                                </td>
                                <td class="py-3 px-4 font-extrabold text-slate-800 dark:text-slate-200">
                                    <?= $row['nama'] ?>
                                </td>
                                
                                <!-- Saturday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Saturday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Saturday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Saturday">
                                </td>

                                <!-- Sunday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Sunday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Sunday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Sunday">
                                </td>

                                <!-- Monday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Monday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Monday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Monday">
                                </td>

                                <!-- Tuesday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Tuesday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Tuesday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Tuesday">
                                </td>

                                <!-- Wednesday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Wednesday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Wednesday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Wednesday">
                                </td>

                                <!-- Thursday -->
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" <?= in_array('Thursday', $hari_guru) ? 'checked' : '' ?> 
                                           class="day-checkbox Thursday h-6 w-6 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition cursor-pointer" 
                                           data-id="<?= $row['id_guru'] ?>" data-hari="Thursday">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
        document.querySelectorAll('.day-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const guruId = this.getAttribute('data-id');
                const hari = this.getAttribute('data-hari');
                const isChecked = this.checked ? 1 : 0;

                fetch(`<?= site_url('absensiguru/setPembiasaan') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_guru: guruId,
                        hari: hari,
                        status: isChecked
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Status pembiasaan diperbarui');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memperbarui status pembiasaan.'
                        });
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
            });
        });

        document.querySelectorAll('.check-all-day').forEach(masterCheckbox => {
            masterCheckbox.addEventListener('change', function() {
                const day = this.getAttribute('data-day');
                const isChecked = this.checked;

                document.querySelectorAll(`.day-checkbox.${day}`).forEach(checkbox => {
                    checkbox.checked = isChecked;
                    checkbox.dispatchEvent(new Event('change'));
                });
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
