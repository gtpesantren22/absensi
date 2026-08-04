<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Absensi - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= base_url('assets/sw/sweetalert2.min.css') ?>">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
        // Apply theme immediately to avoid white flash
        if (localStorage.getItem('theme') === 'dark' || 
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Scanner frame container styling */
        #reader {
            border: none !important;
        }
        #reader video {
            object-fit: cover !important;
            border-radius: 1rem;
        }

        /* Scanning overlay animation line */
        .scanner-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            background: #2563eb;
            box-shadow: 0 0 10px #3b82f6;
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen pb-28 transition-colors duration-200">

    <!-- Sidebar Drawer Overlay -->
    <!-- Sidebar Drawer -->
    <?php $this->load->view('guru/sidebar'); ?>

    <!-- Top Sticky Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 py-4 shadow-lg sticky top-0 z-50 flex items-center justify-between text-white">
        <div class="flex items-center space-x-3">
            <a href="<?= base_url() ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Scan QR Code</h1>
        </div>
        
        <div class="flex items-center space-x-1">
            <!-- Theme Toggle -->
            <button id="themeToggle" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition">
                <i id="themeIcon" class="fas fa-moon"></i>
            </button>
            
            <!-- Logout Trigger -->
            <button onclick="confirmLogout(event)" class="p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition" title="Keluar Aplikasi">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="px-4 py-6 flex flex-col items-center justify-center">
        
        <!-- Scanner Wrapper Card -->
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xl border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            
            <!-- Context Header Info -->
            <div class="text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold tracking-wider bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-350 uppercase mb-2">
                    Absen <?= ucfirst($jenis) ?>
                </span>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Arahkan kamera ponsel Anda ke kode QR yang ada di layar monitor kantor.
                </p>
            </div>

            <!-- Custom Scanner Container with overlay square target -->
            <div class="relative overflow-hidden rounded-2xl border-4 border-slate-100 dark:border-slate-700 bg-slate-900 flex items-center justify-center aspect-square">
                <!-- Outer scanning container -->
                <div id="reader" class="w-full h-full"></div>
                
                <!-- Laser scan animation overlay -->
                <div class="absolute inset-0 pointer-events-none border border-white/10 rounded-2xl">
                    <div class="scanner-line"></div>
                </div>
            </div>

            <!-- Camera Switcher -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pl-1">
                    Sumber Kamera
                </label>
                <select id="cameraSelect"
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Mengakses kamera...</option>
                </select>
            </div>

            <!-- Scanning Status Alert Box -->
            <div id="scanStatus"
                 class="text-center font-bold text-sm px-4 py-4 rounded-xl transition bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-400">
                <i class="fas fa-camera mr-2"></i>Menunggu inisialisasi kamera...
            </div>

        </div>

    </div>

    <!-- Bottom Navigation Bar (Restored 3-Menu Layout with Drawer Trigger) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700/80 shadow-2xl px-6 py-3 flex items-center justify-around">
        <!-- Home Button -->
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Beranda</span>
        </a>

        <!-- Floating QR Action Button -->
        <div class="relative -mt-8">
            <a href="#" 
               class="w-16 h-16 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-800 scale-105 active:scale-95 transition-transform duration-200">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-primary-600 dark:text-primary-400 whitespace-nowrap">Scan QR</span>
        </div>

        <!-- Menu Button (Trigger Sidebar Drawer) -->
        <button id="menuBottomToggle" onclick="openMenu()" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-extrabold tracking-wide">Menu</span>
        </button>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>
    <script>
        if (typeof Html5QrcodeScannerState === 'undefined') {
            window.Html5QrcodeScannerState = {
                UNKNOWN: 1,
                NOT_STARTED: 2,
                SCANNING: 3,
                PAUSED: 4
            };
        }
        const html5QrCode = new Html5Qrcode("reader");
        const cameraSelect = document.getElementById("cameraSelect");
        const statusEl = document.getElementById("scanStatus");

        function setStatus(text, type = 'info') {
            const base = "text-center font-bold text-sm px-4 py-4 rounded-xl flex items-center justify-center gap-2 ";
            let icon = '<i class="fas fa-info-circle"></i>';
            
            if (type === 'success') {
                statusEl.className = base + "bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300";
                icon = '<i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>';
            } else if (type === 'error') {
                statusEl.className = base + "bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300";
                icon = '<i class="fas fa-exclamation-triangle text-rose-600 dark:text-rose-400"></i>';
            } else if (type === 'loading') {
                statusEl.className = base + "bg-blue-105 text-blue-800 dark:bg-blue-950/80 dark:text-blue-305 bg-blue-50";
                icon = '<i class="fas fa-spinner fa-spin text-blue-600"></i>';
            } else {
                statusEl.className = base + "bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-400";
                icon = '<i class="fas fa-qrcode"></i>';
            }

            statusEl.innerHTML = icon + ' <span>' + text + '</span>';
        }

        // Initialize and list cameras
        Html5Qrcode.getCameras().then(cameras => {
            cameraSelect.innerHTML = '';
            
            if (cameras.length === 0) {
                const opt = document.createElement("option");
                opt.text = "Kamera tidak ditemukan";
                cameraSelect.appendChild(opt);
                setStatus("Perangkat kamera tidak ditemukan.", "error");
                return;
            }

            // Fill select options
            cameras.forEach(cam => {
                const opt = document.createElement("option");
                opt.value = cam.id;
                opt.text = cam.label || `Camera ${cameraSelect.length + 1}`;
                cameraSelect.appendChild(opt);
            });

            // Auto-start using BACK camera facingMode 'environment'
            setStatus("Menginisialisasi kamera...", "loading");
            startScannerEnv();

        }).catch(err => {
            setStatus("Gagal memuat kamera: " + err, "error");
        });

        // Toggle manual camera source select
        cameraSelect.addEventListener("change", function() {
            if (!this.value) return;

            if (html5QrCode.getState() === Html5QrcodeScannerState.SCANNING ||
                html5QrCode.getState() === Html5QrcodeScannerState.PAUSED) {
                html5QrCode.stop().then(startScannerId);
            } else {
                startScannerId();
            }
        });

        // Start scanner with direct camera ID
        function startScannerId() {
            setStatus("Memulai kamera terpilih...", "loading");
            html5QrCode.start(
                cameraSelect.value, 
                {
                    fps: 10,
                    qrbox: (width, height) => {
                        const minDim = Math.min(width, height);
                        const qrboxSize = Math.floor(minDim * 0.65);
                        return { width: qrboxSize, height: qrboxSize };
                    }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                setStatus("Arahkan kamera ke QR Code", "info");
            }).catch(err => {
                setStatus("Kamera gagal diaktifkan: " + err, "error");
            });
        }

        // Auto-start rear camera using FacingMode
        function startScannerEnv() {
            html5QrCode.start(
                { facingMode: "environment" }, 
                {
                    fps: 10,
                    qrbox: (width, height) => {
                        const minDim = Math.min(width, height);
                        const qrboxSize = Math.floor(minDim * 0.65);
                        return { width: qrboxSize, height: qrboxSize };
                    }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                setStatus("Kamera belakang aktif. Arahkan ke QR Code", "info");
                // Select back camera in dropdown if matched
                if (html5QrCode.getActiveCamera()) {
                    cameraSelect.value = html5QrCode.getActiveCamera().id;
                }
            }).catch(err => {
                // Fallback to start with first listed camera
                if (cameraSelect.options.length > 0 && cameraSelect.options[0].value) {
                    cameraSelect.selectedIndex = 0;
                    startScannerId();
                } else {
                    setStatus("Kamera tidak merespon izin akses.", "error");
                }
            });
        }

        function onScanFailure(error) {
            // Silently ignore scanner lookup frame fails
        }

        function onScanSuccess(decodedText) {
            setStatus("QR Code terdeteksi!", "success");
            
            // Stop camera scan immediately to avoid duplicate submits
            if (html5QrCode.getState() === Html5QrcodeScannerState.SCANNING) {
                html5QrCode.stop();
            }
 
            // Show SweetAlert2 loading indicator
            Swal.fire({
                title: 'Memproses Absensi',
                html: 'Mohon tunggu sebentar, kehadiran Anda sedang diproses...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
 
            setStatus("Memproses absensi...", "loading");
            sendScanPayload(decodedText, null, null, null);
        }

        function sendScanPayload(decodedText, lat, lon, accuracy) {
            setStatus("Mengirim data kehadiran...", "loading");

            fetch("<?= base_url('qrcode/sendScan/' . $jenis) ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    token: decodedText,
                    lat: lat,
                    lon: lon,
                    accuracy: accuracy
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.valid) {
                    setStatus(res.message, "success");
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        location.href = '<?= base_url() ?>';
                    }, 2000);
                } else {
                    setStatus(res.message, "error");
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan Gagal',
                        text: res.message
                    }).then(() => {
                        // Restart scanner on failure so user can try again
                        startScannerEnv();
                    });
                }
            })
            .catch(err => {
                setStatus("Terjadi kesalahan koneksi", "error");
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal menghubungi server. Silakan coba lagi.'
                }).then(() => {
                    startScannerEnv();
                });
            });
        }

        // Theme switching logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function applyTheme() {
            const isDark = localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeIcon.className = 'fa-regular fa-sun text-lg';
            } else {
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'fa-regular fa-moon text-lg';
            }
        }

        themeToggle.addEventListener('click', () => {
            const isCurrentlyDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isCurrentlyDark ? 'light' : 'dark');
            applyTheme();
        });

        applyTheme();

        // Confirm logout using SweetAlert2
        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem absensi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url("auth/logout") ?>';
                }
            });
        }

        // Sidebar Drawer toggle logic
        const sidebarDrawer = document.getElementById('sidebarDrawer');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const menuToggle = document.getElementById('menuToggle');
        const closeDrawer = document.getElementById('closeDrawer');

        function openMenu() {
            sidebarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sidebarOverlay.classList.add('opacity-100');
                sidebarDrawer.classList.remove('-translate-x-full');
            }, 50);
        }

        function closeMenu() {
            sidebarDrawer.classList.add('-translate-x-full');
            sidebarOverlay.classList.remove('opacity-100');
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', openMenu);
        }
        const menuBottomToggle = document.getElementById('menuBottomToggle');
        if (menuBottomToggle) {
            menuBottomToggle.addEventListener('click', openMenu);
        }
        closeDrawer.addEventListener('click', closeMenu);
        sidebarOverlay.addEventListener('click', closeMenu);
    </script>
</body>
</html>
