<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Rekap Absensi Pembiasaan Siswa</h2>
        <p class="text-gray-600 dark:text-gray-400">Pantau rekapitulasi data absensi pembiasaan harian siswa berdasarkan tanggal.</p>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form id="filterForm" class="flex flex-wrap items-end gap-4" onsubmit="event.preventDefault(); loadRekap();">
        <div>
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                Dari Tanggal
            </label>
            <input type="date" id="tgl_dari" name="tgl_dari"
                   value="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                   class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-650 text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-primary-500 focus:outline-none rounded-lg">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                Sampai Tanggal
            </label>
            <input type="date" id="tgl_sampai" name="tgl_sampai"
                   value="<?= date('Y-m-d') ?>"
                   class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-650 text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-primary-500 focus:outline-none rounded-lg">
        </div>

        <?php if ($this->session->userdata('level') === 'superadmin'): ?>
        <div>
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                Pilih Lembaga
            </label>
            <select id="id_lembaga" name="id_lembaga"
                    class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-650 text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-primary-500 focus:outline-none rounded-lg font-medium">
                <option value="">Semua Lembaga</option>
                <?php foreach ($lembagas as $l): ?>
                    <option value="<?= $l->id_lembaga ?>"><?= htmlspecialchars($l->nama) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" id="id_lembaga" name="id_lembaga" value="<?= $this->id_lembaga ?>">
        <?php endif; ?>

        <button type="submit"
                class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            Tampilkan
        </button>
    </form>
</div>

<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-350 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-center font-extrabold w-16">No</th>
                    <th class="px-6 py-4 text-left font-extrabold">Tanggal Absensi</th>
                    <th class="px-6 py-4 text-center font-extrabold">Total Siswa Absen</th>
                    <th class="px-6 py-4 text-center font-extrabold w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80" id="rekap-body">
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    function loadRekap() {
        const dari = document.getElementById('tgl_dari').value;
        const sampai = document.getElementById('tgl_sampai').value;
        const id_lembaga = document.getElementById('id_lembaga').value;

        const tbody = document.getElementById('rekap-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                </td>
            </tr>
        `;

        fetch("<?= site_url('rekap/ajaxRekapPembiasaanSiswa') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `dari=${dari}&sampai=${sampai}&id_lembaga=${id_lembaga}`
        })
        .then(res => res.json())
        .then(res => {
            renderRekap(res.data);
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-rose-500">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data dari server.
                    </td>
                </tr>
            `;
        });
    }

    function renderRekap(data) {
        const tbody = document.getElementById('rekap-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                        Tidak ada data absensi pembiasaan pada periode terpilih.
                    </td>
                </tr>
            `;
            return;
        }

        const id_lembaga = document.getElementById('id_lembaga').value;

        data.forEach((row, index) => {
            const dateStr = formatDateIndo(row.tanggal);
            const detailUrl = `<?= site_url('rekap/pembiasaan_siswa_detail') ?>/${row.tanggal}/${id_lembaga}`;
            const deleteUrl = `<?= site_url('rekap/pembiasaan_siswa_delete') ?>/${row.tanggal}/${id_lembaga}`;

            tbody.innerHTML += `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 text-center text-slate-450 font-medium">
                        ${index + 1}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                        ${dateStr}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-750 dark:bg-primary-950/40 dark:text-primary-300">
                            ${row.total_siswa} Siswa
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center flex items-center justify-center gap-2">
                        <a href="${detailUrl}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                            <i class="fas fa-eye text-[10px]"></i> Detail
                        </a>
                        <button onclick="confirmDelete('${deleteUrl}', '${dateStr}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-150 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400 hover:dark:bg-rose-900/20 text-xs font-bold rounded-lg transition shadow-sm border border-transparent hover:border-rose-200">
                            <i class="fas fa-trash text-[10px]"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function formatDateIndo(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return parts[2] + ' ' + months[parseInt(parts[1])] + ' ' + parts[0];
    }

    function confirmDelete(url, dateStr) {
        Swal.fire({
            title: 'Hapus Rekap Absensi?',
            text: `Semua data kehadiran pembiasaan siswa pada tanggal ${dateStr} akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Auto load on page load
    window.addEventListener("load", function() {
        loadRekap();
    });
</script>
