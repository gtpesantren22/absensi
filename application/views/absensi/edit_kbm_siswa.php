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
        <h3 class="font-bold text-lg">Edit absensi siswa <?= tanggal_indo(date('d-m-Y'), true) ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
        <!-- Header -->

        <!-- Body -->
        <div class="p-2">

            <div class="overflow-x-auto">

                <div class="bg-white dark:bg-slate-800 px-2 py-2">

                    <?= form_open('kbm/edit_multiple_data', ['class' => 'space-y-5']) ?>

                    <!-- Nama Guru -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Nama Guru
                        </label>
                        <input
                            type="text"
                            name="guru"
                            value="<?= $guru->nama ?? 'Nama gurunya' ?>"
                            readonly
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 
                                    bg-slate-100 dark:bg-slate-700 
                                    text-slate-800 dark:text-slate-200
                                    px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Tabel Ajax -->
                    <div class="mt-4">
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-800">
                            <!-- Hidden data -->

                            <input type="hidden" name="dari" value="<?= $listdata->row('dari') ?>">
                            <input type="hidden" name="sampai" value="<?= $listdata->row('sampai') ?>">
                            <input type="hidden" name="kode" value="<?= $listdata->row('kode') ?>">
                            <input type="hidden" name="guru" value="<?= $listdata->row('guru') ?>">
                            <input type="hidden" name="mapel" value="<?= $listdata->row('mapel') ?>">
                            <input type="hidden" name="kelas" value="<?= $listdata->row('kelas') ?>">

                            <table class="min-w-full border-collapse text-sm text-slate-700 dark:text-slate-200">
                                <tbody>

                                    <!-- Mapel -->
                                    <tr>
                                        <td colspan="2" class="bg-sky-600 dark:bg-sky-700 px-4 py-3 font-semibold text-white">
                                            Mapel : <?= $listdata->row('mapel') ?>
                                        </td>
                                    </tr>

                                    <!-- Jam -->
                                    <tr>
                                        <td colspan="2" class="bg-sky-600 dark:bg-sky-700 px-4 py-2 font-semibold text-white">
                                            Jam ke : <?= $listdata->row('dari') . ' - ' . $listdata->row('sampai') ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $no = 1;
                                    foreach ($listdata->result() as $row) :
                                    ?>
                                        <tr class="border-b border-gray-200 dark:border-slate-700 
							hover:bg-gray-50 dark:hover:bg-slate-700/50">

                                            <!-- Nama -->
                                            <td class="px-4 py-2 font-medium text-gray-700 dark:text-slate-200">
                                                <?= $row->nama_siswa ?>
                                            </td>

                                            <!-- Absensi -->
                                            <td class="px-4 py-1">
                                                <input type="hidden" name="data[<?= $no ?>][id]" value="<?= $row->id_harian ?>">

                                                <div class="flex flex-wrap gap-2">

                                                    <!-- Hadir -->
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="data[<?= $no ?>][ket]" value="hadir" <?= $row->ket === 'hadir' ? 'checked' : '' ?> class="peer hidden">
                                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-emerald-500 peer-checked:text-white">
                                                            H
                                                        </span>
                                                    </label>

                                                    <!-- Sakit -->
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="data[<?= $no ?>][ket]" value="sakit" <?= $row->ket === 'sakit' ? 'checked' : '' ?> class="peer hidden">
                                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-yellow-400 peer-checked:text-white">
                                                            S
                                                        </span>
                                                    </label>

                                                    <!-- Izin -->
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="data[<?= $no ?>][ket]" value="izin" <?= $row->ket === 'izin' ? 'checked' : '' ?> class="peer hidden">
                                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-blue-500 peer-checked:text-white">
                                                            I
                                                        </span>
                                                    </label>

                                                    <!-- Alpha -->
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="data[<?= $no ?>][ket]" value="alpha" <?= $row->ket === 'alpha' ? 'checked' : '' ?> class="peer hidden">
                                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-red-500 peer-checked:text-white">
                                                            A
                                                        </span>
                                                    </label>

                                                    <!-- Telat -->
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="data[<?= $no ?>][ket]" value="telat" <?= $row->ket === 'telat' ? 'checked' : '' ?> class="peer hidden">
                                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-purple-500 peer-checked:text-white">
                                                            T
                                                        </span>
                                                    </label>

                                                </div>
                                            </td>

                                        </tr>
                                    <?php
                                        $no++;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Materi -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Materi yang disampaikan
                        </label>
                        <textarea
                            name="isi"
                            rows="3"
                            placeholder="Input materi yang diajarkan"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                                    bg-white dark:bg-slate-700
                                    text-slate-800 dark:text-slate-200
                                    px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= $materi->isi ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2 rounded-lg
                                    bg-emerald-600 hover:bg-emerald-700
                                    text-white font-semibold
                                    shadow transition">
                            💾 Simpan Absensi
                        </button>
                    </div>

                    <?= form_close() ?>

                </div>


            </div>


        </div>
    </div>


    <!-- Pagination -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">

    </div>
</div>



<?php $this->load->view('admin/foot'); ?>