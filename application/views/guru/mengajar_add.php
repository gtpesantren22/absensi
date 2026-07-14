<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Jurnal Mengajar - Portal Guru</title>
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
            <a href="<?= base_url('mengajar') ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Tambah Jurnal Mengajar</h1>
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
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Tanggal Jurnal</span>
                <span class="text-base font-extrabold text-slate-800 dark:text-slate-200">
                    <?= tanggal_indo($tanggal, true) ?>
                </span>
            </div>
        </div>

        <!-- Matrix Table Container -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                    Matriks Jadwal & Jurnal Mengajar
                </h3>
            </div>

            <!-- Responsive Scrolling Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="datatable">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-extrabold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/50">
                            <th class="py-3.5 px-4 min-w-[150px]">Nama Guru</th>
                            <?php for ($i = 1; $i <= $jml_jp; $i++) : ?>
                                <th class="py-3.5 px-2.5 text-center w-12 border-l border-slate-100 dark:border-slate-850">
                                    <?= $i ?>
                                </th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="<?= $jml_jp + 1 ?>" class="py-8 px-4 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                        <i class="fas fa-exclamation-circle text-2xl text-amber-500 mb-2"></i>
                                        <p class="text-sm font-bold">
                                            Tidak ada jadwal mengajar pada hari <?= hari_indo($hari) ?>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $hariini = $tanggal;
                            $id_semester_aktif = $this->session->userdata('id_semester_aktif');
                            foreach ($data as $row) :
                                $guru = $row['id_guru'];

                                for ($i = 1; $i <= $jml_jp; $i++) {
                                    ${"cek$i"} = $this->db
                                        ->query("SELECT * FROM mengajar WHERE id_guru='$guru' AND tanggal='$hariini' AND jam=$i AND id_lembaga = '$id_lembaga'")
                                        ->row();
                                }

                                // Get daily attendance status
                                $cek_hadir = $this->db->get_where('kehadiran_guru', [
                                    'id_guru' => $guru,
                                    'tanggal' => $hariini,
                                    'id_semester' => $id_semester_aktif
                                ])->row();

                                $forcedLabel = '';
                                if ($cek_hadir && in_array(strtolower($cek_hadir->ket), ['izin', 'sakit', 'alpha', 'alfa', 'cuti'])) {
                                    $ketAbsen = strtolower($cek_hadir->ket);
                                    $badgeColor = 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400';
                                    if ($ketAbsen === 'izin') {
                                        $badgeColor = 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400';
                                    } else if ($ketAbsen === 'sakit') {
                                        $badgeColor = 'bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400';
                                    }
                                    $forcedLabel = ' <span class="text-[10px] px-2 py-0.5 rounded-lg border border-slate-205 dark:border-slate-800 font-extrabold capitalize ' . $badgeColor . '">' . $ketAbsen . '</span>';
                                }
                            ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                                    <!-- Nama Guru -->
                                    <td class="py-3 px-4 font-bold">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <a data-guru="<?= $row['id_guru'] ?>" 
                                               class="text-primary-650 hover:text-primary-750 dark:text-primary-400 dark:hover:text-primary-300 hover:underline cursor-pointer show-rinci font-extrabold transition flex items-center gap-1.5">
                                                <i class="fas fa-user-edit text-xs opacity-60"></i>
                                                <?= $row['nama'] ?>
                                            </a>
                                            <?= $forcedLabel ?>
                                        </div>
                                    </td>

                                    <!-- Jam Matrix Cells -->
                                    <?php for ($i = 1; $i <= $jml_jp; $i++) : ?>
                                        <td class="py-3 px-2.5 text-center font-extrabold text-xs border-l border-slate-100 dark:border-slate-850/50
                                            <?= in_array($i, $row['jam'])
                                                    ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 font-extrabold'
                                                    : 'text-slate-400 dark:text-slate-500' ?>
                                        ">
                                            <?= ${"cek$i"} ? ${"cek$i"}->ket : '-' ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Input Absensi Jurnal -->
    <div id="inputModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center p-4 z-[110] transition-opacity duration-300">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl w-full max-w-lg max-h-[85vh] overflow-hidden flex flex-col shadow-2xl">
            <!-- Header Modal -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700/50">
                <h3 class="font-extrabold text-base text-slate-850 dark:text-white">Input Absensi Jurnal</h3>
                <button onclick="closeModal('inputModal')" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Content Area inside Modal -->
            <div class="px-5 py-4 overflow-y-auto flex-1 text-slate-800 dark:text-slate-200">
                <div id="showHasil"></div>
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
        // Clicking Teacher Name to Load Modal Details
        document.querySelectorAll('.show-rinci').forEach(function(element) {
            element.addEventListener('click', function() {
                const guru = this.getAttribute('data-guru');
                const tanggal = "<?= $tanggal ?>";
                
                // Show loader while fetching
                document.getElementById('showHasil').innerHTML = `
                    <div class="py-8 text-center text-slate-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2 text-primary-600"></i>
                        <p class="text-sm font-bold">Mengambil rincian jadwal...</p>
                    </div>
                `;
                openModal('inputModal');

                fetch(`<?= base_url('mengajar/rincian_guru') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `guru=${guru}&tanggal=${tanggal}`
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('showHasil').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('showHasil').innerHTML = `
                        <div class="py-6 text-center text-rose-500">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p class="text-sm font-bold">Gagal memuat rincian jadwal.</p>
                        </div>
                    `;
                });
            });
        });

        // AJAX Modal form submission
        $(document).on('submit', '#form-absensi', function(e) {
            e.preventDefault();
            const formData = [];

            // Loop through each checked radio button
            $('input[type="radio"]:checked').each(function() {
                const value = $(this).val();
                const jam = $(this).data('jam');
                const guru = $(this).data('guru');
                const alasan = $(this).closest('tr').find('textarea').val();

                formData.push({
                    value: value,
                    jam: jam,
                    guru: guru,
                    alasan: alasan
                });
            });

            $.ajax({
                url: '<?= site_url("mengajar/simpanJam") ?>',
                type: 'POST',
                data: {
                    datas: formData,
                    tanggal: '<?= $tanggal ?>'
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: 'Absensi jurnal mengajar berhasil disimpan.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan absensi jurnal.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data.'
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

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
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
