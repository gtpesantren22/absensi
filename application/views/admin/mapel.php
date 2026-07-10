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
            let actionButtons = `<button data-id="${row.id_mapel}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs tombol-hapus">Hapus</button>`;

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

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        pag.innerHTML = '';

        const current = meta.page;
        const last = meta.lastPage;
        const delta = 1; // number of pages left-right

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
</script>