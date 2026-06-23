<?php $this->load->view('admin/head'); ?>

<style>
    .item-jadwal {
        cursor: default !important;
        pointer-events: none !important;
    }
</style>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Full Jadwal Pelajaran</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman rekap seluruh jadwal pelajaran dan jam mengajar guru</p>
    </div>

    <div class="flex items-center space-x-2 mt-4 md:mt-0">
        <a href="<?= base_url('jadwal/export_excel') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center shadow transition-colors">
            <i class="fas fa-file-excel mr-2"></i>
            Export Excel
        </a>
        <a href="<?= base_url('jadwal') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center shadow transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Kelola Jadwal
        </a>
    </div>
</div>

<div class="space-y-8">
    <!-- JADWAL SEMUA HARI -->
    <div class="space-y-6">
        <?php 
        $daysOfWeek = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        foreach ($daysOfWeek as $eng => $ind): 
        ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow p-6 border border-gray-150 dark:border-gray-700">
                <h3 class="font-bold text-lg mb-4 text-gray-850 dark:text-white flex items-center">
                    <i class="fas fa-calendar-day mr-2 text-primary-500"></i>
                    Jadwal Hari <?= $ind ?>
                </h3>
                <div id="showJadwal_<?= $eng ?>" class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center p-8 text-gray-500 bg-white dark:bg-gray-800">
                        <i class="fas fa-spinner fa-spin text-2xl mr-2 text-primary-500"></i>
                        <span>Memuat Jadwal...</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- REKAP JAM MENGAJAR GURU -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow p-6 border border-gray-150 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-clock mr-2 text-primary-500"></i>
                Rekap Jam Mengajar Guru
            </h3>
            
            <!-- Search Guru -->
            <div class="relative mt-2 md:mt-0 w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" id="searchGuru" onkeyup="filterGurus()" placeholder="Cari guru..." class="pl-9 pr-4 py-2 w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            </div>
        </div>

        <!-- Tabel Rekap -->
        <?php if (empty($gurus)): ?>
            <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-6">Tidak ada data guru</p>
        <?php else: ?>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm border-collapse border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold">
                            <th class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-center w-12">No</th>
                            <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Nama Guru</th>
                            <?php foreach ($kelas as $k): ?>
                                <th class="py-3 px-2 border border-gray-200 dark:border-gray-700 text-center min-w-[80px]"><?= htmlspecialchars($k->nama) ?></th>
                            <?php endforeach; ?>
                            <th class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-center w-28 bg-gray-150 dark:bg-gray-850">Total Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php 
                        $no = 1;
                        foreach ($gurus as $g): 
                        ?>
                            <tr class="guru-row hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors" data-name="<?= htmlspecialchars($g->nama) ?>" data-code="<?= htmlspecialchars($g->kode_guru) ?>">
                                <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-center font-medium"><?= $no++ ?></td>
                                <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-gray-250">
                                    <!-- <div class="flex items-center">
                                        <span class="w-3.5 h-3.5 rounded-full inline-block border border-gray-300 dark:border-gray-650 shadow-sm shrink-0" style="background-color: <?= $g->warna ?: '#000' ?>;"></span>
                                        <div class="truncate">
                                            <span class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($g->kode_guru) ?></span>
                                        </div>
                                    </div> -->
                                    <span class="block leading-tight text-gray-900 dark:text-gray-200"><?= htmlspecialchars($g->nama) ?></span>
                                </td>
                                <?php foreach ($kelas as $k): 
                                    $jp = $jpMap[$g->id_guru][$k->id_kelas] ?? 0;
                                ?>
                                    <td class="py-3 px-2 border border-gray-200 dark:border-gray-700 text-center font-mono">
                                        <?= $jp > 0 ? '<span class="font-bold text-primary-600 dark:text-primary-400">' . $jp . ' JP</span>' : '<span class="text-gray-300 dark:text-gray-600">-</span>' ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-center bg-gray-50/50 dark:bg-gray-900/10">
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full <?= $g->total_jp > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-750 dark:text-gray-400' ?>">
                                        <?= $g->total_jp ?> JP
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    function loadJadwalAll() {
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        days.forEach(day => {
            fetch('<?= base_url('jadwal/fetch_jadwal/') ?>' + day)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('showJadwal_' + day).innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('showJadwal_' + day).innerHTML = `
                        <div class="p-6 text-red-500 text-center bg-white dark:bg-gray-800">
                            <i class="fas fa-exclamation-triangle text-xl mb-1 text-red-500"></i>
                            <p class="text-sm font-semibold">Gagal memuat jadwal</p>
                        </div>
                    `;
                });
        });
    }

    function filterGurus() {
        const query = document.getElementById('searchGuru').value.toLowerCase();
        document.querySelectorAll('.guru-row').forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const code = row.dataset.code.toLowerCase();
            if (name.includes(query) || code.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Load all schedules on page load
    window.addEventListener('DOMContentLoaded', () => {
        loadJadwalAll();
    });
</script>
