<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Data Absensi Pembiasaan Siswa</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola hasil absensi harian pembiasaan siswa</p>
    </div>
</div>

<!-- Filter Card -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow p-5 mb-6 border border-gray-200 dark:border-gray-700">
    <h3 class="font-bold text-lg mb-4 flex items-center">
        <i class="fas fa-filter mr-2 text-primary-500"></i> Filter Pencarian
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <?php if ($this->session->userdata('level') === 'superadmin'): ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Lembaga</label>
                <select id="filter_lembaga" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Lembaga</option>
                    <?php foreach ($lembagas as $lmb): ?>
                        <option value="<?= $lmb->id_lembaga ?>"><?= htmlspecialchars($lmb->nama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Dari Tanggal</label>
            <input type="date" id="tgl_dari" value="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sampai Tanggal</label>
            <input type="date" id="tgl_sampai" value="<?= date('Y-m-d') ?>"
                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <div class="md:col-span-3 mt-2 flex justify-end">
            <button onclick="loadHasil()" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition shadow">
                Filter Data
            </button>
        </div>
    </div>
</div>

<!-- Tabel Rekap -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6 border border-gray-200 dark:border-gray-700">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="font-bold text-lg">Hasil Absensi Harian</h3>
    </div>

    <div class="overflow-x-auto px-4 py-2">
        <table class="w-full text-left" id="table-rekap">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-200 dark:border-gray-700">
                    <th class="py-3 px-4 font-semibold">No</th>
                    <th class="py-3 px-4 font-semibold">Tanggal Absensi</th>
                    <th class="py-3 px-4 font-semibold text-center">Jumlah Santri Hadir</th>
                    <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="hasil-body">
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                        <p>Memuat rekap data...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    function loadHasil() {
        const dari = document.getElementById("tgl_dari").value;
        const sampai = document.getElementById("tgl_sampai").value;
        const filterLembaga = document.getElementById("filter_lembaga");
        const id_lembaga = filterLembaga ? filterLembaga.value : '';

        const tbody = document.getElementById("hasil-body");
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                    <p>Memuat rekap data...</p>
                </td>
            </tr>
        `;

        $.post("<?= site_url('kbm/ajaxHasilPembiasaanSiswa') ?>", {
            dari: dari,
            sampai: sampai,
            id_lembaga: id_lembaga
        }, function(res) {
            let r = typeof res === 'string' ? JSON.parse(res) : res;
            renderHasil(r.data);
        }).fail(function() {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-rose-500">
                        <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                        <p>Gagal memuat data dari server.</p>
                    </td>
                </tr>
            `;
        });
    }

    function renderHasil(data) {
        const tbody = document.getElementById("hasil-body");
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-400">
                        <i class="fas fa-calendar-alt text-xl mb-2"></i>
                        <p>Tidak ada data absensi untuk rentang tanggal terpilih.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let no = 1;
        data.forEach(row => {
            const dateStr = formatDateIndo(row.tanggal);
            const detailUrl = `<?= site_url('kbm/pembiasaan_siswa_detail') ?>/${row.tanggal}`;
            const deleteUrl = `<?= site_url('kbm/pembiasaan_siswa_delete') ?>/${row.tanggal}`;

            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">
                    <td class="py-3.5 px-4 font-medium text-gray-550 dark:text-gray-400">${no++}</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800 dark:text-white">${dateStr}</td>
                    <td class="py-3.5 px-4 text-center font-extrabold text-primary-600 dark:text-primary-400">
                        <span class="inline-block px-3 py-1 bg-primary-50 dark:bg-primary-950/40 rounded-full">
                            ${row.total_siswa} Santri
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right space-x-1">
                        <a href="${detailUrl}" class="inline-flex items-center px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-bold text-xs transition shadow">
                            <i class="fas fa-eye mr-1.5"></i> Detail
                        </a>
                        <button onclick="confirmDelete('${deleteUrl}', '${dateStr}')" class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-xs transition shadow">
                            <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function confirmDelete(url, dateLabel) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: `Semua data absensi santri pada tanggal ${dateLabel} akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function formatDateIndo(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return parts[2] + ' ' + months[parseInt(parts[1])] + ' ' + parts[0];
    }

    // Load initial data
    window.addEventListener("load", loadHasil);
</script>
