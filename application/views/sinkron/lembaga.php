<?php $this->load->view('admin/head'); ?>

    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data lembaga</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data lembaga</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">


            <!-- Tombol Tambah Siswa -->
            <button id="start" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-refresh mr-2"></i>
                Sinkron Lembaga
            </button>
        </div>

    </div>

    <!-- Tabel Data Siswa -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar lembaga</h3>

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
                        <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama Lembaga</th>
                        <th onclick="sort('npsn')" class="py-3 px-4 font-medium cursor-pointer">NPSN</th>
                        <th onclick="sort('jenjang')" class="py-3 px-4 font-medium cursor-pointer">Jenjang</th>
                        <th onclick="sort('session_id')" class="py-3 px-4 font-medium cursor-pointer">Session ID</th>
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

    <div id="infoPage" class="hidden">
        <div class="mt-6">
            <div class="flex justify-between text-sm mb-1
                text-gray-700 dark:text-gray-300">
                <span id="progressText">0 / 0</span>
                <span id="progressPercent">0%</span>
            </div>

            <div class="w-full rounded-full h-4 overflow-hidden
                bg-gray-200 dark:bg-gray-700">
                <div id="progressBar"
                    class="h-4 transition-all duration-300
                   bg-green-500 dark:bg-emerald-400"
                    style="width: 0%">
                </div>
            </div>
        </div>

        <!-- Log -->
        <pre id="log"
            class="mt-4 h-48 overflow-auto text-xs p-3 rounded
           bg-gray-100 text-gray-800
           dark:bg-gray-900 dark:text-green-400
           border border-gray-200 dark:border-gray-700"> </pre>
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

        fetch(`<?= site_url('sinkron/data_lembaga') ?>?${params}`)
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
            let wrn = row.warna != '' ? row.warna : 'black'
            tbody.innerHTML += `
            <tr class="border-b">
                <td class="p-2"> 
                    ${row.nama}
                </td>
                <td class="p-2"> 
                    ${row.npsn }
                </td>
                <td class="p-2">${row.jenjang}</td>
                <td class="p-2">
                    <input type="text" value="${row.session_id || ''}" 
                           class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-32 input-session-id" 
                           data-id="${row.id_lembaga}">
                </td>
                <td class="p-2">
                    <button onclick="editData('${row.id_lembaga}')" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <button data-id="${row.id_lembaga}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
                </td>
            </tr>
        `;
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
            text: 'Data lembaga akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'sinkron/hapus', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });

    $(document).on('change', '.input-session-id', function() {
        const id_lembaga = $(this).data('id');
        const session_id = $(this).val();
        const base_url = '<?= base_url() ?>';

        $.post(base_url + 'sinkron/update_session_id', {
            id_lembaga: id_lembaga,
            session_id: session_id
        }, function(res) {
            let r = typeof res === 'string' ? JSON.parse(res) : res;
            if (r.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: r.msg,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: r.msg
                });
            }
        });
    });
</script>
<script>
    let page = 1;
    let index = 0;
    let lembagas = [];
    let total = 0;
    let done = 0;

    $('#start').click(function() {
        page = 1;
        done = 0;
        total = 0;
        $('#log').html('');
        $('#infoPage').removeClass('hidden');
        updateProgress();
        loadPage();
    });

    function loadPage() {
        $('#log').append(`📦 Ambil page ${page}\n`);

        $.post('<?= base_url('sinkron/fetch_page_lembaga') ?>', {
            page
        }, function(res) {
            let r = JSON.parse(res);

            if (!r.data || r.data.length === 0) {
                $('#log').append("🎉 Sinkron selesai\n");
                return;
            }

            lembagas = r.data.data;
            total += lembagas.length;
            index = 0;
            updateProgress();
            syncNext();
            // console.log(lembagas);

        });
    }

    function syncNext() {
        if (index >= lembagas.length) {
            page++;
            loadPage();
            return;
        }

        let lembaga = lembagas[index];

        // console.log(lembaga.ptk_id);

        $.ajax({
            url: '<?= base_url('sinkron/sync_one_lembaga') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                lembaga: {
                    id_lembaga: lembaga?.lembaga_id ?? null,
                    nama: lembaga?.nama ?? '',
                    npsn: lembaga?.npsn ?? '',
                    jenjang: lembaga?.jenjang_pendidikan?.nama ?? '',
                    alamat: [
                        lembaga?.wilayah?.nama,
                        lembaga?.wilayah?.parrent_recursive?.nama,
                        lembaga?.wilayah?.parrent_recursive?.parrent_recursive?.nama
                    ].filter(Boolean).join(' - ')
                }
            }),

            timeout: 20000,
            success: function(res) {
                let r = typeof res === 'string' ? JSON.parse(res) : res;

                $('#log').append("✅ " + r.msg + "\n");

                index++;
                done++;
                updateProgress();
                syncNext();
            },
            error: function() {
                $('#log').append("❌ Error, retry...\n");
                setTimeout(syncNext, 3000);
            }
        });

    }

    function updateProgress() {
        let percent = total > 0 ? Math.round((done / total) * 100) : 0;

        $('#progressBar').css('width', percent + '%');
        $('#progressPercent').text(percent + '%');
        $('#progressText').text(done + ' / ' + total);
    }
</script>