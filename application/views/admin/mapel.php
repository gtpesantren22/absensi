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
<main class="flex-1 p-4 md:p-6 overflow-y-auto">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data Mapel</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data mapel</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">


            <!-- Tombol Tambah Siswa -->
            <button onclick="openModal('uploadMapelModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-upload mr-2"></i>
                Upload Mapel
            </button>
            <button onclick="openModal('tambahMapelModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Mapel
            </button>
        </div>
    </div>


    <!-- Tabel Data -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar Mapel</h3>

            <div class="flex items-center space-x-2 mt-2 md:mt-0">
                <div class="relative">
                    <select id="perPage" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
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
                            <input type="text" name="kode_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode mapel" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Mapel</label>
                            <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama mapel" required>
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
                <h3 class="text-xl font-bold">Edit Data Guru</h3>
                <button onclick="closeModal('editMapelModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Tambah Siswa -->
            <div class="p-6">
                <form id="formEditMapel" action="<?= base_url('mapel/udpdate') ?>" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode mapel" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Mapel</label>
                            <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama mapel" required>
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

    <!-- Modal Upload -->
    <div id="uploadMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold">Upload Data Guru</h3>
                <button onclick="closeModal('uploadMapelModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Upload Siswa -->
            <div class="p-6">
                <form id="" action="<?= base_url('mapel/upload_excel') ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Pilih file</label>
                            <input type="file" name="file" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode guru" required>
                            <small class="text-red-500">upload file yang didownload dari aplikasi ini</small>
                        </div>
                        <div class="md:col-span-2">
                            <button type="button" onclick="window.location.href='<?= base_url() ?>mapel/downloadTemplate'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Download Template
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('uploadMapelModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php $this->load->view('admin/foot'); ?>
<script>
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

        data.forEach(row => {
            tbody.innerHTML += `
            <tr class="border-b">
                <td class="p-2">
                    <span class="px-2 py-1 me-2 rounded-xl bg-blue-500 text-white">
                                ${row.kode_mapel}
                            </span>
                </td>
                <td class="p-2"> 
                    ${row.nama}
                </td>
                <td class="p-2">
                    <button onclick="editData(${row.id_mapel})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <button data-id="${row.id_mapel}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
                </td>
            </tr>
        `;
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

        Swal.fire({
            title: 'Yakin?',
            text: 'Data guru akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'mapel/hapus', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });
</script>