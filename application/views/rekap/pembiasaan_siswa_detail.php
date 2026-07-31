<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?= base_url('rekap/pembiasaan_siswa') ?>" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition">
                <i class="fas fa-chevron-left"></i> Kembali ke Rekap
            </a>
        </div>
        <h2 class="text-2xl font-bold">Detail Absensi Pembiasaan Siswa</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Tanggal: <strong class="text-slate-800 dark:text-white"><?= tanggal_indo($date) ?></strong> 
            | Lembaga: <strong class="text-slate-800 dark:text-white"><?= $lembaga_selected ? htmlspecialchars($lembaga_selected->nama) : 'Semua Lembaga' ?></strong>
        </p>
    </div>
    
    <div class="flex items-center gap-2 mt-4 md:mt-0">
        <a href="<?= base_url('rekap/pembiasaan_siswa_excel/' . $date . '/' . ($id_lembaga ?: '')) ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Success/Error Alert messages -->
<?php if ($this->session->flashdata('ok')): ?>
    <div class="mb-5 p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-900 font-bold text-sm">
        <?= $this->session->flashdata('ok') ?>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="mb-5 p-4 rounded-xl bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-300 dark:border-rose-900 font-bold text-sm">
        <?= $this->session->flashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Tabel Log Siswa -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-350 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-center font-extrabold w-16">No</th>
                    <th class="px-6 py-4 text-left font-extrabold">Nama Siswa</th>
                    <th class="px-6 py-4 text-center font-extrabold">NIS</th>
                    <th class="px-6 py-4 text-center font-extrabold">Jenis Kelamin</th>
                    <th class="px-6 py-4 text-center font-extrabold">Jam Masuk</th>
                    <th class="px-6 py-4 text-center font-extrabold">Jam Pulang</th>
                    <th class="px-6 py-4 text-center font-extrabold w-32">Status</th>
                    <th class="px-6 py-4 text-center font-extrabold w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                            Tidak ada siswa yang tercatat melakukan absensi pembiasaan pada tanggal ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($list as $row): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4 text-center text-slate-450 font-medium">
                                <?= $no++ ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-850 dark:text-white">
                                <?= htmlspecialchars($row->nama) ?>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-xs">
                                <?= htmlspecialchars($row->nis ?: '-') ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?= htmlspecialchars($row->jkl) ?>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                                <?= $row->jam_masuk ?: '-' ?>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-blue-600 dark:text-blue-400 font-semibold">
                                <?= $row->jam_pulang ?: '-' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $status = strtolower($row->ket);
                                if ($status === 'hadir') {
                                    $badge = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400';
                                } elseif ($status === 'sakit') {
                                    $badge = 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-400';
                                } elseif ($status === 'izin') {
                                    $badge = 'bg-sky-100 text-sky-800 dark:bg-sky-950/40 dark:text-sky-400';
                                } else {
                                    $badge = 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-455';
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $badge ?>">
                                    <?= $row->ket ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode([
                                    'id' => $row->id_pembiasaan_siswa,
                                    'nama' => $row->nama,
                                    'ket' => $row->ket,
                                    'jam_masuk' => $row->jam_masuk,
                                    'jam_pulang' => $row->jam_pulang
                                ])) ?>)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-lg transition border border-slate-350 dark:border-slate-600">
                                    <i class="fas fa-edit text-[10px]"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDIT DATA ABSENSI -->
<div id="editModal" class="fixed inset-0 z-55 flex items-center justify-center p-4 bg-black/60 hidden backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-850 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Edit Absensi Siswa</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form action="<?= base_url('rekap/pembiasaan_siswa_update') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id_pembiasaan_siswa" id="modal_id">
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Siswa</label>
                <input type="text" id="modal_nama" readonly
                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-750 text-slate-500 dark:text-slate-400 rounded-xl focus:outline-none font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Kehadiran</label>
                <select name="ket" id="modal_ket" required
                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500 focus:outline-none font-bold">
                    <option value="hadir">HADIR</option>
                    <option value="sakit">SAKIT</option>
                    <option value="izin">IZIN</option>
                    <option value="alpha">ALPHA</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="modal_jam_masuk" step="1"
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jam Pulang</label>
                    <input type="time" name="jam_pulang" id="modal_jam_pulang" step="1"
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono font-bold">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2 border border-slate-300 dark:border-slate-650 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    function openEditModal(data) {
        document.getElementById('modal_id').value = data.id;
        document.getElementById('modal_nama').value = data.nama;
        document.getElementById('modal_ket').value = data.ket.toLowerCase();
        document.getElementById('modal_jam_masuk').value = data.jam_masuk || '';
        document.getElementById('modal_jam_pulang').value = data.jam_pulang || '';

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
    }
</script>
