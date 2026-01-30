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
            <h2 class="text-2xl font-bold">Data User</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data user aplikasi</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">


            <!-- Tombol Tambah Siswa -->
            <!-- <button onclick="openModal('uploadMapelModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-upload mr-2"></i>
                Upload Mapel -->
            </button>
            <button onclick="renderAkun()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-refresh mr-2"></i>
                Generate User guru
            </button>
        </div>
    </div>


    <!-- Tabel Data -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar User</h3>

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
                    <input type="search" id="search" class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Cari user...">
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto px-4">
            <table class="w-full" id="datatable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                        <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama</th>
                        <th onclick="sort('username')" class="py-3 px-4 font-medium cursor-pointer">Username</th>
                        <th onclick="sort('pass_v')" class="py-3 px-4 font-medium cursor-pointer">Password</th>
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

        fetch(`<?= site_url('user/datatable') ?>?${params}`)
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
                    ${row.username}
                </td>
                <td class="p-2"> 
                    ${row.pass_v}
                </td>
                <td class="p-2">
                    <button data-id="${row.id_user}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
                </td>
            </tr>
        `;
        });
    }

    function editData(id) {
        // Ambil data mapel berdasarkan ID (Anda bisa menyesuaikan ini sesuai kebutuhan)
        fetch(`<?= site_url('user/getById/') ?>${id}`)
            .then(res => res.json())
            .then(data => {
                // Isi form edit dengan data yang diambil
                const form = document.getElementById('formEditMapel');
                form.action = `<?= base_url('user/update/') ?>${id}`;
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

    function renderAkun() {
        const base_url = '<?= base_url() ?>';
        swal.fire({
            title: 'Yakin?',
            text: 'Data akun guru akan diperbarui',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get(base_url + 'user/render')
                    .done(() => loadData());
            }
        });
    }

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
                $.get(base_url + 'user/hapus', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });
</script>