<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div class="flex items-center space-x-3">
        <a href="<?= site_url('kbm/pembiasaan_siswa') ?>" class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-full transition text-gray-700 dark:text-white">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold">Detail Absensi Pembiasaan</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Tanggal: <strong><?= tanggal_indo($date) ?></strong> | Lembaga: <strong><?= $lembaga_selected ? htmlspecialchars($lembaga_selected->nama) : 'Semua Lembaga' ?></strong>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-4 md:mt-0">
        <a href="<?= site_url('kbm/download_pembiasaan_siswa_screen/' . $date . ($id_lembaga ? '/' . $id_lembaga : '')) ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm flex items-center shadow transition">
            <i class="fas fa-download mr-2 text-base"></i> Download Screen
        </a>
        <a href="<?= site_url('kbm/pembiasaan_siswa_excel/' . $date . ($id_lembaga ? '/' . $id_lembaga : '')) ?>" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm flex items-center shadow transition">
            <i class="fas fa-file-excel mr-2 text-base"></i> Export Excel
        </a>
    </div>
</div>

<!-- Pencarian & Stat -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow p-4 mb-6 border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-4">
        <span class="text-sm font-semibold text-gray-500">Total Hadir: <strong class="text-gray-800 dark:text-white"><?= count($list) ?> Santri</strong></span>
    </div>
    
    <div class="relative w-full md:w-72">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input type="search" id="detail-search" oninput="filterSiswa()" 
               class="pl-10 pr-4 py-2.5 w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" 
               placeholder="Cari nama santri atau NIS...">
    </div>
</div>

<!-- Tabel Rincian -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto px-4 py-2">
        <table class="w-full text-left" id="table-detail">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-200 dark:border-gray-700">
                    <th class="py-3 px-4 font-semibold">No</th>
                    <th class="py-3 px-4 font-semibold">Nama Santri</th>
                    <th class="py-3 px-4 font-semibold">NIS</th>
                    <th class="py-3 px-4 font-semibold text-center">Gender</th>
                    <th class="py-3 px-4 font-semibold text-center">Jam Masuk</th>
                    <th class="py-3 px-4 font-semibold text-center">Jam Pulang</th>
                    <th class="py-3 px-4 font-semibold text-center">Status</th>
                    <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="detail-body">
                <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400">
                            <i class="fas fa-user-slash text-xl mb-2"></i>
                            <p>Tidak ada santri yang terabsen pada tanggal ini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = 1;
                    foreach ($list as $row): 
                    ?>
                        <tr class="siswa-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150"
                            data-nama="<?= strtolower($row->nama) ?>"
                            data-nis="<?= strtolower($row->nis ?: '') ?>">
                            <td class="py-3 px-4 text-gray-550 dark:text-gray-400 font-medium"><?= $no++ ?></td>
                            <td class="py-3 px-4 font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($row->nama) ?></td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300 font-mono"><?= htmlspecialchars($row->nis ?: '-') ?></td>
                            <td class="py-3 px-4 text-center text-gray-650 dark:text-gray-350"><?= htmlspecialchars($row->jkl) ?></td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-emerald-600 dark:text-emerald-450"><?= $row->jam_masuk ?: '-' ?></td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-blue-600 dark:text-blue-450"><?= $row->jam_pulang ?: '-' ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php
                                $status = strtolower($row->ket);
                                if ($status === 'hadir') {
                                    $badge = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300';
                                } elseif ($status === 'sakit') {
                                    $badge = 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300';
                                } elseif ($status === 'izin') {
                                    $badge = 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300';
                                } else {
                                    $badge = 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300';
                                }
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase <?= $badge ?>">
                                    <?= htmlspecialchars($row->ket) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button type="button" 
                                        onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)"
                                        class="px-2.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold text-xs shadow transition">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Absensi (Inline Modal menggunakan HTML & Tailwind) -->
<div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all w-full max-w-md p-6 border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="fas fa-user-edit text-primary-500"></i> Edit Absensi Santri
            </h3>
            
            <form action="<?= site_url('kbm/pembiasaan_siswa_update') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="id_pembiasaan_siswa" id="modal_id">
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Nama Santri</label>
                    <input type="text" id="modal_nama" disabled 
                           class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-450 dark:text-gray-550 uppercase tracking-wide mb-1">Status Kehadiran</label>
                    <select name="ket" id="modal_ket" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Hadir">Hadir</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Alpha">Alpha</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-450 dark:text-gray-555 uppercase tracking-wide mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="modal_jam_masuk" step="1"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-450 dark:text-gray-555 uppercase tracking-wide mb-1">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="modal_jam_pulang" step="1"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-lg shadow transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    function filterSiswa() {
        const query = document.getElementById("detail-search").value.toLowerCase();
        const rows = document.querySelectorAll(".siswa-row");

        rows.forEach(row => {
            const nama = row.getAttribute("data-nama");
            const nis = row.getAttribute("data-nis");
            
            if (nama.includes(query) || nis.includes(query)) {
                row.classList.remove("hidden");
            } else {
                row.classList.add("hidden");
            }
        });
    }

    const editModal = document.getElementById("edit-modal");

    function openEditModal(studentData) {
        document.getElementById("modal_id").value = studentData.id_pembiasaan_siswa;
        document.getElementById("modal_nama").value = studentData.nama;
        document.getElementById("modal_ket").value = studentData.ket;
        document.getElementById("modal_jam_masuk").value = studentData.jam_masuk || '';
        document.getElementById("modal_jam_pulang").value = studentData.jam_pulang || '';

        editModal.classList.remove("hidden");
    }

    function closeEditModal() {
        editModal.classList.add("hidden");
    }
</script>
