<?php $this->load->view('admin/head'); ?>
<!-- Style helper -->
<style>
    .time-input {
        width: 6rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        border-width: 1px;
        border-color: #d1d5db;
        background-color: #ffffff;
        color: #374151;
    }
    .dark .time-input, .dark .time-input-style {
        border-color: #4b5563;
        background-color: #1f2937 !important;
        color: #f3f4f6 !important;
        color-scheme: dark;
    }
    @media (min-width: 768px) {
        .time-input {
            width: 7rem;
        }
    }
</style>

<!-- Header Dashboard -->
<div class="mb-6">
    <h2 class="text-2xl font-bold">Setting aplikasi</h2>
    <p class="text-gray-600 dark:text-gray-400">Halaman setting utiliti applikasi.</p>
</div>

<div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
        <i class="fas fa-cog text-blue-500"></i>
        <span>Pengaturan Umum & Ketentuan Waktu</span>
    </h3>
    <form action="<?= base_url('setting/jml_rombel') ?>" method="post">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Jumlah JP</label>
                <input type="number" name="jml_rombel" value="<?= $jml_rombel->isi ?? 0 ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Waktu Info Jadwal</label>
                <input type="time" name="waktu_info_jadwal" value="<?= $waktu_info_jadwal ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200 time-input-style" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Waktu Pembiasaan</label>
                <input type="time" name="waktu_pembiasaan" value="<?= $waktu_pembiasaan ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200 time-input-style" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Waktu Kehadiran</label>
                <input type="time" name="waktu_kehadiran" value="<?= $waktu_kehadiran ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-700 dark:text-gray-200 time-input-style" required>
            </div>
            <div>
                <button type="submit" class="w-full px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition duration-200 shadow-sm hover:shadow-md">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>



<div class="w-full mb-4 mx-auto">
    <div class="
        bg-white dark:bg-gray-800
        border border-gray-200 dark:border-gray-700
        rounded-2xl shadow-md
        p-6
    ">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KELOMPOK 1: DAFTAR GRUP WHATSAPP -->
            <div id="wa-groups-card" class="bg-gray-50/50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-xl p-5 flex flex-col min-h-[300px]">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center justify-between">
                    <span>Daftar Grup WhatsApp</span>
                    <span id="wa-group-count" class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 text-xs px-2.5 py-0.5 rounded-full font-medium">0</span>
                </h3>
                <div class="overflow-y-auto max-h-[280px] pr-1 flex-1">
                    <ul id="wa-group-list" class="space-y-2">
                        <li class="text-xs text-gray-500 py-4 text-center">Menunggu koneksi WhatsApp...</li>
                    </ul>
                </div>
            </div>

            <!-- KELOMPOK 2: GRUP WHATSAPP TERPILIH -->
            <div id="wa-selected-groups-card" class="bg-blue-50/30 dark:bg-slate-900/40 border border-blue-100 dark:border-slate-700 rounded-xl p-5 flex flex-col min-h-[300px]">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center justify-between">
                    <span>Grup Notifikasi Terpilih</span>
                    <span id="wa-selected-group-count" class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 text-xs px-2.5 py-0.5 rounded-full font-medium">0</span>
                </h3>
                <div class="overflow-y-auto max-h-[280px] pr-1 flex-1">
                    <ul id="wa-selected-group-list" class="space-y-2">
                        <li class="text-xs text-gray-500 py-4 text-center">Belum ada grup yang terpilih.</li>
                    </ul>
                </div>
            </div>

            <!-- KANAN : STATUS KONEKSI & QR CODE -->
            <div id="wa-connection-card" class="
                border border-dashed
                border-gray-300 dark:border-gray-600
                rounded-xl
                p-5
                flex flex-col items-center justify-center
                min-h-[300px]
                transition-all duration-300
            ">
                <!-- Loader / Checking Status -->
                <div id="wa-loading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Memeriksa status koneksi WhatsApp...</p>
                </div>

                <!-- STATE: DISCONNECTED (QR Code) -->
                <div id="wa-disconnected" class="text-center hidden w-full">
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 mb-4">
                        Disconnected
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                        Scan QR Code
                    </p>

                    <div class="
                        w-44 h-44
                        bg-white
                        border border-gray-300
                        rounded-lg
                        flex items-center justify-center
                        mx-auto mb-3 p-2
                    ">
                        <!-- QR Code Image -->
                        <img id="wa-qr-img" src="" alt="QR Code" class="w-full h-full object-contain hidden">
                        <div id="wa-qr-placeholder" class="text-xs text-gray-400 text-center px-4">
                            Menunggu QR Code dari server...
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Scan QR Code menggunakan WhatsApp Anda untuk menghubungkan perangkat.
                    </p>
                </div>

                <!-- STATE: CONNECTED (Status Connected) -->
                <div id="wa-connected" class="text-center hidden w-full">
                    <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-base font-bold text-green-700 dark:text-green-400">
                        Connected
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">
                        Perangkat WhatsApp Anda telah aktif.
                    </p>
                    
                    <!-- Button Disconnect -->
                    <button id="wa-btn-disconnect" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs font-semibold transition">
                        Disconnect
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 md:p-6">

    <!-- Header -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Pengaturan Jam Ketentuan
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Input jam per hari (Sabtu – Kamis)
        </p>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">

            <!-- THEAD -->
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                        Ket
                    </th>
                    <th class="px-3 py-2 text-center">Sabtu</th>
                    <th class="px-3 py-2 text-center">Minggu</th>
                    <th class="px-3 py-2 text-center">Senin</th>
                    <th class="px-3 py-2 text-center">Selasa</th>
                    <th class="px-3 py-2 text-center">Rabu</th>
                    <th class="px-3 py-2 text-center">Kamis</th>
                </tr>
            </thead>

            <!-- TBODY -->
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="loadJam">



            </tbody>
        </table>
    </div>
</div>


<?php $this->load->view('admin/foot'); ?>
<script>
    const waApiUrl = '<?= $wa_api_url ?>';
    const waSessionId = '<?= $wa_api_session_id ?>';
    const waApiKey = '<?= $wa_api_key ?>';
    let selectedGroups = <?= $wa_selected_groups ?>;
    let pollInterval = null;

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showConnectedState() {
        document.getElementById('wa-loading').classList.add('hidden');
        document.getElementById('wa-disconnected').classList.add('hidden');
        document.getElementById('wa-connected').classList.remove('hidden');
        
        // Remove error if any
        const oldErr = document.getElementById('wa-error-container');
        if (oldErr) oldErr.remove();
        
        loadGroups();
    }

    function showDisconnectedState() {
        document.getElementById('wa-loading').classList.add('hidden');
        document.getElementById('wa-disconnected').classList.remove('hidden');
        document.getElementById('wa-connected').classList.add('hidden');
        
        document.getElementById('wa-group-list').innerHTML = '<li class="text-xs text-gray-500 py-4 text-center">Menunggu koneksi WhatsApp...</li>';
        document.getElementById('wa-group-count').textContent = '0';
        
        // Remove error if any
        const oldErr = document.getElementById('wa-error-container');
        if (oldErr) oldErr.remove();
    }

    function showError(msg) {
        document.getElementById('wa-loading').classList.add('hidden');
        document.getElementById('wa-disconnected').classList.add('hidden');
        document.getElementById('wa-connected').classList.add('hidden');
        
        const container = document.getElementById('wa-connection-card');
        const oldErr = document.getElementById('wa-error-container');
        if (oldErr) oldErr.remove();
        
        const errDiv = document.createElement('div');
        errDiv.id = 'wa-error-container';
        errDiv.className = 'text-center py-6 text-red-500';
        errDiv.innerHTML = `
            <svg class="w-12 h-12 mx-auto text-red-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-semibold">${escapeHtml(msg)}</p>
            <button onclick="retryConnection()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                Coba Lagi
            </button>
        `;
        container.appendChild(errDiv);
    }

    function retryConnection() {
        const oldErr = document.getElementById('wa-error-container');
        if (oldErr) oldErr.remove();
        document.getElementById('wa-loading').classList.remove('hidden');
        checkWaStatus();
    }

    async function checkWaStatus() {
        try {
            const res = await fetch('<?= base_url("setting/wa_status") ?>');
            
            if (res.status === 404) {
                await createSession();
                return;
            }

            const data = await res.json();
            
            if (data.status && data.data) {
                const sessionData = data.data;
                if (sessionData.connected) {
                    showConnectedState();
                    if (pollInterval) {
                        clearInterval(pollInterval);
                        pollInterval = null;
                    }
                } else {
                    showDisconnectedState();
                    
                    const qrImg = document.getElementById('wa-qr-img');
                    const qrPlaceholder = document.getElementById('wa-qr-placeholder');
                    if (sessionData.qr) {
                        qrImg.src = sessionData.qr;
                        qrImg.classList.remove('hidden');
                        qrPlaceholder.classList.add('hidden');
                    } else {
                        qrImg.src = '';
                        qrImg.classList.add('hidden');
                        qrPlaceholder.classList.remove('hidden');
                    }

                    if (!pollInterval) {
                        pollInterval = setInterval(checkWaStatus, 5000);
                    }
                }
            } else {
                showError('Respon status API tidak valid.');
            }
        } catch (err) {
            console.error('Error checking status:', err);
            showError('Tidak dapat terhubung ke server WhatsApp API.');
        }
    }

    async function createSession() {
        try {
            const res = await fetch('<?= base_url("setting/wa_create") ?>', {
                method: 'POST'
            });
            const data = await res.json();
            if (data.status) {
                checkWaStatus();
            } else {
                showError('Gagal membuat sesi: ' + data.message);
            }
        } catch (err) {
            console.error('Error creating session:', err);
            showError('Gagal membuat sesi baru di server.');
        }
    }

    function updateSelectedGroupUI() {
        const listEl = document.getElementById('wa-selected-group-list');
        const countEl = document.getElementById('wa-selected-group-count');
        
        if (!Array.isArray(selectedGroups)) {
            selectedGroups = [];
        }
        
        countEl.textContent = selectedGroups.length;
        listEl.innerHTML = '';
        
        if (selectedGroups.length === 0) {
            listEl.innerHTML = '<li class="text-xs text-gray-500 py-4 text-center">Belum ada grup yang terpilih.</li>';
            return;
        }
        
        selectedGroups.forEach(group => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg text-xs flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition';
            
            li.innerHTML = `
                <div class="flex-1 min-w-0 pr-2 text-left">
                    <div class="font-medium text-gray-800 dark:text-gray-200 truncate">
                       ${escapeHtml(group.subject)}
                    </div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono select-all truncate">
                       ${group.id}
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button onclick="removeGroup('${escapeHtml(group.id)}')" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/30 dark:hover:bg-red-900/30 dark:text-red-400 rounded-lg text-xs font-semibold transition" title="Hapus Grup">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
            listEl.appendChild(li);
        });
    }

    async function selectGroup(id, name) {
        if (selectedGroups.length >= 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Batas Maksimal',
                text: 'Maksimal grup terpilih adalah 2 grup.'
            });
            return;
        }
        try {
            const res = await fetch('<?= base_url("setting/save_wa_group") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_group: id,
                    nama_group: name
                })
            });
            const data = await res.json();
            if (data.status) {
                if (!selectedGroups.some(g => g.id === id)) {
                    selectedGroups.push({ id: id, subject: name });
                }
                updateSelectedGroupUI();
                loadGroups();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Grup WhatsApp berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal menambahkan grup.'
                });
            }
        } catch (err) {
            console.error('Error selecting group:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menambahkan grup.'
            });
        }
    }

    async function loadGroups() {
        const groupList = document.getElementById('wa-group-list');
        const groupCount = document.getElementById('wa-group-count');
        groupList.innerHTML = '<li class="text-xs text-gray-500 py-2 text-center">Memuat daftar grup...</li>';
        
        try {
            const res = await fetch('<?= base_url("setting/wa_groups") ?>');
            const data = await res.json();
            
            if (data.status && Array.isArray(data.data)) {
                groupList.innerHTML = '';
                groupCount.textContent = data.data.length;
                
                if (data.data.length === 0) {
                    groupList.innerHTML = '<li class="text-xs text-gray-500 py-2 text-center">Tidak ada grup WhatsApp yang diikuti.</li>';
                    return;
                }
                
                data.data.forEach(group => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-lg text-xs flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 transition';
                    
                    const subject = group.subject || 'Grup Tanpa Nama';
                    const id = group.id || '';
                    const isSelected = selectedGroups.some(g => g.id === id);
                    
                    li.innerHTML = `
                        <div class="flex-1 min-w-0 pr-2 text-left">
                            <div class="font-medium text-gray-800 dark:text-gray-200 truncate">
                               ${escapeHtml(subject)}
                            </div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono select-all truncate">
                               ${id}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            ${isSelected ? `
                                <span class="px-2.5 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-md text-[10px] font-semibold">Terpilih</span>
                            ` : `
                                <button onclick="selectGroup('${escapeHtml(id)}', '${escapeHtml(subject)}')" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-[10px] font-semibold transition">Pilih</button>
                            `}
                        </div>
                    `;
                    groupList.appendChild(li);
                });
            } else {
                groupList.innerHTML = '<li class="text-xs text-red-500 py-2 text-center">Gagal memuat daftar grup.</li>';
            }
        } catch (err) {
            console.error('Error loading groups:', err);
            groupList.innerHTML = '<li class="text-xs text-red-500 py-2 text-center">Terjadi kesalahan saat memuat grup.</li>';
        }
    }

    document.getElementById('wa-btn-disconnect').addEventListener('click', async () => {
        if (!confirm('Apakah Anda yakin ingin memutuskan koneksi dan menghapus sesi ini?')) return;
        
        document.getElementById('wa-connected').classList.add('hidden');
        document.getElementById('wa-loading').classList.remove('hidden');
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        
        try {
            const res = await fetch('<?= base_url("setting/wa_disconnect") ?>', {
                method: 'POST'
            });
            const data = await res.json();
            checkWaStatus();
        } catch (err) {
            console.error('Error disconnecting:', err);
            alert('Gagal memutuskan koneksi.');
            checkWaStatus();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        checkWaStatus();
        updateSelectedGroupUI();
    });
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkWaStatus();
        updateSelectedGroupUI();
    }

    function removeGroup(id) {
        Swal.fire({
            title: 'Yakin?',
            text: 'Grup WhatsApp ini akan dihapus dari daftar notifikasi aktif',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch('<?= base_url("setting/delete_wa_group") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            id_group: id
                        })
                    });
                    const data = await res.json();
                    if (data.status) {
                        selectedGroups = selectedGroups.filter(g => g.id !== id);
                        updateSelectedGroupUI();
                        loadGroups();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Grup WhatsApp berhasil dihapus dari notifikasi',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Gagal menghapus grup.'
                        });
                    }
                } catch (err) {
                    console.error('Error deleting group:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus grup.'
                    });
                }
            }
        });
    }

    function loadJam() {
        $.ajax({
            url: '<?= base_url('setting/loadjam') ?>',
            type: 'get',
            dataType: 'html',
            success: function(res) {
                $('#loadJam').html(res)
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        })
    }

    loadJam()

    $(document).on('change', '.jam-input', function() {

        let jam = $(this).data('jam');
        let hari = $(this).data('hari');
        let waktu = $(this).val();


        // validasi ringan
        if (!jam) return;

        $.ajax({
            url: '<?= base_url("setting/simpanJam") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                jam: jam,
                hari: hari,
                waktu: waktu
            },
            success: function(res) {
                if (res.status) {
                    console.log('Jam tersimpan');
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>