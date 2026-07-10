<?php $this->load->view('admin/head'); ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Proses Kenaikan Kelas</h2>
        <p class="text-gray-600 dark:text-gray-400">Naikkan kelas siswa secara massal per rombongan belajar</p>
    </div>
</div>

<form action="<?= base_url('kelas/proses_kenaikan') ?>" method="POST" onsubmit="return confirmProses(event)">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Kiri: Pilih Sumber (Kelas Asal) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-lg mb-4 flex items-center text-primary-600 dark:text-primary-400">
                <i class="fas fa-sign-out-alt mr-2 rotate-180"></i>
                1. Pilih Kelas Asal
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Tahun Ajaran Asal</label>
                    <select id="tahun_asal" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none" onchange="loadKelasAsal(this.value)">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta->id_tahun ?>" <?= $ta->id_tahun == $this->session->userdata('id_tahun_aktif') ? 'selected' : '' ?>>
                                <?= $ta->nama_tahun ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Kelas Asal</label>
                    <select id="kelas_asal" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none" onchange="loadSiswa(this.value)" disabled>
                        <option value="">-- Pilih Kelas --</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tengah: Pilih Tujuan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-lg mb-4 flex items-center text-primary-600 dark:text-primary-400">
                <i class="fas fa-sign-in-alt mr-2"></i>
                2. Pilih Kelas Tujuan
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Tahun Ajaran Tujuan</label>
                    <select id="tahun_tujuan" name="id_tahun_tujuan" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none" onchange="loadKelasTujuan(this.value)">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta->id_tahun ?>">
                                <?= $ta->nama_tahun ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Kelas Tujuan</label>
                    <select id="kelas_tujuan" name="id_kelas_tujuan" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none" disabled>
                        <option value="">-- Pilih Kelas --</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Kanan: Aksi Cepat -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-lg mb-4 flex items-center text-primary-600 dark:text-primary-400">
                    <i class="fas fa-info-circle mr-2"></i>
                    3. Proses Kenaikan
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Sistem akan menyalin pendaftaran rombel siswa terpilih ke kelas tujuan pada Tahun Ajaran Baru. Riwayat kelas lama tidak akan dihapus.
                </p>
                <div class="bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-200 dark:border-yellow-900 rounded-lg p-3 text-xs text-yellow-800 dark:text-yellow-300 mb-4">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Tahun Ajaran Tujuan dan Kelas Tujuan sudah sesuai.
                </div>
            </div>
            
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition shadow-sm">
                <i class="fas fa-graduation-cap mr-2 text-lg"></i>
                Proses Kenaikan Kelas
            </button>
        </div>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
            <h3 class="font-bold text-lg">Daftar Siswa Kelas Asal</h3>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="checkAll(true)" class="text-xs font-semibold px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 rounded text-gray-700 dark:text-gray-200 transition">
                    Pilih Semua
                </button>
                <button type="button" onclick="checkAll(false)" class="text-xs font-semibold px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 rounded text-gray-700 dark:text-gray-200 transition">
                    Hapus Semua
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/30 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 w-12 text-center">Pilih</th>
                        <th class="p-4">NISN</th>
                        <th class="p-4">Nama Lengkap</th>
                    </tr>
                </thead>
                <tbody id="siswa_list" class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            Silakan pilih Kelas Asal terlebih dahulu
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<?php $this->load->view('admin/foot'); ?>

<script>
    // Inisialisasi awal saat halaman diload
    document.addEventListener("DOMContentLoaded", function() {
        const defaultTahunAsal = document.getElementById("tahun_asal").value;
        if(defaultTahunAsal) {
            loadKelasAsal(defaultTahunAsal);
        }
    });

    function loadKelasAsal(id_tahun) {
        const selectKelas = document.getElementById("kelas_asal");
        selectKelas.innerHTML = '<option value="">-- Memuat Kelas --</option>';
        selectKelas.disabled = true;

        if(!id_tahun) {
            selectKelas.innerHTML = '<option value="">-- Pilih Tahun Ajaran --</option>';
            return;
        }

        fetch("<?= base_url('kelas/get_kelas_by_tahun/') ?>" + id_tahun)
            .then(res => res.json())
            .then(data => {
                selectKelas.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                if(data.length > 0) {
                    data.forEach(k => {
                        selectKelas.innerHTML += `<option value="${k.id_kelas}">${k.nama}</option>`;
                    });
                    selectKelas.disabled = false;
                } else {
                    selectKelas.innerHTML = '<option value="">Tidak ada kelas</option>';
                }
                document.getElementById("siswa_list").innerHTML = `
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            Silakan pilih Kelas Asal terlebih dahulu
                        </td>
                    </tr>
                `;
            });
    }

    function loadKelasTujuan(id_tahun) {
        const selectKelas = document.getElementById("kelas_tujuan");
        selectKelas.innerHTML = '<option value="">-- Memuat Kelas --</option>';
        selectKelas.disabled = true;

        if(!id_tahun) {
            selectKelas.innerHTML = '<option value="">-- Pilih Tahun Ajaran --</option>';
            return;
        }

        fetch("<?= base_url('kelas/get_kelas_by_tahun/') ?>" + id_tahun)
            .then(res => res.json())
            .then(data => {
                selectKelas.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                if(data.length > 0) {
                    data.forEach(k => {
                        selectKelas.innerHTML += `<option value="${k.id_kelas}">${k.nama}</option>`;
                    });
                    selectKelas.disabled = false;
                } else {
                    selectKelas.innerHTML = '<option value="">Tidak ada kelas</option>';
                }
            });
    }

    function loadSiswa(id_kelas) {
        const tbody = document.getElementById("siswa_list");
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data siswa...
                </td>
            </tr>
        `;

        if(!id_kelas) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">
                        Silakan pilih Kelas Asal terlebih dahulu
                    </td>
                </tr>
            `;
            return;
        }

        fetch("<?= base_url('kelas/get_siswa_by_kelas/') ?>" + id_kelas)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(s => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 text-center">
                                    <input type="checkbox" name="id_siswa[]" value="${s.id_siswa}" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                </td>
                                <td class="p-4 font-mono text-xs text-gray-500 dark:text-gray-400">${s.nisn || '-'}</td>
                                <td class="p-4 font-medium text-gray-800 dark:text-gray-200">${s.nama}</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada siswa terdaftar di kelas ini
                            </td>
                        </tr>
                    `;
                }
            });
    }

    function checkAll(status) {
        const checkboxes = document.querySelectorAll('input[name="id_siswa[]"]');
        checkboxes.forEach(cb => cb.checked = status);
    }

    function confirmProses(e) {
        e.preventDefault();
        const selectKelasTujuan = document.getElementById("kelas_tujuan");
        const checkboxes = document.querySelectorAll('input[name="id_siswa[]"]:checked');

        if(!selectKelasTujuan.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Lengkap',
                text: 'Silakan pilih Kelas Tujuan terlebih dahulu!'
            });
            return false;
        }

        if(checkboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Siswa Belum Dipilih',
                text: 'Silakan pilih minimal satu siswa yang akan dinaikkan kelas!'
            });
            return false;
        }

        Swal.fire({
            title: 'Konfirmasi Kenaikan Kelas',
            text: `Anda yakin ingin memproses kenaikan kelas untuk ${checkboxes.length} siswa ke kelas "${selectKelasTujuan.options[selectKelasTujuan.selectedIndex].text}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Proses Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
</script>
