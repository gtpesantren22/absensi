<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-md shadow-primary-500/20">
                <i class="fas fa-shield-alt text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Laporan Pelanggaran Siswa</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pencatatan pelanggaran tata tertib dan poin santri terintegrasi API</p>
            </div>
        </div>
    </div>

    <!-- Status Koneksi API -->
    <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold <?= $api_connected ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400' ?>">
        <span class="w-2 h-2 rounded-full <?= $api_connected ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
        <span><?= $api_connected ? 'API Pelanggaran Terhubung' : 'Server API Standby / Offline' ?></span>
    </div>
</div>

<?php if (!$api_connected && !empty($api_error)): ?>
<div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 flex items-start gap-3 text-amber-800 dark:text-amber-300 text-sm">
    <i class="fas fa-exclamation-triangle mt-0.5 text-amber-600 dark:text-amber-400 flex-shrink-0"></i>
    <div>
        <span class="font-bold">Perhatian:</span> <?= htmlspecialchars($api_error) ?>
        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">Pastikan server Laravel API telah dijalankan dengan perintah <code class="px-1 py-0.5 bg-amber-100 dark:bg-amber-900/50 rounded font-mono">php artisan serve</code>.</p>
    </div>
</div>
<?php endif; ?>

<!-- Form Container Card -->
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Card Header Banner -->
        <div class="px-6 py-5 bg-gray-50/70 dark:bg-gray-700/40 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Formulir Pengaduan &amp; Laporan</span>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-clock mr-1"></i> <?= date('d M Y') ?></span>
        </div>

        <form id="formPelanggaran" class="p-6 md:p-8 space-y-6">
            
            <!-- STEP 1: Pilih Santri / Siswa -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    1. Identitas Santri / Siswa <span class="text-red-500">*</span>
                </label>

                <!-- Hidden Input ID Santri -->
                <input type="hidden" name="santri_id" id="santri_id" required>

                <!-- Search Input Container -->
                <div id="searchSantriContainer" class="relative">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" id="inputSearchSantri" autocomplete="off"
                            placeholder="Ketik Nama, NIS, atau Kamar Santri (contoh: Budi, Ahmad, dll)..." 
                            class="w-full pl-10 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        <div id="loadingSearchSantri" class="absolute inset-y-0 right-0 pr-3.5 flex items-center hidden">
                            <i class="fas fa-circle-notch fa-spin text-primary-500 text-sm"></i>
                        </div>
                    </div>

                    <!-- Dropdown Results -->
                    <div id="dropdownSantriResults" class="absolute left-0 right-0 top-full mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 max-h-64 overflow-y-auto z-50 hidden divide-y divide-gray-100 dark:divide-gray-700/60">
                        <!-- Items rendered via JS -->
                    </div>
                </div>

                <!-- Selected Santri Card Preview -->
                <div id="selectedSantriCard" class="hidden p-4 rounded-xl bg-primary-50/50 dark:bg-gray-900/50 border border-primary-200 dark:border-gray-700 flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-primary-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-primary-500/20" id="previewAvatar">
                            S
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-800 dark:text-gray-100 text-base" id="previewNama">-</h4>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300" id="previewNis">-</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                <span id="previewKelas">-</span> <span class="mx-1">•</span> <span id="previewKamar">-</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="resetSelectedSantri()" class="px-3 py-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:bg-primary-100/50 dark:hover:bg-primary-950/50 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-sync-alt"></i> Ganti Santri
                    </button>
                </div>
            </div>

            <!-- STEP 2: Pilih Jenis Pelanggaran (Poin) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    2. Jenis Pelanggaran &amp; Bobot Poin <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="point_id" id="point_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none">
                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                        <?php if (!empty($points)): ?>
                            <?php foreach ($points as $p): ?>
                                <?php 
                                    $pt = (int)($p['point'] ?? ($p['jumlah_poin'] ?? ($p['jumlah'] ?? 0)));
                                    $kat = $p['kategori'] ?? ($p['jenis'] ?? '');
                                    $kode = !empty($p['kode']) ? '[' . $p['kode'] . '] ' : '';
                                ?>
                                <option value="<?= $p['id'] ?>" data-point="<?= $pt ?>" data-kategori="<?= htmlspecialchars($kat) ?>" data-kode="<?= htmlspecialchars($p['kode'] ?? '') ?>">
                                    <?= htmlspecialchars($kode . $p['nama']) ?> (<?= $pt ?> Poin<?= !empty($kat) ? ' - ' . htmlspecialchars($kat) : '' ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Info Poin Terpilih -->
                <div id="pointInfoBanner" class="hidden mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400" id="pointKategoriLabel">Kategori: -</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-900/50" id="pointBadge">+0 Poin</span>
                </div>
            </div>

            <!-- STEP 3: Tanggal, Lokasi & Pelapor -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        3. Tanggal Kejadian <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        4. Lokasi Kejadian <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="lokasi" id="lokasi" list="listLokasi" required
                        placeholder="Contoh: Gerbang Utama, Kelas..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    <datalist id="listLokasi">
                        <option value="Gerbang Utama"></option>
                        <option value="Kamar / Asrama"></option>
                        <option value="Masjid"></option>
                        <option value="Kantin"></option>
                        <option value="Area Kelas / Gedung"></option>
                        <option value="Luar Lingkungan Pondok"></option>
                        <option value="Pos Keamanan"></option>
                    </datalist>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        5. Nama Pelapor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pelapor" id="pelapor" required
                        value="<?= htmlspecialchars($this->session->userdata('nama_user') ?? '') ?>"
                        placeholder="Nama pelapor (contoh: Ustadz Husein)..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                </div>
            </div>

            <!-- STEP 4: Kronologi Kejadian -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    6. Kronologi Kejadian <span class="text-red-500">*</span>
                </label>
                <textarea name="kronologi" id="kronologi" rows="4" required
                    placeholder="Jelaskan kronologi kejadian pelanggaran secara jelas, waktu, dan rincian perbuatan yang dilakukan santri..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"></textarea>
            </div>

            <!-- STEP 5: Saksi-Saksi Kejadian -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    7. Saksi-Saksi Kejadian (Opsional)
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Ketik nama saksi (contoh: Ustadz Fauzi, Ahmad Dahlan) lalu tekan tombol <b>Tambah</b> atau <b>Enter</b>.</p>
                
                <div class="flex gap-2 mb-3">
                    <input type="text" id="inputSaksi" placeholder="Masukkan nama saksi..."
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    <button type="button" onclick="addSaksiFromInput()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold text-sm transition-colors flex items-center gap-1.5">
                        <i class="fas fa-plus text-xs"></i> Tambah
                    </button>
                </div>

                <!-- Tags Container -->
                <div id="saksiTagsContainer" class="flex flex-wrap gap-2 min-h-[36px] p-2.5 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/30 items-center">
                    <span class="text-xs text-gray-400 italic">Belum ada saksi yang ditambahkan.</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="button" onclick="resetFormFull()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-semibold transition-all">
                    Reset Form
                </button>
                <button type="submit" id="btnSubmitPelanggaran" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-md shadow-primary-500/20 hover:shadow-primary-500/30 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Laporan Pelanggaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>

<script>
    // State
    let searchTimeout = null;
    let saksiList = [];

    // Initialize default saksi from rendered DOM
    document.querySelectorAll('.saksi-tag').forEach(el => {
        const name = el.getAttribute('data-name');
        if (name && !saksiList.includes(name)) {
            saksiList.push(name);
        }
    });

    // 1. Santri Search & Selection
    const inputSearchSantri = document.getElementById('inputSearchSantri');
    const dropdownResults = document.getElementById('dropdownSantriResults');
    const loadingSearch = document.getElementById('loadingSearchSantri');
    const santriIdInput = document.getElementById('santri_id');
    const searchContainer = document.getElementById('searchSantriContainer');
    const selectedCard = document.getElementById('selectedSantriCard');

    inputSearchSantri.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(searchTimeout);

        if (q.length < 2) {
            dropdownResults.classList.add('hidden');
            dropdownResults.innerHTML = '';
            loadingSearch.classList.add('hidden');
            return;
        }

        loadingSearch.classList.remove('hidden');

        searchTimeout = setTimeout(() => {
            fetch(`<?= site_url('pelanggaran/search_santri?search=') ?>` + encodeURIComponent(q))
                .then(res => res.json())
                .then(res => {
                    loadingSearch.classList.add('hidden');
                    if (res.status && Array.isArray(res.data) && res.data.length > 0) {
                        renderSantriDropdown(res.data);
                    } else {
                        dropdownResults.innerHTML = `
                            <div class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ditemukan santri dengan kata kunci "${q}"
                            </div>
                        `;
                        dropdownResults.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    loadingSearch.classList.add('hidden');
                    dropdownResults.innerHTML = `
                        <div class="p-3 text-center text-xs text-red-500">
                            Gagal menghubungi API Santri. Pastikan server API aktif.
                        </div>
                    `;
                    dropdownResults.classList.remove('hidden');
                });
        }, 300);
    });

    function renderSantriDropdown(items) {
        dropdownResults.innerHTML = '';
        items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'p-3 hover:bg-primary-50 dark:hover:bg-gray-700/60 cursor-pointer flex items-center justify-between transition-colors';
            row.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 font-bold text-xs flex items-center justify-center">
                        ${item.nama ? item.nama.charAt(0).toUpperCase() : 'S'}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">${item.nama}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            NIS: ${item.nis || '-'} ${item.kelas ? ' • ' + item.kelas : ''} ${item.kamar ? ' • ' + item.kamar : ''}
                        </div>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">Pilih</span>
            `;
            row.onclick = () => selectSantri(item);
            dropdownResults.appendChild(row);
        });
        dropdownResults.classList.remove('hidden');
    }

    function selectSantri(item) {
        santriIdInput.value = item.id;
        document.getElementById('previewNama').textContent = item.nama;
        document.getElementById('previewNis').textContent = 'NIS: ' + (item.nis || '-');
        document.getElementById('previewKelas').textContent = item.kelas ? 'Kelas ' + item.kelas : 'Santri Terdaftar';
        document.getElementById('previewKamar').textContent = item.kamar ? 'Kamar: ' + item.kamar : 'Pondok Pesantren';
        document.getElementById('previewAvatar').textContent = item.nama ? item.nama.charAt(0).toUpperCase() : 'S';

        searchContainer.classList.add('hidden');
        selectedCard.classList.remove('hidden');
        dropdownResults.classList.add('hidden');
        inputSearchSantri.value = '';
    }

    function resetSelectedSantri() {
        santriIdInput.value = '';
        selectedCard.classList.add('hidden');
        searchContainer.classList.remove('hidden');
        inputSearchSantri.focus();
    }

    // Close dropdown when clicked outside
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            dropdownResults.classList.add('hidden');
        }
    });

    // 2. Point selection info
    const selectPoint = document.getElementById('point_id');
    const pointInfo = document.getElementById('pointInfoBanner');
    const pointKategori = document.getElementById('pointKategoriLabel');
    const pointBadge = document.getElementById('pointBadge');

    selectPoint.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.value) {
            const pt = opt.getAttribute('data-point') || 0;
            const kat = opt.getAttribute('data-kategori') || 'Pelanggaran';
            pointKategori.textContent = 'Kategori: ' + kat;
            pointBadge.textContent = '+' + pt + ' Poin';
            pointInfo.classList.remove('hidden');
        } else {
            pointInfo.classList.add('hidden');
        }
    });

    // 3. Saksi Management
    const inputSaksi = document.getElementById('inputSaksi');
    inputSaksi.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSaksiFromInput();
        }
    });

    function addSaksiFromInput() {
        const val = inputSaksi.value.trim();
        if (!val) return;

        if (saksiList.includes(val)) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Nama saksi tersebut sudah ditambahkan.',
                timer: 1500,
                showConfirmButton: false
            });
            inputSaksi.value = '';
            return;
        }

        saksiList.push(val);
        renderSaksiTags();
        inputSaksi.value = '';
        inputSaksi.focus();
    }

    function removeSaksiTag(btn) {
        const tag = btn.closest('.saksi-tag');
        const name = tag.getAttribute('data-name');
        saksiList = saksiList.filter(s => s !== name);
        renderSaksiTags();
    }

    function renderSaksiTags() {
        const container = document.getElementById('saksiTagsContainer');
        container.innerHTML = '';
        if (saksiList.length === 0) {
            container.innerHTML = '<span class="text-xs text-gray-400 italic">Belum ada saksi tambahan yang dimasukkan.</span>';
            return;
        }

        saksiList.forEach(name => {
            const tag = document.createElement('div');
            tag.className = 'saksi-tag inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800 text-xs font-semibold';
            tag.setAttribute('data-name', name);
            tag.innerHTML = `
                <i class="fas fa-user-check text-[10px]"></i>
                <span>${name}</span>
                <button type="button" onclick="removeSaksiTag(this)" class="hover:text-primary-900 dark:hover:text-primary-100 ml-1">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            `;
            container.appendChild(tag);
        });
    }

    function resetFormFull() {
        document.getElementById('formPelanggaran').reset();
        resetSelectedSantri();
        pointInfo.classList.add('hidden');
        saksiList = [];
        renderSaksiTags();
    }

    // 4. Form Submit Handler
    document.getElementById('formPelanggaran').addEventListener('submit', function(e) {
        e.preventDefault();

        const santri_id = document.getElementById('santri_id').value;
        const point_id  = document.getElementById('point_id').value;
        const tanggal   = document.getElementById('tanggal').value;
        const lokasi    = document.getElementById('lokasi').value;
        const kronologi = document.getElementById('kronologi').value;
        const pelapor   = document.getElementById('pelapor').value;

        if (!santri_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Santri Belum Dipilih',
                text: 'Silakan cari dan pilih santri yang melakukan pelanggaran terlebih dahulu.',
                confirmButtonColor: '#2563eb'
            });
            inputSearchSantri.focus();
            return;
        }

        if (!point_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Poin Belum Dipilih',
                text: 'Silakan tentukan jenis pelanggaran dan bobot poin.',
                confirmButtonColor: '#2563eb'
            });
            selectPoint.focus();
            return;
        }

        if (!pelapor) {
            Swal.fire({
                icon: 'warning',
                title: 'Pelapor Wajib Diisi',
                text: 'Silakan masukkan nama pelapor kejadian.',
                confirmButtonColor: '#2563eb'
            });
            document.getElementById('pelapor').focus();
            return;
        }

        const payload = {
            santri_id: santri_id,
            point_id: parseInt(point_id),
            tanggal: tanggal,
            lokasi: lokasi,
            kronologi: kronologi,
            pelapor: pelapor,
            saksi: saksiList
        };

        Swal.fire({
            title: 'Kirim Laporan Pelanggaran?',
            text: 'Data pelanggaran ini akan diteruskan dan dicatat langsung ke sistem pembinaan santri.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Laporan',
            cancelButtonText: 'Periksa Lagi',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = document.getElementById('btnSubmitPelanggaran');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengirim Laporan...';

                fetch('<?= site_url('pelanggaran/submit') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data: data })))
                .then(result => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;

                    if (result.ok && result.data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Laporan Berhasil Terkirim!',
                            text: result.data.message || 'Laporan pelanggaran santri berhasil dicatat ke sistem.',
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            resetFormFull();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim Laporan',
                            text: result.data.message || 'Terjadi kesalahan saat memproses laporan ke API luar.',
                            confirmButtonColor: '#2563eb'
                        });
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Jaringan',
                        text: 'Tidak dapat menghubungi server lokal atau API luar. Silakan periksa koneksi Anda.',
                        confirmButtonColor: '#2563eb'
                    });
                });
            }
        });
    });
</script>
