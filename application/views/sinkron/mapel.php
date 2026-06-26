<?php $this->load->view('admin/head'); ?>

    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data Master Mapel</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola dan sinkronisasi data master mata pelajaran terpusat</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
            <button id="start" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-sync mr-2"></i>
                Sinkron Mapel
            </button>
        </div>
    </div>

    <!-- Tabel Data Master Mapel -->
    <div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
        <!-- Header Tabel dengan Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-lg">Daftar Master Mapel</h3>

            <div class="flex items-center space-x-2 mt-2 md:mt-0">
                <div class="relative">
                    <select id="filterPeruntukan" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                        <option value="">Semua Peruntukan</option>
                        <option value="MI">MI</option>
                        <option value="MTs">MTs</option>
                        <option value="MA">MA</option>
                        <option value="SMP">SMP</option>
                        <option value="SMK">SMK</option>
                        <option value="Madin Putra">Madin Putra</option>
                        <option value="Madin Putri">Madin Putri</option>
                    </select>
                </div>
                <div class="relative">
                    <select id="perPage" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
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
                    <input type="search" id="search" class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm" placeholder="Cari mapel...">
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto px-4">
            <table class="w-full" id="datatable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                        <th onclick="sort('id_master_mapel')" class="py-3 px-4 font-medium cursor-pointer">ID Mapel</th>
                        <th onclick="sort('kode_mapel')" class="py-3 px-4 font-medium cursor-pointer">Kode Mapel</th>
                        <th onclick="sort('nama')" class="py-3 px-4 font-medium cursor-pointer">Nama Mapel</th>
                        <th class="py-3 px-4 font-medium">Peruntukan Lembaga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tableBody">
                    <!-- Baris Data -->
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

    <!-- Progress Status & Logs -->
    <div id="infoPage" class="hidden bg-white dark:bg-gray-800 rounded-xl flux-shadow p-6 mb-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-tasks text-blue-500"></i>
            <span>Proses Sinkronisasi</span>
        </h3>
        <div>
            <div class="flex justify-between text-sm mb-1 text-gray-700 dark:text-gray-300">
                <span id="progressText">0 / 0</span>
                <span id="progressPercent">0%</span>
            </div>

            <div class="w-full rounded-full h-4 overflow-hidden bg-gray-200 dark:bg-gray-700">
                <div id="progressBar" class="h-4 transition-all duration-300 bg-green-500 dark:bg-emerald-400" style="width: 0%"></div>
            </div>
        </div>

        <!-- Log -->
        <pre id="log" class="mt-4 h-48 overflow-auto text-xs p-3 rounded bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-green-400 border border-gray-200 dark:border-gray-700 font-mono"></pre>
    </div>

<?php $this->load->view('admin/foot'); ?>
<script>
    let state = {
        page: 1,
        perPage: 10,
        search: '',
        peruntukan: '',
        sortBy: 'nama',
        sortDir: 'ASC',
        total: 0
    };

    function loadData() {
        const params = new URLSearchParams(state).toString();

        fetch(`<?= site_url('sinkron/data_master_mapel') ?>?${params}`)
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
                    <td colspan="4" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada data master mapel. Silakan klik tombol "Sinkron Mapel" untuk mengambil data.
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(row => {
            let peruntukan = '';
            try {
                let list = JSON.parse(row.jenis_lembaga) || [];
                list.forEach(item => {
                    let badgeName = typeof item === 'object' ? item.nama : item;
                    peruntukan += `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 mr-1 mb-1">${badgeName}</span>`;
                });
            } catch (e) {
                peruntukan = '<span class="text-xs text-gray-400">-</span>';
            }

            tbody.innerHTML += `
            <tr class="border-b">
                <td class="p-4 text-sm text-gray-700 dark:text-gray-300 font-mono">
                    ${row.id_master_mapel}
                </td>
                <td class="p-4 text-sm font-medium"> 
                    <span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-mono">
                        ${row.kode_mapel}
                    </span>
                </td>
                <td class="p-4 text-sm text-gray-800 dark:text-gray-200 font-semibold"> 
                    ${row.nama}
                </td>
                <td class="p-4 text-sm">
                    ${peruntukan || '<span class="text-xs text-gray-400">Semua</span>'}
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
                class="px-3 py-1 border rounded text-xs transition duration-150
                ${i === meta.page ? 'bg-primary-500 border-primary-500 text-white font-semibold' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'}">
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
        document.getElementById('startRecord').textContent = total === 0 ? 0 : (page - 1) * perpage + 1;
        document.getElementById('endRecord').textContent = Math.min(page * perpage, total);
        document.getElementById('totalRecords').textContent = total;
    }

    /* ===== EVENTS ===== */
    document.getElementById('search').addEventListener('input', e => {
        state.search = e.target.value;
        state.page = 1;
        loadData();
    });

    document.getElementById('perPage').addEventListener('change', e => {
        state.perPage = e.target.value;
        state.page = 1;
        loadData();
    });

    document.getElementById('filterPeruntukan').addEventListener('change', e => {
        state.peruntukan = e.target.value;
        state.page = 1;
        loadData();
    });

    /* INIT */
    loadData();
</script>
<script>
    let page = 1;
    let index = 0;
    let mapels = [];
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
        $('#log').append(`📦 Mengambil data page ${page} dari API...\n`);
        
        // Auto scroll log to bottom
        let logEl = document.getElementById('log');
        logEl.scrollTop = logEl.scrollHeight;

        $.post('<?= base_url('sinkron/fetch_page_mapel') ?>', {
            page
        }, function(res) {
            let r;
            try {
                r = typeof res === 'string' ? JSON.parse(res) : res;
            } catch (e) {
                $('#log').append("❌ Respon API tidak valid atau session API kedaluwarsa\n");
                return;
            }

            // check structure: r.data.data
            let list = r?.data?.data || [];

            if (list.length === 0) {
                $('#log').append("🎉 Sinkronisasi selesai seluruhnya!\n");
                logEl.scrollTop = logEl.scrollHeight;
                loadData();
                return;
            }

            mapels = list;
            total += mapels.length;
            index = 0;
            updateProgress();
            syncNext();
        }).fail(function() {
            $('#log').append("❌ Koneksi ke API Gagal.\n");
        });
    }

    function syncNext() {
        let logEl = document.getElementById('log');
        
        if (index >= mapels.length) {
            page++;
            loadPage();
            return;
        }

        let item = mapels[index];

        $.ajax({
            url: '<?= base_url('sinkron/sync_one_mapel') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                mapel: {
                    id_master_mapel: item?.mata_pelajaran_id ?? null,
                    nama: item?.nama ?? '',
                    jenis_lembaga: item?.jenis_lembaga ?? []
                }
            }),
            timeout: 20000,
            success: function(res) {
                let r = typeof res === 'string' ? JSON.parse(res) : res;
                $('#log').append("✅ " + r.msg + "\n");
                logEl.scrollTop = logEl.scrollHeight;

                index++;
                done++;
                updateProgress();
                syncNext();
            },
            error: function() {
                $('#log').append("❌ Gagal memproses, mengulang...\n");
                logEl.scrollTop = logEl.scrollHeight;
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
