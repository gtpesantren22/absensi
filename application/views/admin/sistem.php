<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">System Administration</h2>
        <p class="text-gray-600 dark:text-gray-400">Pengaturan tahun pelajaran, semester, backup database, dan integrasi API</p>
    </div>
</div>

<!-- Grid Tabel Master Tahun Ajaran & Semester -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Card Tahun Ajaran -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-blue-500"></i>
                <span>Tahun Ajaran</span>
            </h3>
            <button onclick="openModalTahun()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Tahun
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-slate-700/50">
                        <th class="py-3 px-4">Nama Tahun</th>
                        <th class="py-3 px-4">Tanggal Mulai</th>
                        <th class="py-3 px-4">Tanggal Selesai</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    <?php if (empty($tahuns)): ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">Belum ada data tahun ajaran.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tahuns as $t): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                                <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200"><?= $t->nama_tahun ?></td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400"><?= $t->tanggal_mulai ? date('d-m-Y', strtotime($t->tanggal_mulai)) : '-' ?></td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400"><?= $t->tanggal_selesai ? date('d-m-Y', strtotime($t->tanggal_selesai)) : '-' ?></td>
                                <td class="py-3 px-4 text-center">
                                    <?php if ($t->is_active == 1): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                                    <?php else: ?>
                                        <a href="<?= base_url('sistem/toggle_tahun_active/'.$t->id_tahun) ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300 hover:bg-blue-100 hover:text-blue-800">Set Aktif</a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                    <button onclick="openModalTahun(<?= htmlspecialchars(json_encode($t)) ?>)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400"><i class="fas fa-edit"></i></button>
                                    <a href="<?= base_url('sistem/hapus_tahun/'.$t->id_tahun) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini? Semua semester di dalamnya akan terhapus.')" class="text-red-600 hover:text-red-900 dark:text-red-400"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card Semester -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-indigo-500"></i>
                <span>Semester</span>
            </h3>
            <button onclick="openModalSemester()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Semester
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-slate-700/50">
                        <th class="py-3 px-4">Tahun</th>
                        <th class="py-3 px-4">Semester</th>
                        <th class="py-3 px-4">Rentang Waktu</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    <?php if (empty($semesters)): ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">Belum ada data semester.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($semesters as $s): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                                <td class="py-3 px-4 font-semibold text-gray-800 dark:text-gray-200"><?= $s->nama_tahun ?></td>
                                <td class="py-3 px-4 text-gray-800 dark:text-gray-200"><?= $s->nama_semester ?></td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                    <?= ($s->tanggal_mulai ? date('d-m-Y', strtotime($s->tanggal_mulai)) : '-') . ' s/d ' . ($s->tanggal_selesai ? date('d-m-Y', strtotime($s->tanggal_selesai)) : '-') ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <?php if ($s->is_active == 1): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                                    <?php else: ?>
                                        <a href="<?= base_url('sistem/toggle_semester_active/'.$s->id_semester) ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300 hover:bg-blue-100 hover:text-blue-800">Set Aktif</a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                    <button onclick="openModalSemester(<?= htmlspecialchars(json_encode($s)) ?>)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400"><i class="fas fa-edit"></i></button>
                                    <a href="<?= base_url('sistem/hapus_semester/'.$s->id_semester) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus semester ini?')" class="text-red-600 hover:text-red-900 dark:text-red-400"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Card Backup Database (MIGRATED) -->
<div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
        <i class="fas fa-database text-green-500"></i>
        <span>Backup Database</span>
    </h3>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Unduh salinan cadangan seluruh database sistem dalam format SQL. Backup ini berisi struktur tabel dan semua data saat ini.
            </p>
            <p class="text-xs text-amber-500 dark:text-amber-400 mt-1">
                <i class="fas fa-exclamation-triangle mr-1"></i> Harap simpan file hasil backup ini di tempat yang aman dan rahasia.
            </p>
        </div>
        <div>
            <a href="<?= base_url('sistem/backup_sql') ?>" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-download"></i>
                <span>Download Backup SQL</span>
            </a>
        </div>
    </div>
</div>

<!-- Card API Token & Endpoint (MIGRATED) -->
<div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
        <i class="fas fa-key text-yellow-500"></i>
        <span>API Token &amp; Endpoint</span>
    </h3>
    <form action="<?= base_url('sistem/save_api_token') ?>" method="post">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">API Bearer Token / API Key</label>
                <input type="text" name="api_token" value="<?= htmlspecialchars($api_token, ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200" required>
            </div>
            <div>
                <button type="submit" class="w-full px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition duration-200 shadow-sm hover:shadow-md">
                    Simpan Token
                </button>
            </div>
        </div>
    </form>
    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Endpoint URL:</p>
        <div class="space-y-3">
            <div>
                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">1. Rekap Semua Guru</span>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 select-all font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">
                    <span>GET <?= base_url('api/jam_mengajar') ?></span>
                </div>
            </div>
            <div>
                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">2. Detail Guru Berdasarkan ID</span>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 select-all font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">
                    <span>GET <?= base_url('api/guru/[id_guru]') ?></span>
                </div>
            </div>
            <div>
                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">3. Rekap Rombel &amp; Jumlah Anggota (per Lembaga)</span>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 select-all font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">
                    <span>GET <?= base_url('api/rombel') ?></span>
                </div>
            </div>
            <div>
                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">4. Data Jadwal Pelajaran (dengan Filter)</span>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 select-all font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">
                    <span>GET <?= base_url('api/jadwal?id_lembaga=&amp;hari=&amp;id_kelas=&amp;id_guru=') ?></span>
                </div>
            </div>
        </div>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-3">
            Gunakan Header: <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded font-mono font-semibold">Authorization: Bearer [Token]</code> atau <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded font-mono font-semibold">X-API-KEY: [Token]</code>, atau parameter query <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded font-mono font-semibold">?api_key=[Token]</code>.
        </p>
    </div>
</div>

<!-- Card Pengaturan Aplikasi -->
<div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
        <i class="fas fa-sliders-h text-primary-500"></i>
        <span>Pengaturan Aplikasi</span>
    </h3>
    <form action="<?= base_url('sistem/save_app_settings') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Aplikasi -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Aplikasi</label>
                <input type="text" name="app_name" value="<?= htmlspecialchars($app_name, ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200" required>
            </div>
            <!-- Logo Aplikasi -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Logo Aplikasi (Format Image)</label>
                <div class="flex items-center gap-4">
                    <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 p-1 bg-white flex items-center justify-center shrink-0">
                            <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="max-w-full max-h-full object-contain">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="app_logo" accept="image/*" class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none text-xs">
                </div>
            </div>
        </div>
        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition duration-200 shadow-sm hover:shadow-md">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>


<!-- Modal Tahun Ajaran -->
<div id="modalTahun" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-slate-900/80" onclick="closeModalTahun()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-slate-700">
            <form id="formTahun" action="" method="post">
                <div class="p-6">
                    <h3 id="modalTahunTitle" class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Tambah Tahun Ajaran</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Nama Tahun Ajaran</label>
                            <input type="text" name="nama_tahun" id="tahun_nama" placeholder="Contoh: 2025/2026" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tahun_mulai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tahun_selesai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" name="is_active" id="tahun_aktif" value="1" class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4">
                            <label for="tahun_aktif" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Set sebagai Tahun Ajaran aktif</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/30 px-6 py-4 flex justify-end gap-2">
                    <button type="button" onclick="closeModalTahun()" class="px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Semester -->
<div id="modalSemester" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-slate-900/80" onclick="closeModalSemester()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-slate-700">
            <form id="formSemester" action="" method="post">
                <div class="p-6">
                    <h3 id="modalSemesterTitle" class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Tambah Semester</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tahun Ajaran</label>
                            <select name="id_tahun" id="semester_tahun" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($tahuns as $t): ?>
                                    <option value="<?= $t->id_tahun ?>"><?= $t->nama_tahun ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Nama Semester</label>
                            <select name="nama_semester" id="semester_nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="semester_mulai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="semester_selesai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" name="is_active" id="semester_aktif" value="1" class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4">
                            <label for="semester_aktif" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Set sebagai Semester aktif</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/30 px-6 py-4 flex justify-end gap-2">
                    <button type="button" onclick="closeModalSemester()" class="px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal Tahun Ajaran functions
    function openModalTahun(data = null) {
        const modal = document.getElementById('modalTahun');
        const form = document.getElementById('formTahun');
        const title = document.getElementById('modalTahunTitle');
        const namaInput = document.getElementById('tahun_nama');
        const mulaiInput = document.getElementById('tahun_mulai');
        const selesaiInput = document.getElementById('tahun_selesai');
        const aktifCheck = document.getElementById('tahun_aktif');

        if (data) {
            title.textContent = 'Edit Tahun Ajaran';
            form.action = '<?= base_url("sistem/edit_tahun/") ?>' + data.id_tahun;
            namaInput.value = data.nama_tahun || '';
            mulaiInput.value = data.tanggal_mulai || '';
            selesaiInput.value = data.tanggal_selesai || '';
            aktifCheck.checked = (data.is_active == 1);
        } else {
            title.textContent = 'Tambah Tahun Ajaran';
            form.action = '<?= base_url("sistem/tambah_tahun") ?>';
            namaInput.value = '';
            mulaiInput.value = '';
            selesaiInput.value = '';
            aktifCheck.checked = false;
        }

        modal.classList.remove('hidden');
    }

    function closeModalTahun() {
        document.getElementById('modalTahun').classList.add('hidden');
    }

    // Modal Semester functions
    function openModalSemester(data = null) {
        const modal = document.getElementById('modalSemester');
        const form = document.getElementById('formSemester');
        const title = document.getElementById('modalSemesterTitle');
        const tahunSelect = document.getElementById('semester_tahun');
        const namaSelect = document.getElementById('semester_nama');
        const mulaiInput = document.getElementById('semester_mulai');
        const selesaiInput = document.getElementById('semester_selesai');
        const aktifCheck = document.getElementById('semester_aktif');

        if (data) {
            title.textContent = 'Edit Semester';
            form.action = '<?= base_url("sistem/edit_semester/") ?>' + data.id_semester;
            tahunSelect.value = data.id_tahun || '';
            namaSelect.value = data.nama_semester || 'Ganjil';
            mulaiInput.value = data.tanggal_mulai || '';
            selesaiInput.value = data.tanggal_selesai || '';
            aktifCheck.checked = (data.is_active == 1);
        } else {
            title.textContent = 'Tambah Semester';
            form.action = '<?= base_url("sistem/tambah_semester") ?>';
            tahunSelect.value = '';
            namaSelect.value = 'Ganjil';
            mulaiInput.value = '';
            selesaiInput.value = '';
            aktifCheck.checked = false;
        }

        modal.classList.remove('hidden');
    }

    function closeModalSemester() {
        document.getElementById('modalSemester').classList.add('hidden');
    }
</script>

<?php $this->load->view('admin/foot'); ?>
