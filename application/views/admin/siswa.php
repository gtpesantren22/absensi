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
            <h2 class="text-2xl font-bold">Data Siswa</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data siswa</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

            <?php if (isset($ksjdhkjdf)) { ?>
                <!-- Tombol Tambah Siswa -->
                <button onclick="openModal('uploadSiswaModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Siswa
                </button>
                <button onclick="openModal('tambahSiswaModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Siswa
                </button>

                <!-- Modal Tambah Siswa -->
                <div id="tambahSiswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Header Modal -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-bold">Tambah Data Siswa</h3>
                            <button onclick="closeModal('tambahSiswaModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Form Tambah Siswa -->
                        <div class="p-6">
                            <form id="formTambahSiswa" action="<?= base_url('siswa/add') ?>" method="POST">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">NISN</label>
                                        <input type="text" name="nisn" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan NISN" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                                        <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">Alamat</label>
                                        <input type="text" name="alamat" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan alamat" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Jenis Kelamin</label>
                                        <div class="flex space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="jenis_kelamin" value="Laki-laki" class="mr-2" required>
                                                <span>Laki-laki</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="jenis_kelamin" value="Perempuan" class="mr-2" required>
                                                <span>Perempuan</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal('tambahSiswaModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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

                <!-- Modal Edit Siswa -->
                <div id="editSiswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Header Modal -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-bold">Edit Data Siswa</h3>
                            <button onclick="closeModal('editSiswaModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Form Tambah Siswa -->
                        <div class="p-6">
                            <form id="formEditSiswa" action="<?= base_url('siswa/update') ?>" method="POST">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">NISN</label>
                                        <input type="text" name="nisn" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan NISN" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                                        <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">Alamat</label>
                                        <input type="text" name="alamat" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan alamat" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Jenis Kelamin</label>
                                        <div class="flex space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="jenis_kelamin" value="Laki-laki" class="mr-2" required>
                                                <span>Laki-laki</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="jenis_kelamin" value="Perempuan" class="mr-2" required>
                                                <span>Perempuan</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal('editSiswaModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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

                <!-- Modal Upload Siswa -->
                <div id="uploadSiswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Header Modal -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-bold">Upload Data Siswa</h3>
                            <button onclick="closeModal('uploadSiswaModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Form Upload Siswa -->
                        <div class="p-6">
                            <form id="" action="<?= base_url('siswa/upload_excel') ?>" method="POST" enctype="multipart/form-data">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-2">Pilih file</label>
                                        <input type="file" name="file" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode siswa" required>
                                        <small class="text-red-500">upload file yang didownload dari aplikasi ini</small>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="button" onclick="window.location.href='<?= base_url() ?>siswa/downloadTemplate'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Download Template
                                        </button>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal('uploadSiswaModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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
            <?php } ?>
        </div>
    </div>


    <!-- Tabel Data Siswa -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar Siswa</h3>

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
                    <input type="search" id="search" class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari siswa...">
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto px-4">
            <table class="w-full" id="datatable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                        <th onclick="sort('nisn')" class="py-3 px-4 font-medium cursor-pointer">NISN</th>
                        <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama Siswa</th>
                        <th onclick="sort('jkl')" class="py-3 px-4 font-medium cursor-pointer">Jenis Kelamin</th>
                        <th onclick="sort('alamat')" class="py-3 px-4 font-medium cursor-pointer">Alamat</th>
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

        fetch(`<?= site_url('siswa/datatable') ?>?${params}`)
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
                    ${row.nisn}
                </td>
                <td class="p-2"> 
                    ${row.nama}
                </td>
                <td class="p-2"> 
                    ${row.jkl }
                </td>
                <td class="p-2">${row.alamat}</td>
                <td class="p-2">
                    <button onclick="editData('${row.id_siswa}')" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <button data-id="${row.id_siswa}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
                </td>
            </tr>
        `;
        });
    }

    function editData(id) {
        // Ambil data guru berdasarkan ID (Anda bisa menyesuaikan ini sesuai kebutuhan)
        fetch(`<?= site_url('siswa/getById/') ?>${id}`)
            .then(res => res.json())
            .then(data => {
                // Isi form edit dengan data yang diambil
                const form = document.getElementById('formEditSiswa');
                form.action = `<?= base_url('siswa/update/') ?>${id}`;
                form.nama.value = data.nama;
                form.alamat.value = data.alamat;
                form.nisn.value = data.nisn;

                // Set radio button jenis kelamin
                const jenisKelaminOptions = form.jenis_kelamin;
                for (let option of jenisKelaminOptions) {
                    option.checked = option.value === data.jkl;
                }

                // Tampilkan modal edit
                openModal('editSiswaModal');
            });
    }

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        pag.innerHTML = '';

        const current = meta.page;
        const last = meta.lastPage;
        const delta = 1; // jumlah halaman kiri-kanan

        function addButton(label, page = null, active = false, disabled = false) {
            pag.innerHTML += `
                <button
                    ${page ? `onclick="goPage(${page})"` : ''}
                    class="px-3 py-1 border rounded text-sm
                    ${active ? 'bg-blue-500 text-white' : 'bg-white dark:bg-gray-800'}
                    ${disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'}">
                    ${label}
                </button>
            `;
        }

        // Prev
        addButton('«', current - 1, false, current === 1);

        // Page 1
        addButton(1, 1, current === 1);

        let start = Math.max(2, current - delta);
        let end = Math.min(last - 1, current + delta);

        if (start > 2) addButton('...', null, false, true);

        for (let i = start; i <= end; i++) {
            addButton(i, i, current === i);
        }

        if (end < last - 1) addButton('...', null, false, true);

        // Last page
        if (last > 1) addButton(last, last, current === last);

        // Next
        addButton('»', current + 1, false, current === last);
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
            text: 'Data siswa akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'siswa/hapus', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });
</script>