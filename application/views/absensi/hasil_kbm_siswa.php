<?php $this->load->view('admin/head'); ?>


<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Data Absensi KBM Siswa</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi kbm siswa</p>
    </div>

</div>


<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Hasil KBM siswa <?= tanggal_indo(date('d-m-Y'), true) ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
        <!-- Header -->

        <!-- Body -->
        <div class="p-2">

            <div class="overflow-x-auto">

                <div class="bg-white dark:bg-slate-800 px-2 py-2">
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
                                    <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition" onclick="window.location.href='<?= site_url('kbm/absensi') ?>'">
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
                                                    <div class="flex items-center gap-2">

                                                        <!-- BUTTON EDIT -->
                                                        <a href="<?= base_url('kbm/edit/' . $queryCek->row('kode')) ?>"
                                                            class="inline-flex items-center text-xs px-3 py-1 rounded-md
                                                                bg-blue-100 text-blue-700
                                                                hover:bg-blue-200
                                                                dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800
                                                                transition">
                                                            ✎
                                                        </a>

                                                        <!-- BUTTON HAPUS -->
                                                        <a href="<?= base_url('kbm/hapus_hasil/' . $queryCek->row('kode')) ?>"
                                                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                            class="inline-flex items-center text-xs px-3 py-1 rounded-md
                                                                bg-red-100 text-red-700
                                                                hover:bg-red-200
                                                                dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800
                                                                transition">
                                                            🗑
                                                        </a>

                                                    </div>

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


    <!-- Pagination -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">

    </div>
</div>

<?php $this->load->view('admin/foot'); ?>