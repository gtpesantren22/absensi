<?php $this->load->view('admin/head'); ?>

<main class="flex-1 p-4 md:p-6 overflow-y-auto">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Rekap Absensi KBM Siswa</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi kehadiran kbm siswa</p>
        </div>
        `
        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

        </div>
    </div>


    <!-- Tabel Data -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <!-- <h3 class="font-bold text-lg">Jadwal KBM Siswa hari ini <?= tanggal_indo(date('d-m-Y'), true) ?></h3> -->
        </div>

        <div class="bg-white dark:bg-slate-900 shadow-sm border border-slate-200/70 dark:border-slate-700 p-4 mb-5">
            <form class="flex flex-wrap items-end gap-3" method="post" action="<?= base_url('rekap/export_kbm_siswa') ?>">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">
                        Dari
                    </label>
                    <input type="date" id="tgl_dari" name="tgl_dari"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800
                       border border-slate-300 dark:border-slate-600
                       text-slate-700 dark:text-slate-200
                       focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">
                        Sampai
                    </label>
                    <input type="date" id="tgl_sampai" name="tgl_sampai"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800
                       border border-slate-300 dark:border-slate-600
                       text-slate-700 dark:text-slate-200
                       focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- <button type="button" onclick="loadRekap()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                   text-white text-sm font-semibold shadow-sm">
                    Tampilkan
                </button> -->

                <button type="submit"
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-700
                   text-white text-sm font-semibold shadow-sm">
                    Export Excel
                </button>
            </form>
        </div>

        <!-- <div class="bg-white dark:bg-slate-900 shadow-sm border border-slate-200/70 dark:border-slate-700">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                       
                        <tr>
                            <th rowspan="2" class="px-4 py-3 text-center align-middle">No</th>
                            <th rowspan="2" class="px-4 py-3 text-left align-middle">Nama Guru</th>
                            <th rowspan="2" class="px-4 py-3 text-center align-middle">Wajib</th>
                            <th rowspan="2" class="px-4 py-3 text-center align-middle">Hadir</th>
                            <th colspan="3" class="px-4 py-3 text-center">
                                Tidak Hadir
                            </th>
                            <th rowspan="2" class="px-4 py-3 text-center align-middle">%</th>
                        </tr>

                        <tr class="text-xs uppercase tracking-wide">
                            <th class="px-4 py-2 text-center text-blue-600 dark:text-blue-400">
                                Total
                            </th>
                            <th class="px-4 py-2 text-center text-yellow-600 dark:text-yellow-400">
                                Izin
                            </th>
                            <th class="px-4 py-2 text-center text-red-600 dark:text-red-400">
                                Alpha
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700" id="rekap-body">
                    </tbody>
                </table>
            </div>
        </div> -->


        <!-- Pagination -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">

        </div>
    </div>


</main>
<?php $this->load->view('admin/foot'); ?>