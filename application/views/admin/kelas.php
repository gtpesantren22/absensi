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
            <h2 class="text-2xl font-bold">Data Kelas</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data kelas</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">


            <!-- Tombol Tambah Siswa -->
            <button onclick="openModal('uploadKelasModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-upload mr-2"></i>
                Upload Kelas
            </button>
            <button onclick="openModal('tambahKelasModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kelas
            </button>
        </div>
    </div>


    <!-- Tabel Data -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar Kelas</h3>

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
                    <input type="search" id="search" class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari kelas...">
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto px-4">
            <table class="w-full" id="datatable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                        <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama Kelas</th>
                        <th onclick="sort('jenis')" class="py-3 px-4 font-medium cursor-pointer">Jenis</th>
                        <th class="py-3 px-4 font-medium">Anggota Rombel</th>
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

    <!-- Modal Tambah  -->
    <div id="tambahKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold">Tambah Data Mapel</h3>
                <button onclick="closeModal('tambahKelasModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Tambah  -->
            <div class="p-6">
                <form id="formTambahMapel" action="<?= base_url('kelas/add') ?>" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Kelas</label>
                            <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama kelas" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Jenis</label>
                            <select name="jenis" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value='Utama'>Utama</option>
                                <option value='Campuran'>Campuran</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('tambahKelasModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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
    <div id="editKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold">Edit Data Kelas</h3>
                <button onclick="closeModal('editKelasModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Tambah Siswa -->
            <div class="p-6">
                <form id="formEditMapel" action="<?= base_url('kelas/update') ?>" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Kelas</label>
                            <input type="text" name="nama" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan nama kelas" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Jenis</label>
                            <select name="jenis" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value='Utama'>Utama</option>
                                <option value='Campuran'>Campuran</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editKelasModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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
    <div id="uploadKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold">Upload Data Kelas</h3>
                <button onclick="closeModal('uploadKelasModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Upload Siswa -->
            <div class="p-6">
                <form id="" action="<?= base_url('kelas/upload_excel') ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Pilih file</label>
                            <input type="file" name="file" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode guru" required>
                            <small class="text-red-500">upload file yang didownload dari aplikasi ini</small>
                        </div>
                        <div class="md:col-span-2">
                            <button type="button" onclick="window.location.href='<?= base_url() ?>kelas/downloadTemplate'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Download Template
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('uploadKelasModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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

    <!-- Upload Anggota -->
    <div id="inputAnggotaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold">Upload Anggota Rombel Kelas <span id="nmkelas"></span></h3>
                <button onclick="closeModal('inputAnggotaModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form Upload Siswa -->
            <div class="p-6">
                <form id="" action="<?= base_url('kelas/upload_anggota') ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <input type="hidden" name="id_kelas" id="id_kelas_input_anggota">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Pilih file</label>
                            <input type="file" name="file" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Masukkan kode guru" required>
                            <small class="text-red-500">upload file yang didownload dari aplikasi ini</small>
                        </div>
                        <div class="md:col-span-2">
                            <button type="button" id="btnDownloadTemplate" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Download Template
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('inputAnggotaModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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
    let currentKelasId = null
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

        fetch(`<?= site_url('kelas/datatable') ?>?${params}`)
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
                    ${row.nama}
                </td>
                    <td class="p-2"> 
                        ${row.jenis}
                    </td>
                    <td class="p-2"> 
                        ${row.jumlah_anggota}
                    </td>
                <td class="p-2">
                    <button onclick="editData(${row.id_kelas})" class="text-sm px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <button data-id="${row.id_kelas}" data-url='<?= base_url('kelas/hapus/') ?>' class="text-sm px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
                    <div class="relative inline-block text-left">
                        <button
                            data-id="${row.id_kelas}"
                            onclick="toggleDropdown(this)"
                            class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">

                            Anggota Rombel
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div class="dropdown-menu-btn hidden absolute right-0 mt-1 w-44 bg-white border rounded shadow z-50">
                            <button
                                onclick="window.location.href='<?= base_url('kelas/anggota/') ?>${row.id_kelas}'"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:text-gray-500">
                                ➕ Input Anggota
                            </button>
                            
                            <button
                            onclick="openModal1('inputAnggotaModal', ${row.id_kelas}, '${row.nama}')"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:text-gray-500">
                            📤 Upload Anggota
                            </button>

                            <button
                                data-id="${row.id_kelas}"
                                data-url='<?= base_url('kelas/kosongiAnggota/') ?>'
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:text-gray-500 tombol-hapus">
                                🗑️ Kosongi Rombel
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        });
    }

    function editData(id) {
        // Ambil data mapel berdasarkan ID (Anda bisa menyesuaikan ini sesuai kebutuhan)
        fetch(`<?= site_url('kelas/getById/') ?>${id}`)
            .then(res => res.json())
            .then(data => {
                // Isi form edit dengan data yang diambil
                const form = document.getElementById('formEditMapel');
                form.action = `<?= base_url('kelas/update/') ?>${id}`;
                form.nama.value = data.nama;
                form.jenis.value = data.jenis;

                // Tampilkan modal edit
                openModal('editKelasModal');
            });
    }

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        pag.innerHTML = '';

        const current = meta.page;
        const last = meta.lastPage;
        const delta = 2; // jumlah halaman kiri-kanan

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
        const url = $(this).data('url');

        Swal.fire({
            title: 'Yakin?',
            text: 'Data kelas akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });

    function toggleDropdown(btn) {
        // Tutup dropdown lain
        document.querySelectorAll('.dropdown-menu-btn').forEach(d => d.classList.add('hidden'));

        const menu = btn.nextElementSibling;
        menu.classList.toggle('hidden');
    }

    function openModal1(modalId, kelasId, namaKelas) {
        currentKelasId = kelasId

        if (namaKelas) {
            document.getElementById('nmkelas').textContent = namaKelas
        }
        document.getElementById('id_kelas_input_anggota').value = kelasId
        const modal = document.getElementById(modalId)
        modal.classList.remove('hidden')
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('.dropdown-menu-btn').forEach(d => d.classList.add('hidden'));
        }
    });
    document.getElementById('btnDownloadTemplate').addEventListener('click', function() {
        if (!currentKelasId) {
            alert('ID kelas tidak ditemukan')
            return
        }
        window.location.href = "<?= base_url('kelas/downloadTemplateAnggotaKelas/') ?>" + currentKelasId
    })
</script>