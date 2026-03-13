<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Sekolah</title>
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
                    <button id="sidebarToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
                            <i class="fas fa-school text-white"></i>
                        </div>
                        <h1 class="text-xl font-bold">SekolahCerdas</h1>
                    </div>
                </div>

                <!-- Right Side Header -->
                <div class="flex items-center space-x-4">
                    <!-- Pencarian -->
                    <div class="hidden md:block relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="search" class="pl-10 pr-4 py-2 w-64 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari...">
                    </div>


                    <!-- Dark/Light Mode Toggle -->
                    <button id="themeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i id="themeIcon" class="fas fa-moon text-lg"></i>
                    </button>

                    <!-- Profil Pengguna -->
                    <div class="relative">
                        <button id="profileBtn" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                <span class="font-semibold text-primary-800 dark:text-primary-200">AD</span>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium">Admin Sekolah</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs hidden md:block"></i>
                        </button>

                        <!-- Dropdown Profil -->
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i> Profil Saya
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-2"></i> Pengaturan
                            </a>
                            <hr class="my-2 border-gray-200 dark:border-gray-700">
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex flex-1">

            <aside id="sidebar"
                class="fixed inset-y-0 left-0 z-40 w-64
                bg-white dark:bg-gray-800
                transform transition-transform duration-300
                -translate-x-full md:translate-x-0
                md:static">
                <div class="h-full overflow-y-auto">
                    <div class="px-4 py-6">
                        <!-- Menu Navigasi -->
                        <nav class="space-y-1">
                            <a href="<?= base_url() ?>" class="sidebar-item <?= $menu == 'home' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>

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
                                    </div>
                                </div>
                            </div>

                            <a href="<?= base_url('jadwal') ?>" class="sidebar-item <?= $sub == 'jadwal' ? 'active' : '' ?> flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                Jadwal Pelajaran
                            </a>

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
                                        <a href="#" class="flex items-center sidebar-item <?= $sub == '-' ? 'active' : '' ?> px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
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
                                        <a href="data-guru.html" class="flex items-center sidebar-item px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            KBM Siswa
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <a href="#" class="sidebar-item flex items-center px-4 py-3 text-sm font-medium border-l-4 border-transparent hover:border-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-3"></i>
                                Pengaturan
                            </a>
                        </nav>

                        <!-- Divider -->
                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        <!-- Info Sekolah -->
                        <div class="px-4">
                            <h3 class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-3">Info Sekolah</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar-day mr-2 text-primary-500"></i>
                                    <span>Senin, 15 April 2024</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-clock mr-2 text-primary-500"></i>
                                    <span>07:30 - 15:30 WIB</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-users mr-2 text-primary-500"></i>
                                    <span>1.245 Siswa Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Overlay untuk mobile sidebar -->
            <div id="sidebarOverlay"
                class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden">
            </div>
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('ok') ?>"></div>
            <div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('error') ?>"></div>