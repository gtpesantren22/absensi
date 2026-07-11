    <!-- Sidebar Drawer Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 z-[90] bg-black/50 hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Sidebar Drawer Menu -->
    <div id="sidebarDrawer" class="fixed top-0 left-0 bottom-0 z-[100] w-72 bg-white dark:bg-slate-900 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Drawer Header -->
        <div class="bg-primary-600 dark:bg-slate-900 px-5 py-6 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-extrabold text-lg">
                    <?= inisial($this->session->userdata('nama_user')) ?>
                </div>
                <div>
                    <h4 class="font-extrabold text-sm leading-tight text-white"><?= $this->session->userdata('nama_user') ?></h4>
                    <span class="text-xs font-bold text-primary-200 dark:text-slate-400 capitalize"><?= $this->session->userdata('level') ?></span>
                </div>
            </div>
            <button id="closeDrawer" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Drawer Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <?php
            $ci =& get_instance();
            $iduser = $ci->session->userdata('id_user');
            $hari = date('l');
            $cekGuru = $ci->db->query("SELECT * FROM user WHERE id_user = '$iduser' ")->row();
            $is_piket = false;
            if ($cekGuru) {
                $cek_piket = $ci->db->query("SELECT * FROM piket WHERE id_guru = '$cekGuru->id_guru' AND hari = '$hari'")->row();
                if ($cek_piket) {
                    $is_piket = true;
                }
            }
            // Ensure menu variable is set
            $menu = isset($menu) ? $menu : '';
            ?>
            <a href="<?= base_url() ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'home') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-home text-lg"></i>
                <span>Beranda</span>
            </a>

            <a href="<?= site_url('kbm/absensi') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'kbm') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-edit text-lg"></i>
                <span>Absensi KBM</span>
            </a>

            <a href="<?= site_url('kbm/hasil') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'hasil') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-history text-lg"></i>
                <span>Hasil Absensi</span>
            </a>

            <a href="<?= site_url('keaktifanguru') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'keaktifanguru') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-202 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-clipboard-list text-lg"></i>
                <span>Keaktifan Saya</span>
            </a>

            <?php if ($is_piket): ?>
            <div class="h-px bg-slate-100 dark:bg-slate-700 my-4"></div>
            <span class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Absensi Guru</span>

            <a href="<?= site_url('absensiguru/pembiasaan') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'absensiguru') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-praying-hands text-lg"></i>
                <span>Pembiasaan Guru</span>
            </a>

            <a href="<?= site_url('kehadiranguru') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'kehadiranguru') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-user-check text-lg"></i>
                <span>Kehadiran Guru</span>
            </a>

            <a href="<?= site_url('mengajar') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'mengajar') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-202 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-chalkboard text-lg"></i>
                <span>Jurnal Mengajar</span>
            </a>

            <div class="h-px bg-slate-100 dark:bg-slate-700 my-4"></div>
            <span class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Siswa & KBM</span>

            <a href="<?= site_url('kbm/control') ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl font-bold transition <?= ($menu == 'absensisiswa') ? 'bg-primary-50 text-primary-600 dark:bg-slate-700 dark:text-primary-400' : 'text-slate-700 dark:text-slate-202 hover:bg-slate-105 dark:hover:bg-slate-700/60' ?>">
                <i class="fas fa-tasks text-lg"></i>
                <span>Kontrol KBM Siswa</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Drawer Footer -->
        <div class="p-4 border-t border-slate-100 dark:border-slate-700/60">
            <button onclick="confirmLogout(event)" class="w-full flex items-center justify-center space-x-2 py-3.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 dark:text-rose-400 font-extrabold transition">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar Aplikasi</span>
            </button>
        </div>
    </div>
