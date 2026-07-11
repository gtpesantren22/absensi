<?php $this->load->view('admin/head'); ?>

<!-- Header Dashboard -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold">Monitoring Keaktifan & Jurnal Guru</h2>
        <p class="text-gray-600 dark:text-gray-400">Pantau kehadiran harian, jam mengajar, dan isi jurnal KBM guru secara real-time.</p>
    </div>
</div>

<!-- Filter Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Date selector -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-4">
        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pilih Tanggal</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-450">
                <i class="far fa-calendar-alt"></i>
            </span>
            <input type="date" id="reportDate" 
                   class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-650 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500"
                   value="<?= date('Y-m-d') ?>">
        </div>
    </div>
    
    <!-- Search teacher -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-4 md:col-span-2">
        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Cari Nama Guru</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-450">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="searchTeacher" 
                   class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-650 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500"
                   placeholder="Masukkan nama guru untuk memfilter...">
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6" id="kpiContainer">
    <!-- Total Guru Terjadwal -->
    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Guru Terjadwal</span>
            <span id="kpiScheduled" class="text-2xl font-extrabold text-gray-800 dark:text-slate-100">-</span>
        </div>
    </div>

    <!-- Guru Hadir -->
    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            <i class="fas fa-user-check"></i>
        </div>
        <div>
            <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Guru Hadir (Datang)</span>
            <span id="kpiPresent" class="text-2xl font-extrabold text-gray-800 dark:text-slate-100">-</span>
        </div>
    </div>

    <!-- JP Completion Progress -->
    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">KBM Terisi</span>
                <span id="kpiJurnalFilled" class="text-lg font-extrabold text-gray-800 dark:text-slate-100">-</span>
            </div>
        </div>
        <div class="w-24 bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
            <div id="kpiProgressBar" class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Teachers Section Title -->
<div class="mb-4">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center">
        <i class="fas fa-clipboard-list mr-2 text-primary-500"></i>Hasil Pemantauan Guru
    </h3>
</div>

<!-- Teachers Cards Grid -->
<div id="teachersList" class="space-y-4">
    <!-- Loading skeleton -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm animate-pulse space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-48"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                </div>
            </div>
            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
        </div>
    </div>
</div>

<script>
    const API_URL = '<?= site_url("keaktifanguru/getKeaktifanData") ?>';
    let allData = [];

    const dateInput = document.getElementById('reportDate');
    const searchInput = document.getElementById('searchTeacher');

    dateInput.addEventListener('change', loadData);
    searchInput.addEventListener('input', renderTeachers);

    function loadData() {
        const selectedDate = dateInput.value;
        const container = document.getElementById('teachersList');
        container.innerHTML = `
            <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-gray-200 dark:border-slate-700 shadow-sm text-center">
                <i class="fas fa-spinner fa-spin text-2xl text-primary-600 mb-2"></i>
                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Mengambil data keaktifan...</p>
            </div>`;

        fetch(`${API_URL}?date=${selectedDate}`)
            .then(res => res.json())
            .then(data => {
                allData = data;
                renderKPIs(data);
                renderTeachers();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-gray-200 dark:border-slate-700 shadow-sm text-center">
                        <i class="fas fa-exclamation-triangle text-2xl text-rose-500 mb-2"></i>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Gagal memuat data. Silakan coba kembali.</p>
                    </div>`;
            });
    }

    function renderKPIs(data) {
        let totalScheduled = 0;
        let presentCount = 0;
        let totalJp = 0;
        let filledJp = 0;

        data.forEach(teacher => {
            if (teacher.total_jp_jadwal > 0) {
                totalScheduled++;
            }
            if (teacher.status_kehadiran === 'H') {
                presentCount++;
            }
            totalJp += parseInt(teacher.total_jp_jadwal);
            filledJp += parseInt(teacher.total_jp_terisi);
        });

        document.getElementById('kpiScheduled').innerText = totalScheduled;
        document.getElementById('kpiPresent').innerText = presentCount;

        const rate = totalJp > 0 ? Math.round((filledJp / totalJp) * 100) : 0;
        document.getElementById('kpiJurnalFilled').innerText = `${filledJp} / ${totalJp} JP (${rate}%)`;
        document.getElementById('kpiProgressBar').style.width = `${rate}%`;
    }

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
    }

    function renderTeachers() {
        const container = document.getElementById('teachersList');
        const searchVal = searchInput.value.toLowerCase().trim();

        const filtered = allData.filter(t => t.nama.toLowerCase().includes(searchVal));

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-gray-200 dark:border-slate-700 text-center text-gray-500 dark:text-gray-400 shadow-sm">
                    <i class="fas fa-search text-3xl mb-2 text-gray-400"></i>
                    <p class="text-sm font-bold">Guru tidak ditemukan.</p>
                </div>`;
            return;
        }

        let html = '';
        filtered.forEach((teacher, idx) => {
            let statusBadge = '';
            if (teacher.status_kehadiran === 'H') {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/30">Hadir</span>`;
            } else if (teacher.status_kehadiran === 'S') {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-600 dark:bg-yellow-950/40 dark:text-yellow-400 border border-yellow-200/30">Sakit</span>`;
            } else if (teacher.status_kehadiran === 'I') {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200/30">Izin</span>`;
            } else if (teacher.status_kehadiran === 'A') {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/30">Alfa</span>`;
            } else {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-450 border border-gray-200/30">Belum Presensi</span>`;
            }

            const scheduledJp = parseInt(teacher.total_jp_jadwal);
            const completedJp = parseInt(teacher.total_jp_terisi);
            const completionRate = scheduledJp > 0 ? Math.round((completedJp / scheduledJp) * 100) : 0;
            
            let completionBadge = '';
            if (scheduledJp > 0) {
                completionBadge = `
                    <div class="flex items-center space-x-2 text-xs font-bold text-gray-500 dark:text-gray-400">
                        <span>Progress JP: ${completedJp}/${scheduledJp}</span>
                        <span class="px-1.5 py-0.5 rounded bg-gray-105 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-extrabold text-[10px]">${completionRate}%</span>
                    </div>`;
            } else {
                completionBadge = `<span class="text-xs text-gray-450 italic">Tidak ada jadwal mengajar</span>`;
            }

            let classesHtml = '';
            if (teacher.classes.length > 0) {
                classesHtml = `<div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/60 hidden" id="detail_${idx}">`;
                teacher.classes.forEach(c => {
                    const filledIcon = c.is_terisi 
                        ? `<span class="text-emerald-500 text-sm flex items-center space-x-1 font-bold"><i class="fas fa-check-circle mr-1"></i> Terisi</span>`
                        : `<span class="text-rose-500 text-sm flex items-center space-x-1 font-bold"><i class="fas fa-times-circle mr-1"></i> Belum Terisi</span>`;

                    let studentStatsHtml = '';
                    if (c.siswa_stats) {
                        studentStatsHtml = `
                            <div class="mt-3 grid grid-cols-5 gap-1 text-[11px] font-bold text-center">
                                <div class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 py-1 rounded">Hadir: ${c.siswa_stats.hadir}</div>
                                <div class="bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400 py-1 rounded">Sakit: ${c.siswa_stats.sakit}</div>
                                <div class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 py-1 rounded">Izin: ${c.siswa_stats.izin}</div>
                                <div class="bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 py-1 rounded">Alfa: ${c.siswa_stats.alpha}</div>
                                <div class="bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 py-1 rounded">Telat: ${c.siswa_stats.telat}</div>
                            </div>`;
                    }

                    classesHtml += `
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl mb-3 border border-gray-100 dark:border-gray-800">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-extrabold text-sm text-gray-850 dark:text-gray-200">${c.kelas} - ${c.mapel}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-bold block mt-0.5"><i class="far fa-clock mr-1"></i> Jam ke ${c.jam_ke}</span>
                                </div>
                                ${filledIcon}
                            </div>
                            <div class="mt-2.5 text-xs bg-white dark:bg-slate-800 p-3 rounded-lg border border-gray-100 dark:border-slate-700 text-gray-600 dark:text-gray-300">
                                <span class="block text-[10px] font-extrabold uppercase text-gray-400 mb-1">Jurnal / Ringkasan Materi</span>
                                <p class="font-medium whitespace-pre-line leading-relaxed">${c.isi_jurnal}</p>
                            </div>
                            ${studentStatsHtml}
                        </div>`;
                });
                classesHtml += `</div>`;
            }

            const initials = getInitials(teacher.nama);
            const hasClasses = teacher.classes.length > 0;
            const expandBtn = hasClasses 
                ? `<button onclick="toggleDetails(${idx})" class="p-1.5 rounded-full hover:bg-gray-105 dark:hover:bg-gray-700 transition" id="btn_${idx}">
                       <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="icon_${idx}"></i>
                   </button>` 
                : '';

            html += `
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 flex items-center justify-center font-extrabold text-sm shadow-inner">
                                ${initials}
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-extrabold text-sm text-gray-800 dark:text-slate-200 leading-snug">${teacher.nama}</h4>
                                <div class="flex items-center space-x-2">
                                    ${statusBadge}
                                    ${completionBadge}
                                </div>
                            </div>
                        </div>
                        ${expandBtn}
                    </div>
                    ${classesHtml}
                </div>`;
        });

        container.innerHTML = html;
    }

    function toggleDetails(idx) {
        const detail = document.getElementById(`detail_${idx}`);
        const icon = document.getElementById(`icon_${idx}`);
        
        if (detail.classList.contains('hidden')) {
            detail.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            detail.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php $this->load->view('admin/foot'); ?>
