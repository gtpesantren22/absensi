<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($app_name) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>sw/sweetalert2.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
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
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .flux-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .dark .flux-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }

        .sidebar-item.active {
            background-color: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .dark .sidebar-item.active {
            background-color: rgba(59, 130, 246, 0.2);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .dropdown-menu.open {
            max-height: 500px;
        }


        .stat-card:hover {
            transform: translateY(-2px);
        }

        .attendance-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        /* Animasi loading skeleton */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white dark:bg-gray-800 flux-shadow">
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Logo dan Brand -->
                <div class="flex items-center space-x-3">
                    <button id="sidebarToggle"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <div class="flex items-center space-x-2">
                        <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                            <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="w-8 h-8 rounded-lg object-contain">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
                                <i class="fas fa-school text-white"></i>
                            </div>
                        <?php endif; ?>
                        <h1 class="text-xl font-bold"><?= htmlspecialchars($app_name) ?></h1>
                    </div>
                </div>

                <!-- Right Side Header -->
                <div class="flex items-center space-x-4">
                    <?php if ($this->session->userdata('level') === 'super_admin') {
                        $lmbdata = $this->db->query("SELECT * FROM lembaga ORDER BY nama ASC");
                    ?>
                        <!-- Select Lembaga -->
                        <div class="hidden md:block">
                            <select onchange="gantiLembaga(this.value)"
                                class="px-4 py-2 w-48 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200">
                                <option value="">pilih</option>
                                <?php foreach ($lmbdata->result() as $lm): ?>
                                    <option value="<?= $lm->id_lembaga ?>"><?= $lm->nama ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <!-- Pencarian -->
                        <div class="hidden md:block relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="search" class="pl-10 pr-4 py-2 w-64 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="<?= $this->session->userdata('db_selected') ?>">
                        </div>
                    <?php } else { ?>
                        <div class="hidden md:block relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="search" class="pl-10 pr-4 py-2 w-64 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari...">
                        </div>
                    <?php } ?>

                    <!-- Academic Session Selector -->
                    <?php
                    $list_semesters = $this->db->select('s.*, t.nama_tahun')
                        ->from('semester s')
                        ->join('tahun_ajaran t', 's.id_tahun = t.id_tahun')
                        ->order_by('t.nama_tahun', 'DESC')
                        ->order_by('s.nama_semester', 'DESC')
                        ->get()
                        ->result();
                    $current_sem_id = $this->session->userdata('id_semester_aktif');
                    ?>
                    <?php if (!empty($list_semesters)): ?>
                        <div class="hidden sm:block relative">
                            <select onchange="gantiSesiAkademik(this.value)"
                                class="px-3 py-1.5 bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500 max-w-[220px]">
                                <?php foreach ($list_semesters as $ls): ?>
                                    <option value="<?= $ls->id_semester ?>" <?= $ls->id_semester == $current_sem_id ? 'selected' : '' ?> class="text-gray-800 dark:text-gray-200">
                                        <?= $ls->nama_tahun ?> (<?= $ls->nama_semester ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Dark/Light Mode Toggle -->
                    <button id="themeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i id="themeIcon" class="fas fa-moon text-lg"></i>
                    </button>

                    <!-- Profil Pengguna -->
                    <div class="relative">
                        <button id="profileBtn" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                <span class="font-semibold text-primary-800 dark:text-primary-200"><?= inisial($this->session->userdata('nama_user')) ?></span>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium"><?= $this->session->userdata('nama_user') ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= $this->session->userdata('level') ?></p>
                            </div>
                            <i class="fas fa-chevron-down text-xs hidden md:block"></i>
                        </button>

                        <!-- Dropdown Profil -->
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                            <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i> Profil Saya
                            </a>
                            <?php if ($this->session->userdata('level') === 'admin' || $this->session->userdata('level') === 'super_admin'): ?>
                                <a href="<?= base_url('setting') ?>" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-cog mr-2"></i> Pengaturan
                                </a>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level') === 'super_admin'): ?>
                                <a href="<?= base_url('sistem') ?>" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-cogs mr-2"></i> Sistem
                                </a>
                            <?php endif; ?>
                            <hr class="my-2 border-gray-200 dark:border-gray-700">
                            <a href="<?= base_url('auth/logout') ?>" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 tbl-confirm" value="Anda akan keluar aplikasi">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex flex-1">

            <!-- Sidebar (Mobile) -->
            <aside id="sidebar" class="fixed left-0 top-16 bottom-0 z-40 w-64
                    bg-white dark:bg-gray-800 flux-shadow
                    transform transition-transform duration-300
                    <?= !empty($hideSidebar) ? '-translate-x-full' : 'md:translate-x-0' ?>">

                <div class="h-full overflow-y-auto">
                    <div class="px-4 py-6">
                        <!-- Menu Navigasi -->
                        <nav class="space-y-1">
                            <a href="<?= base_url() ?>" class="sidebar-item <?= $menu == 'home' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>
                            <?php if ($this->session->userdata('level') === 'guru'): ?>
                                <a href="<?= base_url('kbm/absensi') ?>" class="sidebar-item <?= $menu == 'kbm' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-book-open mr-3"></i>
                                    Absensi KBM
                                </a>
                                <a href="<?= base_url('kbm/hasil') ?>" class="sidebar-item <?= $menu == 'hasil' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-list mr-3"></i>
                                    Hasil Absensi
                                </a>
                            <?php endif ?>

                            <?php if ($this->session->userdata('level') === 'super_admin'): ?>
                                <!-- Sinkron Data -->
                                <div class="master-data-dropdown">
                                    <button data-dropdown-toggle class="sidebar-item <?= $menu == 'sinkron' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-user-graduate mr-3"></i>
                                            Sinkronisasi Data
                                        </div>
                                        <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                    </button>

                                    <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'sinkron' ? 'hidden' : '' ?>">
                                        <div class="pl-5 pr-4 py-2 space-y-1">
                                            <a href="<?= base_url('sinkron/guru') ?>" class="flex items-center sidebar-item <?= $sub == 'sinc_guru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Sinc. Guru
                                            </a>
                                            <a href="<?= base_url('sinkron/siswa') ?>" class="flex items-center sidebar-item <?= $sub == 'sinc_siswa' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Sinc. Siswa
                                            </a>
                                            <a href="<?= base_url('sinkron/lembaga') ?>" class="flex items-center sidebar-item <?= $sub == 'sinc_lembaga' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Sinc. Lembaga
                                            </a>
                                            <a href="<?= base_url('sinkron/mapel') ?>" class="flex items-center sidebar-item <?= $sub == 'sinc_mapel' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Sinc. Mapel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>

                            <?php if ($this->session->userdata('level') === 'admin' || $this->session->userdata('level') === 'super_admin'): ?>
                                <!-- Master Data Dropdown -->
                                <div class="master-data-dropdown">
                                    <button data-dropdown-toggle class="sidebar-item <?= $menu == 'master' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-database mr-3"></i>
                                            Master Data
                                        </div>
                                        <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                    </button>

                                    <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'master' ? 'hidden' : '' ?>">
                                        <div class="pl-5 pr-4 py-2 space-y-1">
                                            <a href="<?= base_url('guru') ?>" class="flex items-center sidebar-item <?= $sub == 'guru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Data Guru
                                            </a>
                                            <a href="<?= base_url('siswa') ?>" class="flex items-center sidebar-item <?= $sub == 'siswa' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Data Siswa
                                            </a>
                                            <a href="<?= base_url('mapel') ?>" class="flex items-center sidebar-item <?= $sub == 'mapel' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Data Mata Pelajaran
                                            </a>
                                            <a href="<?= base_url('kelas') ?>" class="flex items-center sidebar-item <?= $sub == 'kelas' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Data Kelas
                                            </a>
                                            <a href="<?= base_url('user') ?>" class="flex items-center sidebar-item <?= $sub == 'user' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Data Users
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- jadwal Data Dropdown -->
                                <div class="master-data-dropdown">
                                    <button data-dropdown-toggle class="sidebar-item <?= $menu == 'jadwal' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-3"></i>
                                            Jadwal
                                        </div>
                                        <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                    </button>

                                    <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'jadwal' ? 'hidden' : '' ?>">
                                        <div class="pl-5 pr-4 py-2 space-y-1">
                                            <a href="<?= base_url('jadwal') ?>" class="flex items-center sidebar-item <?= $sub == 'jadwal' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Jadwal Pelajaran
                                            </a>
                                            <a href="<?= base_url('piket') ?>" class="flex items-center sidebar-item <?= $sub == 'piket' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Jadwal Guru Piket
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- <a href="<?= base_url('jadwal') ?>" class="sidebar-item <?= $sub == 'jadwal' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-calendar-alt mr-3"></i>
                                    Jadwal Pelajaran
                                </a> -->
                            <?php endif ?>

                            <!-- Absensi Guru -->
                            <div class="master-data-dropdown">
                                <button data-dropdown-toggle class="sidebar-item <?= $menu == 'absensiguru' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center">
                                        <i class="fas fa-chalkboard-teacher mr-3"></i>
                                        Absensi Guru
                                    </div>
                                    <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>

                                <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'absensiguru' ? 'hidden' : '' ?>">
                                    <div class="pl-5 pr-4 py-2 space-y-1">
                                        <a href="<?= base_url('absensiguru/pembiasaan') ?>" class="flex items-center sidebar-item <?= $sub == 'absensiguru_pembiasaan' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Pembiasaan
                                        </a>
                                        <a href="<?= base_url('kehadiranguru') ?>" class="flex items-center sidebar-item <?= $sub == 'kehadiranguru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Kehadiran
                                        </a>
                                        <a href="<?= base_url('mengajar') ?>" class="flex items-center sidebar-item <?= $sub == 'mengajar' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Mengajar
                                        </a>
                                        <a href="<?= base_url('keaktifanguru') ?>" class="flex items-center sidebar-item <?= $sub == 'keaktifanguru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Keaktifan Guru
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Absensi Siswa -->
                            <div class="master-data-dropdown">
                                <button data-dropdown-toggle class="sidebar-item <?= $menu == 'absensisiswa' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-graduate mr-3"></i>
                                        Absensi Siswa
                                    </div>
                                    <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>

                                <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'absensisiswa' ? 'hidden' : '' ?>">
                                    <div class="pl-5 pr-4 py-2 space-y-1">
                                        <a href="<?= base_url('kbm/pembiasaan_siswa') ?>" class="flex items-center sidebar-item <?= $sub == 'pembiasaan_siswa' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Pembiasaan
                                        </a>
                                        <a href="<?= base_url('kbm/control') ?>" class="flex items-center sidebar-item <?= $sub == 'kbm' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            KBM
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->session->userdata('level') === 'admin' || $this->session->userdata('level') === 'super_admin'): ?>
                                <!-- Laporan -->
                                <div class="master-data-dropdown">
                                    <button data-dropdown-toggle class="sidebar-item <?= $menu == 'rekap' ? 'active' : '' ?> flex items-center justify-between w-full px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-chart-bar mr-3"></i>
                                            Laporan
                                        </div>
                                        <i data-dropdown-icon class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                    </button>

                                    <div data-dropdown-menu class="dropdown-menu open <?= $menu != 'rekap' ? 'hidden' : '' ?>">
                                        <div class="pl-5 pr-4 py-2 space-y-1">
                                            <a href="<?= base_url('rekap/pembiasaan_guru') ?>" class="flex items-center sidebar-item <?= $sub == 'pembiasaan_guru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Pembiasaan Guru
                                            </a>
                                            <a href="<?= base_url('rekap/kehadiran_guru') ?>" class="flex items-center sidebar-item <?= $sub == 'kehadiran_guru' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Kehadiran Guru
                                            </a>
                                            <a href="<?= base_url('rekap/jam_mengajar') ?>" class="flex items-center sidebar-item <?= $sub == 'jam_mengajar' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Jam Mengajar Guru
                                            </a>
                                            <a href="<?= base_url('rekap/pembiasaan_siswa') ?>" class="flex items-center sidebar-item <?= $sub == 'pembiasaan_siswa' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                Pembiasaan Siswa
                                            </a>
                                            <a href="<?= base_url('rekap/kbm_siswa') ?>" class="flex items-center sidebar-item <?= $sub == 'kbm_siswa' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                                KBM Siswa
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($this->session->userdata('level') === 'admin' || $this->session->userdata('level') === 'super_admin'): ?>
                                    <a href="<?= base_url('setting') ?>" class="sidebar-item <?= $menu == 'setting' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <i class="fas fa-cog mr-3"></i>
                                        Pengaturan
                                    </a>
                                <?php endif; ?>
                                <?php if ($this->session->userdata('level') === 'super_admin'): ?>
                                    <a href="<?= base_url('sistem') ?>" class="sidebar-item <?= $menu == 'sistem' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <i class="fas fa-cogs mr-3"></i>
                                        Sistem
                                    </a>
                                <?php endif; ?>
                            <?php endif ?>
                        </nav>

                        <!-- Divider -->
                        <hr class="my-3 border-gray-200 dark:border-gray-700">

                        <?php
                        $lmb = $this->db->query("SELECT lembaga.nama FROM lembaga JOIN user WHERE lembaga.id_lembaga=user.id_lembaga AND user.id_user = '$this->iduser' ")->row();
                        ?>
                        <!-- Info Sekolah -->
                        <div class="px-4">
                            <h3 class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-3">Info Sekolah</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar-day mr-2 text-primary-500"></i>
                                    <span><?= tanggal_indo(date('Y-m-d'), true) ?></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-clock mr-2 text-primary-500"></i>
                                    <span><?= $lmb->nama ?></span>
                                </div>
                                <!-- <div class="flex items-center text-sm">
                                    <i class="fas fa-users mr-2 text-primary-500"></i>
                                    <span>1.245 Siswa Aktif</span>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Overlay untuk mobile sidebar -->
            <div id="sidebarOverlay" class="fixed top-16 left-0 right-0 bottom-0 bg-black bg-opacity-50 z-30 hidden"></div>


            <main id="mainContent"
                class="flex-1 p-4 transition-all duration-300 <?= empty($hideSidebar) ? 'md:ml-64' : 'ml-0' ?>">
                <div class="flash-data" data-flashdata="<?= $this->session->flashdata('ok') ?>"></div>
                <div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('error') ?>"></div>
                <div class="flash-data-warning" data-flashdata="<?= $this->session->flashdata('warning') ?>"></div>