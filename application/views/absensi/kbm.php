<?php $this->load->view('admin/head'); ?>


<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Data Absensi KBM Siswa</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi kbm siswa</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

    </div>
</div>


<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Jadwal KBM Siswa hari ini <?= tanggal_indo(date('d-m-Y'), true) ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
        <!-- Header -->

        <!-- Body -->
        <div class="p-4">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-200 dark:border-slate-700">
                    <?php
                    foreach ($kelas->result() as $kelas) :
                        $days = $harini;
                        $dtlkelas = $this->db->query("SELECT * FROM kelas WHERE id_kelas = '$kelas->id_kelas'")->row();
                        $jadwalKelas = $this->db->query("SELECT jadwal.*, guru.nama as nama_guru FROM jadwal LEFT JOIN guru ON jadwal.id_guru=guru.id_guru WHERE jadwal.hari = '$days' AND jadwal.id_kelas = '$kelas->id_kelas' AND jadwal.id_lembaga = '$id_lembaga' ORDER BY jadwal.jam_dari ASC ");
                    ?>

                        <!-- Head Kelas -->
                        <thead class="bg-slate-100 dark:bg-slate-800">
                            <tr>
                                <th colspan="2"
                                    class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-200 border-b border-slate-200 dark:border-slate-700">
                                    <?= $dtlkelas->nama ?>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($jadwalKelas->result() as $hasil) :
                                $nmmapel = $this->db->query("SELECT * FROM mapel WHERE id_mapel = $hasil->id_mapel")->row();
                                $queryCek = $this->db->query("
                                    SELECT *
                                    FROM harian
                                    WHERE tanggal = '$dateDays'
                                      AND id_kelas = '$hasil->id_kelas'
                                      AND id_guru = '$hasil->id_guru'
                                      AND dari = '$hasil->jam_dari'
                                      AND id_lembaga = '$hasil->id_lembaga'
                                ");
                            ?>
                                <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <!-- Jam -->
                                    <td class="px-3 py-2 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        <?= $hasil->jam_dari . ' - ' . $hasil->jam_sampai ?>
                                    </td>

                                    <!-- Guru & Mapel -->
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-700 dark:text-slate-100">
                                                <?= $hasil->nama_guru ?>
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
                                            <?= $nmmapel->nama ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    <?php endforeach ?>
                </table>
            </div>

        </div>
    </div>

</div>


<?php $this->load->view('admin/foot'); ?>