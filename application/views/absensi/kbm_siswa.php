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
        <h3 class="font-bold text-lg">Input absensi siswa <?= tanggal_indo(date('d-m-Y'), true) ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
        <!-- Header -->

        <!-- Body -->
        <div class="p-2">

            <div class="overflow-x-auto">

                <div class="bg-white dark:bg-slate-800 px-2 py-2">

                    <?= form_open('kbm/save_multiple_data', ['class' => 'space-y-5']) ?>

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

                    <!-- Pilih Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Kelas
                        </label>
                        <select
                            name="kelas"
                            id="selectKelas"
                            required
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                                    bg-white dark:bg-slate-700
                                    text-slate-800 dark:text-slate-200
                                    px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">- pilih kelas -</option>
                            <?php foreach ($kelas as $gr) : ?>
                                <option value="<?= $gr['id_jadwal'] ?>">
                                    <?= $gr['kelas'] . ' (' . $gr['jam_dari'] . '-' . $gr['jam_sampai'] . ')' ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Tabel Ajax -->
                    <div id="tabelData" class="mt-4">
                        <!-- Hasil Ajax -->
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
                                    px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
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
<script>
    document.getElementById('selectKelas').addEventListener('change', e => {
        fetch('<?= site_url('kbm/cariKelas') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_jadwal: e.target.value
                })
            })
            .then(res => res.text())
            .then(res => {
                document.getElementById('tabelData').innerHTML = res;
            });
    });
</script>