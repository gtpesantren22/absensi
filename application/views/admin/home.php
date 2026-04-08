<?php $this->load->view('admin/head'); ?>

    <!-- Header Dashboard -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Dashboard Absensi</h2>
        <p class="text-gray-600 dark:text-gray-400">Selamat pagi, Admin! Berikut ringkasan absensi hari ini.</p>
    </div>

    <div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <!-- KIRI : Identitas Sekolah -->
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <div class="flex h-16 w-16 items-center justify-center rounded-xl
                        bg-emerald-100 dark:bg-emerald-900
                        text-emerald-700 dark:text-emerald-300
                        font-bold text-xl">
                    <?= $sekolah->nickname ?>
                </div>

                <div>
                    <h1 class="text-lg font-semibold text-gray-800 dark:text-slate-100">
                        <?= $sekolah->nama ?>
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400">
                        <?= $sekolah->alamat ?>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-slate-500">
                        Tahun Ajaran 2025 / 2026
                    </p>
                </div>
            </div>

            <!-- KANAN : User Login -->
            <div class="flex flex-col sm:flex-row gap-4 sm:items-center">

                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200">
                        <?= $this->session->userdata('nama_user') ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                        <?= $this->session->userdata('level') ?>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full
                             bg-sky-100 dark:bg-sky-900
                             px-3 py-1 text-xs font-semibold
                             text-sky-700 dark:text-sky-300">
                        <?= $this->session->userdata('level') ?>
                    </span>

                    <span class="inline-flex items-center rounded-full
                             bg-emerald-100 dark:bg-emerald-900
                             px-3 py-1 text-xs font-semibold
                             text-emerald-700 dark:text-emerald-300">
                        Aktif
                    </span>
                </div>

            </div>

        </div>

    </div>

    <div class="max-w-xl mx-auto mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-4">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Kehadiran Hari Ini
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?= tanggal_indo(date('d-m-Y'), true) ?>
                    </p>
                </div>
                <?php if ($hadir): ?>
                    <span class="px-3 py-1 text-sm rounded-full 
                bg-green-100 text-green-700 
                dark:bg-green-900 dark:text-green-300">
                        Hadir
                    </span>
                <?php endif ?>
            </div>

            <!-- ABSENSI DATANG -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-800 dark:text-gray-100">
                        Absensi Datang
                    </h4>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                        Waktu
                    </span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-gray-500 dark:text-gray-400">Jam Kehadiran</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">
                        <?= $hadir ? date('H:i', strtotime($hadir->waktu)) : '--' ?>
                    </span>
                </div>

                <?php if (!$hadir): ?>
                    <!-- Tombol muncul jika belum absen -->
                    <button onclick="window.location.href='<?= base_url() ?>qrcode/scan/masuk'"
                        class="w-full py-2 rounded-lg bg-primary-600 hover:bg-primary-700 
                       text-white font-medium transition">
                        Absen Datang
                    </button>
                <?php endif ?>
            </div>

            <?php if ($hadir): ?>
                <!-- ABSENSI PULANG -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-800 dark:text-gray-100">
                            Absensi Pulang
                        </h4>
                        <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                            Waktu
                        </span>
                    </div>

                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-500 dark:text-gray-400">Jam Kepulangan</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">
                            <?= $hadir->pulang != null ? date('H:i', strtotime($hadir->pulang)) : '--' ?>
                        </span>
                    </div>
                    <?php if ($hadir && $hadir->pulang === null): ?>
                        <button onclick="window.location.href='<?= base_url() ?>qrcode/scan/pulang'"
                            class="w-full py-2 rounded-lg bg-primary-600 hover:bg-primary-700 
                       text-white font-medium transition">
                            Absen Pulang
                        </button>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Statistik Guru -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data Guru</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $jumlah_guru->jumlah ?></h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        <i class="fas fa-users mr-1"></i> Total jumlah guru
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-xl text-blue-600 dark:text-blue-300"></i>
                </div>
            </div>
        </div>

        <!-- Statistik Siswa -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data Siswa</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $jumlah_siswa->jumlah ?></h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        <i class="fas fa-users mr-1"></i> Total jumlah siswa
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-xl text-green-600 dark:text-green-300"></i>
                </div>
            </div>
        </div>

        <!-- Statistik Terlambat -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data Kelas</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $jumlah_kelas->jumlah ?></h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        <i class="fas fa-building mr-1"></i> jumlah kelas disekolah
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                    <i class="fas fa-building text-xl text-amber-600 dark:text-amber-300"></i>
                </div>
            </div>
        </div>

        <!-- Statistik Kegiatan -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data Jadwal</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $jumlah_jadwal->jumlah ?></h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                        <i class="fas fa-calendar-check mr-1"></i> jumlah jadwal terinput
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-xl text-purple-600 dark:text-purple-300"></i>
                </div>
            </div>
        </div>
    </div>



    <!-- Chart & Tabel -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafik Kehadiran -->
        <!-- <div class="bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold">Statistik Kehadiran Minggu Ini</h3>
                <select class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option>Minggu Ini</option>
                    <option>Bulan Ini</option>
                    <option>Tahun Ini</option>
                </select>
            </div>
            <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                    <div class="flex items-end justify-center space-x-2 mb-4">
                        <div class="w-8 h-40 bg-primary-500 rounded-t"></div>
                        <div class="w-8 h-32 bg-primary-400 rounded-t"></div>
                        <div class="w-8 h-48 bg-primary-600 rounded-t"></div>
                        <div class="w-8 h-36 bg-primary-300 rounded-t"></div>
                        <div class="w-8 h-44 bg-primary-500 rounded-t"></div>
                        <div class="w-8 h-28 bg-primary-400 rounded-t"></div>
                        <div class="w-8 h-40 bg-primary-600 rounded-t"></div>
                    </div>
                    <div class="flex justify-center space-x-8 text-sm text-gray-500">
                        <span>Sen</span>
                        <span>Sel</span>
                        <span>Rab</span>
                        <span>Kam</span>
                        <span>Jum</span>
                        <span>Sab</span>
                        <span>Min</span>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex justify-between text-sm">
                <div class="text-center">
                    <p class="font-medium">95%</p>
                    <p class="text-gray-500 dark:text-gray-400">Rata-rata Guru</p>
                </div>
                <div class="text-center">
                    <p class="font-medium">92%</p>
                    <p class="text-gray-500 dark:text-gray-400">Rata-rata Siswa</p>
                </div>
                <div class="text-center">
                    <p class="font-medium">18</p>
                    <p class="text-gray-500 dark:text-gray-400">Rata-rata Terlambat</p>
                </div>
            </div>
        </div> -->

        <!-- Status Absensi -->
        <!-- <div class="bg-white dark:bg-gray-800 rounded-xl p-5 flux-shadow">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold">Status Absensi per Kelas</h3>
                <a href="#" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Lihat detail</a>
            </div>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>XII IPA 1</span>
                        <span class="font-medium">95%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 95%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>XII IPS 2</span>
                        <span class="font-medium">88%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-400 h-2 rounded-full" style="width: 88%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>XI IPA 3</span>
                        <span class="font-medium">92%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>X IPS 1</span>
                        <span class="font-medium">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>X Bahasa</span>
                        <span class="font-medium">97%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 97%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                <h4 class="font-medium mb-3">Aksi Cepat</h4>
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="flex items-center justify-center p-3 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-800/30">
                        <i class="fas fa-qrcode mr-2"></i>
                        <span>QR Absensi</span>
                    </a>
                    <a href="#" class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-800/30">
                        <i class="fas fa-file-export mr-2"></i>
                        <span>Ekspor Laporan</span>
                    </a>
                </div>
            </div>
        </div> -->
    </div>

<?php $this->load->view('admin/foot'); ?>