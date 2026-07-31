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
        <input type="number" id="onePage" class="p-2 w-full md:w-20 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Page...">

        <!-- Group 1: Data Utama (Fast) -->
        <button id="sincOneUtama" class="bg-teal-605 hover:bg-teal-700 text-white px-3 py-2 rounded-lg font-medium text-xs flex items-center">
            <i class="fas fa-refresh mr-1"></i> Utama Page
        </button>
        <button id="startUtama" class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded-lg font-medium text-xs flex items-center">
            <i class="fas fa-users mr-1"></i> 1. Sync Data Utama
        </button>

        <!-- Group 2: Alamat & Registrasi (Detail) -->
        <button id="sincOneDetail" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-lg font-medium text-xs flex items-center">
            <i class="fas fa-refresh mr-1"></i> Detail Page
        </button>
        <button id="startDetail" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg font-medium text-xs flex items-center">
            <i class="fas fa-map-marker-alt mr-1"></i> 2. Sync Alamat & Reg
        </button>
    </div>
</div>


<!-- Tabel Data Siswa -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Daftar Siswa</h3>

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

<div id="pageInfo" class="hidden">
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
           border border-gray-200 dark:border-gray-700">
</div>

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
                <form id="formTambahSiswa" action="<?= base_url('sinkron/data_siswa') ?>" method="POST">
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

        fetch(`<?= site_url('sinkron/data_siswa') ?>?${params}`)
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
                    <button onclick="syncOneSiswa('${row.id_siswa}', this)" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition" title="Sinkronkan PD"><i class="fa fa-refresh mr-1"></i>Sync</button>
                    <button onclick="editData('${row.id_siswa}')" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <button data-id="${row.id_siswa}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 tombol-hapus">Hapus</button>
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
                $.post(base_url + 'sinkron/data_siswa', {
                        id
                    })
                    .done(() => loadData());
            }
        });
    });
</script>

<script>
    let page = 1;
    let index = 0;
    let siswas = [];
    let total = 0;
    let done = 0;
    let syncType = 'utama'; // 'utama' atau 'detail'
    let isSinglePage = false;

    $('#startUtama').click(function() {
        page = 1;
        done = 0;
        total = 0;
        syncType = 'utama';
        isSinglePage = false;
        $('#log').html('');
        $('#pageInfo').removeClass('hidden');
        updateProgress();
        loadPage();
    });

    $('#sincOneUtama').click(function() {
        const inputPage = $('#onePage').val();
        if (!inputPage || inputPage < 1) {
            Swal.fire('Oops', 'Masukkan nomor halaman yang valid!', 'warning');
            return;
        }
        page = inputPage;
        done = 0;
        total = 0;
        syncType = 'utama';
        isSinglePage = true;
        $('#log').html('');
        $('#pageInfo').removeClass('hidden');
        updateProgress();
        loadPage();
    });

    $('#startDetail').click(function() {
        page = 1;
        done = 0;
        total = 0;
        syncType = 'detail';
        isSinglePage = false;
        $('#log').html('');
        $('#pageInfo').removeClass('hidden');
        updateProgress();
        loadPage();
    });

    $('#sincOneDetail').click(function() {
        const inputPage = $('#onePage').val();
        if (!inputPage || inputPage < 1) {
            Swal.fire('Oops', 'Masukkan nomor halaman yang valid!', 'warning');
            return;
        }
        page = inputPage;
        done = 0;
        total = 0;
        syncType = 'detail';
        isSinglePage = true;
        $('#log').html('');
        $('#pageInfo').removeClass('hidden');
        updateProgress();
        loadPage();
    });

    function loadPage() {
        $('#log').append(`📦 Ambil page ${page} (${syncType === 'utama' ? 'Data Utama' : 'Alamat & Reg'})\n`);

        $.post('<?= base_url('sinkron/fetch_page_siswa') ?>', {
            page
        }, function(res) {
            let r = JSON.parse(res);

            if (!r.data || !r.data.data || r.data.data.length === 0) {
                $('#log').append("🎉 Sinkron selesai\n");
                return;
            }

            siswas = r.data.data;
            total += siswas.length;
            index = 0;
            updateProgress();
            syncNext();
        });
    }

    function syncNext() {
        if (index >= siswas.length) {
            if (isSinglePage) {
                $('#log').append(`🎉 Sinkron Halaman ${page} Selesai!\n`);
                return;
            }
            page++;
            loadPage();
            return;
        }

        let siswa = siswas[index];

        let targetUrl = '';
        let postData = {};

        if (syncType === 'utama') {
            targetUrl = '<?= base_url('sinkron/sync_one_siswa') ?>';
            postData = {
                siswa: {
                    id_siswa: siswa.peserta_didik_id,
                    nama: siswa.nama,
                    nisn: siswa.nisn,
                    jkl: siswa.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    nis: siswa.nipd || siswa.nis || siswa.no_induk || null
                }
            };
        } else {
            targetUrl = '<?= base_url('sinkron/sync_detail_siswa') ?>';
            postData = {
                siswa: {
                    id_siswa: siswa.peserta_didik_id
                }
            };
        }

        $.ajax({
            url: targetUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(postData),
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

    function syncOneSiswa(id_siswa, btn) {
        const $btn = $(btn);
        const $icon = $btn.find('i');
        const originalClass = $icon.attr('class') || 'fa fa-refresh mr-1';

        // Start loading animation
        $btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
        $icon.attr('class', 'fa fa-refresh fa-spin mr-1');
        
        $.ajax({
            url: '<?= base_url('sinkron/sync_siswa') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                siswa: {
                    id_siswa: id_siswa
                }
            }),

            timeout: 20000,
            success: function(res) {
                let r = typeof res === 'string' ? JSON.parse(res) : res;

                if (r.status === 'deleted') {
                    // Deleted status animation
                    $icon.attr('class', 'fa fa-trash text-white mr-1');
                    $btn.removeClass('bg-green-500 hover:bg-green-600').addClass('bg-orange-500');
                    $('#log').append("⚠️ " + r.msg + "\n");

                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Dihapus',
                        text: r.msg,
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });

                    setTimeout(function() {
                        $icon.attr('class', originalClass);
                        $btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed bg-orange-500').addClass('bg-green-500 hover:bg-green-600');
                        loadData();
                    }, 1500);
                    return;
                }

                // Success animation
                $icon.attr('class', 'fa fa-check text-white mr-1');
                $btn.removeClass('bg-green-500 hover:bg-green-600').addClass('bg-emerald-600');
                $('#log').append("✅ " + r.msg + "\n");

                Swal.fire({
                    icon: 'success',
                    title: 'Sinkron Selesai',
                    text: r.msg,
                    timer: 1500,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });

                setTimeout(function() {
                    $icon.attr('class', originalClass);
                    $btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed bg-emerald-600').addClass('bg-green-500 hover:bg-green-600');
                    loadData();
                }, 1000);
            },
            error: function() {
                // Error animation
                $icon.attr('class', 'fa fa-exclamation-triangle text-white mr-1');
                $btn.removeClass('bg-green-500 hover:bg-green-600').addClass('bg-rose-600');
                $('#log').append("❌ Error, retry...\n");

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Koneksi terputus atau error server.',
                    timer: 1500,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });

                setTimeout(function() {
                    $icon.attr('class', originalClass);
                    $btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed bg-rose-600').addClass('bg-green-500 hover:bg-green-600');
                }, 1500);
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