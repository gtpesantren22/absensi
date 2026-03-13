</main>
</div>

<!-- Footer -->
<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 px-6">
    <div class="flex flex-col md:flex-row justify-between items-center">

        <!-- KIRI -->
        <p class="text-sm text-gray-600 dark:text-gray-400">
            © 2024 Aplikasi Absensi Sekolah
        </p>

        <!-- KANAN -->
        <a
            href="https://smkdwk.sch.id"
            target="_blank"
            class="text-sm font-medium text-gray-600 dark:text-gray-400 
                   hover:text-emerald-600 dark:hover:text-emerald-400
                   transition">
            Powered by <span class="font-semibold">SMKDWK</span>
        </a>

    </div>
</footer>

</div>

<script src="<?= base_url('assets/'); ?>js/jquery-3.7.1.min.js"></script>
<script src="<?= base_url('assets/'); ?>sw/sweetalert2.all.min.js"></script>
<script src="<?= base_url('assets/'); ?>sw/my-notif.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');
        const main = document.getElementById('mainContent');

        if (!sidebar || !toggle || !overlay) return;

        const isMobile = () => window.innerWidth < 768;

        // =============================
        // INITIAL STATE
        // =============================
        function setInitialState() {
            if (isMobile()) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (main) main.classList.remove('md:ml-64');
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (main) main.classList.add('md:ml-64');
            }
        }

        setInitialState();

        // =============================
        // OPEN MOBILE
        // =============================
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // =============================
        // CLOSE MOBILE
        // =============================
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // =============================
        // TOGGLE BUTTON
        // =============================
        toggle.addEventListener('click', function() {
            if (!isMobile()) return;

            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });

        // =============================
        // OVERLAY CLICK
        // =============================
        overlay.addEventListener('click', function() {
            if (isMobile()) closeSidebar();
        });

        // =============================
        // AUTO CLOSE WHEN CLICK MENU (MOBILE)
        // =============================
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (isMobile()) closeSidebar();
            });
        });

        // =============================
        // RESIZE FIX
        // =============================
        window.addEventListener('resize', function() {
            setInitialState();
        });

    });

    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });

    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-dropdown-toggle]');
        if (!toggle) return;

        const wrapper = toggle.closest('.master-data-dropdown');
        const menu = wrapper.querySelector('[data-dropdown-menu]');
        const icon = toggle.querySelector('[data-dropdown-icon]');

        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });

    // Toggle Dark/Light Mode
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    // Cek preferensi tema
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-lightbulb');
    } else {
        document.documentElement.classList.remove('dark');
        themeIcon.classList.remove('fa-lightbulb');
        themeIcon.classList.add('fa-moon');
    }

    themeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');

        if (document.documentElement.classList.contains('dark')) {
            localStorage.setItem('theme', 'dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-lightbulb');
        } else {
            localStorage.setItem('theme', 'light');
            themeIcon.classList.remove('fa-lightbulb');
            themeIcon.classList.add('fa-moon');
        }
    });

    ;

    // Toggle Dropdown Profil
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', () => {
        // notificationDropdown.classList.add('hidden');
        profileDropdown.classList.add('hidden');
    });

    // Mencegah dropdown tertutup saat klik di dalam dropdown
    // notificationDropdown.addEventListener('click', (e) => e.stopPropagation());
    profileDropdown.addEventListener('click', (e) => e.stopPropagation());

    // Sidebar item active state
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            sidebarItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Tutup sidebar di mobile setelah memilih menu
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.style.display = 'none';
            }
        });
    });

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // Update waktu secara real-time
    function updateDateTime() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const day = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();

        const formattedDate = `${day}, ${date} ${month} ${year}`;

        // Update elemen dengan tanggal (jika ada)
        const dateElements = document.querySelectorAll('.current-date');
        dateElements.forEach(el => {
            el.textContent = formattedDate;
        });
    }

    function tanggalIndo(tanggal, tampilkanHari = false) {
        const hariArray = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        const bulanArray = [
            '', // dummy index 0 biar sama kayak PHP (bulan mulai 1)
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        // input: YYYY-MM-DD
        const dateObj = new Date(tanggal);
        if (isNaN(dateObj)) return tanggal; // fallback aman

        const hari = dateObj.getDay(); // 0 (Minggu) - 6 (Sabtu)
        const tgl = dateObj.getDate(); // 1 - 31
        const bln = dateObj.getMonth() + 1; // 1 - 12
        const thn = dateObj.getFullYear();

        let formatIndo = `${tgl} ${bulanArray[bln]} ${thn}`;

        if (tampilkanHari) {
            formatIndo = `${hariArray[hari]}, ${formatIndo}`;
        }

        return formatIndo;
    }

    function gantiLembaga(id) {
        window.location.href = "<?= site_url('setting/set_lembaga') ?>/" + id;
    }

    // Panggil fungsi update waktu setiap menit
    updateDateTime();
    setInterval(updateDateTime, 60000);
</script>
</body>

</html>