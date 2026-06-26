<?php $this->load->view('admin/head'); ?>

<style>
    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-weight: 600;
        color: white;
    }
</style>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Data Mapel</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data mapel</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
        <?php if ($this->session->userdata('level') === 'super_admin'): ?>
            <button onclick="openModal('tambahMapelModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Mapel
            </button>
        <?php else: ?>
            <button onclick="openPilihMapelModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Mapel
            </button>
        <?php endif; ?>
    </div>
</div>


<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Daftar Mapel</h3>

        <div class="flex items-center space-x-2 mt-2 md:mt-0">
            <div class="relative">
                <select id="perPage" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" id="search" class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari mapel...">
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto px-4">
        <table class="w-full" id="datatable">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                    <th onclick="sort('kode_mapel')" class="py-3 px-4 font-medium cursor-pointer">Kode Mapel</th>
                    <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama Mapel</th>
                    <th class="py-3 px-4 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tableBody">
                <!-- Baris Data 1 -->

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 md:mb-0">
            Menampilkan <span id="startRecord">1</span> sampai <span id="endRecord">10</span> dari <span id="totalRecords">100</span> entri
        </div>
        <div class="flex items-center space-x-2" id="pagination">
        </div>
    </div>
</div>

<!-- Modal Tambah Mapel -->
<div id="tambahMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold">Tambah Data Mapel</h3>
            <button onclick="closeModal('tambahMapelModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Tambah Mapel -->
        <div class="p-6">
            <form id="formTambahMapel" action="<?= base_url('mapel/add') ?>" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Kode Mapel</label>
                        <input type="text" name="kode_mapel" id="tambah_kode_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode mapel (opsional, otomatis dibuat jika kosong)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Nama Mapel</label>
                        <input type="text" name="nama" id="tambah_nama_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama mapel" required>
                    </div>

                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('tambahMapelModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit  -->
<div id="editMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold">Edit Data Mapel</h3>
            <button onclick="closeModal('editMapelModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Tambah Siswa -->
        <div class="p-6">
            <form id="formEditMapel" action="<?= base_url('mapel/update') ?>" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Kode Mapel</label>
                        <input type="text" name="kode_mapel" id="edit_kode_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode mapel (opsional, otomatis dibuat jika kosong)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Nama Mapel</label>
                        <input type="text" name="nama" id="edit_nama_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama mapel" required>
                    </div>

                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editMapelModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Mapel (untuk Lembaga) -->
<div id="pilihMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header Modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold">Pilih Mata Pelajaran</h3>
            <button onclick="closeModal('pilihMapelModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Pilih Mapel -->
        <form id="formPilihMapel" action="<?= base_url('mapel/save_selection') ?>" method="POST" class="flex flex-col flex-1 overflow-hidden">
            <div class="p-6 flex-1 overflow-y-auto min-h-[300px] max-h-[50vh]">
                <!-- Search Filter for Checklist -->
                <div class="mb-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchMasterMapel" class="pl-10 pr-4 py-2 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari mata pelajaran...">
                </div>

                <div class="mb-2 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>Daftar Mata Pelajaran Global</span>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="toggleSelectAll(true)" class="text-primary-600 hover:underline">Pilih Semua</button>
                        <span>•</span>
                        <button type="button" onclick="toggleSelectAll(false)" class="text-primary-600 hover:underline">Hapus Semua</button>
                    </div>
                </div>

                <!-- Checklist Container -->
                <div id="checklistMasterMapel" class="border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-150 dark:divide-gray-700 max-h-[300px] overflow-y-auto">
                    <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">Memuat mata pelajaran...</div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Centang mata pelajaran yang diajarkan di lembaga Anda. Setelah disimpan, hanya mata pelajaran yang dicentang yang akan aktif dan dapat digunakan dalam pembuatan jadwal.</p>
            </div>

            <!-- Footer Modal -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('pilihMapelModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 bg-white dark:bg-gray-800">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                    Simpan Pilihan
                </button>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>
<script>
    const userLevel = '<?= $this->session->userdata("level") ?>';

    let state = {
        page: 1,
        perPage: 10,
        search: '',
        sortBy: 'nama',
        sortDir: 'ASC',
        total: 0
    };

    function loadData() {
        const params = new URLSearchParams(state).toString();

        fetch(`<?= site_url('mapel/datatable') ?>?${params}`)
            .then(res => res.json())
            .then(res => {
                renderTable(res.data);
                renderPagination(res);
                state.total = res.total;
                info(state.perPage, state.page, state.total);
            });
    }

    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!Array.isArray(data)) return;

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada data mata pelajaran.
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(row => {
            let actionButtons = '';
            if (userLevel === 'super_admin') {
                actionButtons += `<button onclick="editData(${row.id_mapel})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs mr-1">Edit</button>`;
            }
            actionButtons += `<button data-id="${row.id_mapel}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs tombol-hapus">Hapus</button>`;

            tbody.innerHTML += `
            <tr class="border-b">
                <td class="p-2">
                    <span class="px-2 py-1 me-2 rounded-xl bg-blue-500 text-white font-mono text-xs">
                        ${row.kode_mapel}
                    </span>
                </td>
                <td class="p-2 text-sm text-gray-800 dark:text-gray-200"> 
                    ${row.nama}
                </td>
                <td class="p-2">
                    ${actionButtons}
                </td>
            </tr>
            `;
        });
    }

    let masterMapelData = [];

    function openPilihMapelModal() {
        const container = document.getElementById('checklistMasterMapel');
        container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat mata pelajaran...</div>';
        document.getElementById('searchMasterMapel').value = '';

        fetch('<?= base_url("mapel/get_available_master") ?>')
            .then(res => res.json())
            .then(data => {
                masterMapelData = data;
                renderChecklist(masterMapelData);
            })
            .catch(err => {
                container.innerHTML = '<div class="p-4 text-center text-sm text-red-500">Gagal memuat data</div>';
                console.error(err);
            });

        openModal('pilihMapelModal');
    }

    function renderChecklist(data) {
        const container = document.getElementById('checklistMasterMapel');
        container.innerHTML = '';

        if (data.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada mata pelajaran global yang tersedia.</div>';
            return;
        }

        data.forEach(item => {
            container.innerHTML += `
                <label class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer checklist-item" data-nama="${item.nama.toLowerCase()}" data-kode="${item.kode_mapel.toLowerCase()}">
                    <input type="checkbox" name="id_master_mapel[]" value="${item.id_master_mapel}" ${item.is_active ? 'checked' : ''} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-3">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">${item.nama}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">${item.kode_mapel}</div>
                    </div>
                </label>
            `;
        });
    }

    // Client-side search filter for checklist
    $(document).on('input', '#searchMasterMapel', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.checklist-item');

        items.forEach(item => {
            const nama = item.getAttribute('data-nama');
            const kode = item.getAttribute('data-kode');

            if (nama.includes(query) || kode.includes(query)) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    });

    // Helper function to check/uncheck all visible options
    function toggleSelectAll(check) {
        const items = document.querySelectorAll('.checklist-item');
        items.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = check;
                }
            }
        });
    }

    function editData(id) {
        // Ambil data mapel berdasarkan ID (Anda bisa menyesuaikan ini sesuai kebutuhan)
        fetch(`<?= site_url('mapel/getById/') ?>${id}`)
            .then(res => res.json())
            .then(data => {
                // Isi form edit dengan data yang diambil
                const form = document.getElementById('formEditMapel');
                form.action = `<?= base_url('mapel/update/') ?>${id}`;
                form.kode_mapel.value = data.kode_mapel;
                form.nama.value = data.nama;

                // Set manual edit flag since we are loading pre-existing data
                editManualEdit = true;

                // Tampilkan modal edit
                openModal('editMapelModal');
            });
    }

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        pag.innerHTML = '';

        for (let i = 1; i <= meta.lastPage; i++) {
            pag.innerHTML += `
            <button onclick="goPage(${i})"
                class="px-3 py-1 border rounded
                ${i === meta.page ? 'bg-blue-500 text-white' : ''}">
                ${i}
            </button>
        `;
        }
    }

    function goPage(page) {
        state.page = page;
        loadData();
    }

    function sort(field) {
        state.sortDir = state.sortDir === 'ASC' ? 'DESC' : 'ASC';
        state.sortBy = field;
        loadData();
    }

    function info(perpage, page, total) {
        document.getElementById('startRecord').textContent = (page - 1) * perpage + 1;
        document.getElementById('endRecord').textContent = Math.min(page * perpage, total);
        document.getElementById('totalRecords').textContent = total;
    }

    /* ===== EVENTS ===== */
    document.getElementById('search').addEventListener('input', e => {
        state.search = e.target.value;
        state.page = 1;
        loadData();
        info(state.perPage, state.page, state.total);
    });

    document.getElementById('perPage').addEventListener('change', e => {
        state.perPage = e.target.value;
        state.page = 1;
        loadData();
        info(state.perPage, state.page, 0);
    });

    /* INIT */
    loadData();


    $(document).on('click', '.tombol-hapus', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const base_url = '<?= base_url() ?>';
        
        let warningText = 'Mata pelajaran ini akan dinonaktifkan / dihapus dari lembaga Anda.';
        if (userLevel === 'super_admin') {
            warningText = 'Mata pelajaran master ini akan dihapus secara permanen beserta semua relasinya di lembaga-lembaga!';
        }

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'mapel/hapus', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });

    // Auto-suggest code helper
    function suggestKodeMapel(nama) {
        nama = nama.trim();
        if (!nama) return '';

        // Check parentheses first
        const matches = nama.match(/\(([^)]+)\)/);
        if (matches) {
            const inner = matches[1].trim().replace(/[^A-Za-z]/g, '');
            const blacklist = ['umum', 'wajib', 'minat', 'peminatan', 'pemin', 'wjb', 'umm', 'kpm', 'sore', 'pagi'];
            if (inner.length >= 2 && inner.length <= 5 && !blacklist.includes(inner.toLowerCase())) {
                return inner.toUpperCase();
            }
            nama = nama.replace(/\s*\([^)]+\)/g, '').trim();
        }

        nama = nama.replace(/[^A-Za-z0-9\s-]/g, '').replace(/\s+/g, ' ');
        const namaUpper = nama.toUpperCase();

        const customCodes = {
            'MATEMATIKA': 'MTK',
            'BIOLOGI': 'BIO',
            'FISIKA': 'FIS',
            'KIMIA': 'KIM',
            'GEOGRAFI': 'GEO',
            'SEJARAH': 'SJR',
            'EKONOMI': 'EKO',
            'SOSIOLOGI': 'SOS',
            'TAFHIDZ': 'TFZ',
            'TAHFIDZ': 'TFZ',
            'HADITS': 'HDT',
            'HADIS': 'HDT',
            'FIQIH': 'FQH',
            'FIKIH': 'FQH',
            'AQIDAH': 'AQD',
            'AKHLAK': 'AKH',
            'NAHWU': 'NHW',
            'SHOROF': 'SRF',
            'TEMATIK': 'TMK',
            'PRAMUKA': 'PRM',
            'KEWIRAUSAHAAN': 'KWU',
            'INFORMATIKA': 'INF',
            'KERAJINAN': 'KRJ',
            'OLAHRAGA': 'OR',
            'SENI': 'SEN',
            'BUDAYA': 'BDY',
            'BAHASA': 'BHS',
            'SASTRA': 'SST',
            'DINIYAH': 'DNY',
            'TAJWID': 'TJW',
            'KHAT': 'KHT',
            'IMLA': 'IML',
            'INSYA': 'INS',
            'MAHFUDZAT': 'MFZ',
            'MUTALAAH': 'MTL',
            'BALAGHAH': 'BLG',
            'FAROIDH': 'FRD',
            'KALIGRAFI': 'KLG',
            'ASWAJA': 'ASW',
            'KEASWAJAAN': 'ASW',
            'PANCASILA': 'PPN',
            'BAHASA INDONESIA': 'BIN',
            'BAHASA INGGRIS': 'BIG',
            'BAHASA ARAB': 'BAR',
            'BAHASA DAERAH': 'BDH',
            'PENDIDIKAN AGAMA ISLAM': 'PAI',
            'PENDIDIKAN AGAMA ISLAM DAN BUDI PEKERTI': 'PABP',
            'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN': 'PPKN',
            'PENDIDIKAN PANCASILA': 'PPN',
            'PENDIDIKAN JASMANI OLAHRAGA DAN KESEHATAN': 'PJOK',
            'PENDIDIKAN JASMANI, OLAHRAGA, DAN KESEHATAN': 'PJOK',
            'PENDIDIKAN JASMANI OLAHRAGA KESEHATAN': 'PJOK',
            'ILMU PENGETAHUAN ALAM': 'IPA',
            'ILMU PENGETAHUAN SOSIAL': 'IPS',
            'SENI BUDAYA': 'SBD',
            'SENI BUDAYA DAN KETERAMPILAN': 'SBK',
            'SENI BUDAYA DAN PRAKARYA': 'SBP',
            'PRAKARYA DAN KEWIRAUSAHAAN': 'PKWU',
            'SEJARAH INDONESIA': 'SIND',
            'BIMBINGAN CONSELING': 'BK',
            'BIMBINGAN KONSELING': 'BK'
        };

        if (customCodes[namaUpper]) {
            return customCodes[namaUpper];
        }

        const words = nama.split(' ').filter(w => w.length > 0);
        if (words.length === 1) {
            const word = words[0].toUpperCase();
            if (word.length <= 3) return word;

            const vowels = ['A', 'E', 'I', 'O', 'U'];
            const consonants = [];
            for (let i = 0; i < word.length; i++) {
                const char = word[i];
                if (char >= 'A' && char <= 'Z' && !vowels.includes(char)) {
                    consonants.push(char);
                }
            }
            const uniqueConsonants = [...new Set(consonants)];
            const startsWithVowel = vowels.includes(word[0]);

            if (startsWithVowel) {
                if (uniqueConsonants.length >= 2) {
                    return word[0] + uniqueConsonants[0] + uniqueConsonants[1];
                }
            } else {
                if (uniqueConsonants.length >= 3) {
                    return uniqueConsonants[0] + uniqueConsonants[1] + uniqueConsonants[2];
                }
            }
            return word.substring(0, 3);
        } else {
            let initials = '';
            words.forEach(w => {
                const cleanW = w.replace(/[^A-Za-z0-9]/g, '');
                if (cleanW.length > 0) {
                    initials += cleanW[0].toUpperCase();
                }
            });
            return initials.length >= 2 ? initials.substring(0, 4) : '';
        }
    }

    let tambahManualEdit = false;
    let editManualEdit = false;

    // Tambah Mapel Listeners
    const tNama = document.getElementById('tambah_nama_mapel');
    const tKode = document.getElementById('tambah_kode_mapel');
    if (tNama && tKode) {
        tNama.addEventListener('input', e => {
            if (e.target.value.trim() === '') {
                tambahManualEdit = false;
            }
            if (!tambahManualEdit) {
                tKode.value = suggestKodeMapel(e.target.value);
            }
        });
        tKode.addEventListener('input', e => {
            tambahManualEdit = e.target.value.trim() !== '';
        });
    }

    // Edit Mapel Listeners
    const eNama = document.getElementById('edit_nama_mapel');
    const eKode = document.getElementById('edit_kode_mapel');
    if (eNama && eKode) {
        eNama.addEventListener('input', e => {
            if (e.target.value.trim() === '') {
                editManualEdit = false;
            }
            if (!editManualEdit) {
                eKode.value = suggestKodeMapel(e.target.value);
            }
        });
        eKode.addEventListener('input', e => {
            editManualEdit = e.target.value.trim() !== '';
        });
    }

    // Reset tambahManualEdit on open
    $(document).on('click', '[onclick*="tambahMapelModal"]', function() {
        tambahManualEdit = false;
        if (tKode) tKode.value = '';
        if (tNama) tNama.value = '';
    });
</script>