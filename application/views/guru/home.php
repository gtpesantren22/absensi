<?php $this->load->view('admin/head'); ?>

<!-- Header Dashboard -->
<div class="mb-6">
    <h2 class="text-2xl font-bold">Dashboard Guru</h2>
    <!-- <p class="text-gray-600 dark:text-gray-400">Selamat pagi, Bpk/Ibu guru! Ahlan wasahlan.</p> -->
</div>

<div class="max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-md p-5 border border-slate-200 dark:border-slate-800 mb-4">
    <div class="flex items-center gap-4 justify-between">

        <!-- Avatar -->
        <div class="relative">
            <div class="w-14 h-14 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl font-bold">
                <?= inisial($guru->nama ?? 'Nama saya') ?>
            </div>
            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-white dark:border-slate-900 rounded-full"></span>
        </div>

        <!-- Identitas -->
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">
                <?= $guru->nama ?? 'Nama Guru' ?>
            </h3>

            <p class="text-sm text-slate-500 dark:text-slate-400">
                <?= $lembaga->nama ?? '-' ?>
            </p>

            <p class="text-sm text-slate-600 dark:text-slate-300">
                <?= $guru->mapel ?? 'Guru Pengajar' ?>
            </p>
        </div>
        <span class="ml-2 text-xs px-2 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300">
            Guru
        </span>

    </div>

    <!-- Divider -->
    <!-- <hr class="my-4 border-slate-200 dark:border-slate-700"> -->

    <!-- Info bawah -->
    <!-- <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-slate-500 dark:text-slate-400">Status</p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                    Aktif
                </span>
            </div>

            <div>
                <p class="text-slate-500 dark:text-slate-400">Last Login</p>
                <p class="font-medium text-slate-700 dark:text-slate-200">
                    <?= $user->last_login ?> WIB
                </p>
            </div>
        </div> -->
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



<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Jadwal KBM Saya</h3>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
        <!-- Header -->

        <!-- Body -->
        <div class="p-4">

            <div class="overflow-x-auto">
                <?php foreach ($lmb as $lmb):
                    $jadwalKelas = $this->db->query("SELECT * FROM jadwal WHERE hari = '$days' AND id_guru = '$idguru' AND id_lembaga = '$lmb->id_lembaga' ORDER BY jam_dari ASC ");
                ?>
                    <table class="min-w-full text-sm border border-slate-200 dark:border-slate-700">

                        <!-- Head Kelas -->
                        <thead class="bg-slate-100 dark:bg-slate-800">
                            <tr>
                                <th colspan="2"
                                    class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-200 border-b border-slate-200 dark:border-slate-700">
                                    <?= $lmb->nama_lembaga ?>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $days = date('l');
                            $dateDays = date('Y-m-d');
                            foreach ($jadwalKelas->result() as $hasil) :
                                $dtl = $this->db->query("SELECT * FROM jadwal_dtl WHERE id_jadwal = '$hasil->id_jadwal' ")->row();

                                $queryCek = $this->db->query("
                                    SELECT *
                                    FROM harian
                                    WHERE tanggal = '$dateDays'
                                      AND id_kelas = '$hasil->id_kelas'
                                      AND id_guru = '$guru->id_guru'
                                      AND dari = '$hasil->jam_dari'
                                      AND id_lembaga = '$lmb->id_lembaga'
                                ");
                            ?>
                                <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                    onclick="window.location.href='<?= site_url('kbm/absensi') ?>'">
                                    <!-- Jam -->
                                    <td class="px-3 py-2 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        <?= $hasil->jam_dari . ' - ' . $hasil->jam_sampai ?>
                                    </td>

                                    <!-- Guru & Mapel -->
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-700 dark:text-slate-100">
                                                <?= $dtl->id_kelas ?>
                                            </span>

                                            <?php if ($queryCek->row()) { ?>
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                                    ✓ sudah
                                                </span>
                                            <?php } else { ?>
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                    ✕ belum
                                                </span>
                                            <?php } ?>
                                        </div>

                                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            <?= $dtl->id_mapel ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                <?php endforeach ?>
            </div>

        </div>
    </div>

</div>


<?php $this->load->view('admin/foot'); ?>