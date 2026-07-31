<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Pembiasaan Siswa - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
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
        
        #reader {
            border: none !important;
        }
        #reader video {
            object-fit: cover !important;
            border-radius: 1rem;
        }

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

    <!-- Sidebar Drawer Menu -->
    <?php $this->load->view('guru/sidebar'); ?>

    <!-- Top Sticky Header Bar -->
    <div class="bg-primary-600 dark:bg-slate-900 px-4 py-4 shadow-lg sticky top-0 z-50 flex items-center justify-between text-white max-w-xl mx-auto rounded-b-3xl">
        <div class="flex items-center space-x-3">
            <a href="<?= site_url('qrcode/pembiasaan_siswa_hasil') ?>" class="p-2 rounded-full hover:bg-white/10 transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold tracking-wide">Absen Pembiasaan Siswa</h1>
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
    <div class="px-4 py-6 max-w-xl mx-auto space-y-6 flex flex-col items-center justify-center">
        
        <!-- Scanner Wrapper Card -->
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xl border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            
            <!-- Mode Segment Control (Masuk / Pulang) -->
            <div class="bg-slate-100 dark:bg-slate-950 p-1.5 rounded-2xl flex">
                <button id="btnModeMasuk" onclick="setMode('masuk')"
                        class="flex-1 text-center py-2.5 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white text-emerald-600 shadow-sm dark:bg-slate-800 dark:text-emerald-400">
                    <i class="fas fa-sign-in-alt mr-1"></i> Absen Masuk
                </button>
                <button id="btnModePulang" onclick="setMode('pulang')"
                        class="flex-1 text-center py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    <i class="fas fa-sign-out-alt mr-1"></i> Absen Pulang
                </button>
            </div>

            <!-- Tab Selection (Kamera HP vs USB Scanner) -->
            <div class="border-b border-slate-200 dark:border-slate-700 flex">
                <button id="tabCamera" onclick="switchTab('camera')"
                        class="flex-1 pb-3 text-center text-xs font-extrabold border-b-2 border-primary-500 text-primary-600 dark:text-primary-400">
                    <i class="fas fa-camera mr-1"></i> Kamera HP / Webcam
                </button>
                <button id="tabUsb" onclick="switchTab('usb')"
                        class="flex-1 pb-3 text-center text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400">
                    <i class="fas fa-keyboard mr-1"></i> USB Scanner Alat
                </button>
            </div>

            <!-- TAB 1 CONTENT: CAMERA SCANNER -->
            <div id="contentCamera" class="space-y-6">
                <!-- Custom Scanner Container with overlay square target -->
                <div class="relative overflow-hidden rounded-2xl border-4 border-slate-100 dark:border-slate-700 bg-slate-900 flex items-center justify-center aspect-square">
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
            </div>

            <!-- TAB 2 CONTENT: USB KEYBOARD SCANNER -->
            <div id="contentUsb" class="hidden py-8 flex flex-col items-center justify-center text-center space-y-4">
                <div class="w-24 h-24 rounded-full bg-primary-50 dark:bg-slate-700 flex items-center justify-center text-primary-500 dark:text-primary-400 text-4xl shadow-inner animate-pulse">
                    <i class="fas fa-id-card"></i>
                </div>
                
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-white">Siap Memindai Kartu</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs mt-1">
                        Arahkan alat scanner/ketikkan kartu ke pembaca. Kursor otomatis terfokus di bawah ini.
                    </p>
                </div>

                <!-- Hidden / Styled USB Input -->
                <div class="w-full">
                    <form id="usbScanForm" onsubmit="handleUsbSubmit(event)" class="relative">
                        <input type="text" id="usbInput" autocomplete="off"
                               class="w-full text-center tracking-widest font-mono text-sm px-4 py-3 bg-slate-100 dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 rounded-xl placeholder-slate-400 pl-10" 
                               placeholder="Tap kartu santri...">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-barcode"></i>
                        </span>
                    </form>
                </div>
            </div>

            <!-- Status Indicator -->
            <div id="scanStatus" class="text-center font-bold text-sm px-4 py-4 rounded-xl flex items-center justify-center gap-2 bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-400">
                <i class="fas fa-qrcode"></i> <span>Menunggu scan kartu...</span>
            </div>

        </div>
    </div>

    <!-- Floating Actions & Footer Menu -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-800/80 px-6 py-4 flex items-center justify-around shadow-2xl max-w-xl mx-auto rounded-t-3xl">
        <a href="<?= base_url() ?>" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs font-bold tracking-wide">Beranda</span>
        </a>
        
        <div class="relative -mt-8">
            <a href="#" class="w-16 h-16 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-800 scale-105">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-extrabold text-primary-600 dark:text-primary-400 whitespace-nowrap">Scan Siswa</span>
        </div>

        <button onclick="openMenu()" class="flex flex-col items-center space-y-1 text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 w-16">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs font-bold tracking-wide">Menu</span>
        </button>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>
    <script>
        // Scanner Configuration
        let html5QrCode;
        if (document.getElementById("reader")) {
            html5QrCode = new Html5Qrcode("reader");
        }
        
        const cameraSelect = document.getElementById("cameraSelect");
        const statusEl = document.getElementById("scanStatus");
        const usbInput = document.getElementById("usbInput");

        let activeTab = 'camera';
        let currentMode = 'masuk'; // 'masuk' atau 'pulang'
        let isProcessing = false;

        if (typeof Html5QrcodeScannerState === 'undefined') {
            window.Html5QrcodeScannerState = {
                UNKNOWN: 1,
                NOT_STARTED: 2,
                SCANNING: 3,
                PAUSED: 4
            };
        }

        // Web Audio API Synthesizer Beep Feedback
        function playBeepSound(type = 'success') {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);

                if (type === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
                    gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.25);
                    osc.start(audioCtx.currentTime);
                    osc.stop(audioCtx.currentTime + 0.25);
                } else {
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(260, audioCtx.currentTime); // Low note
                    gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.4);
                    osc.start(audioCtx.currentTime);
                    osc.stop(audioCtx.currentTime + 0.4);
                }
            } catch (e) {
                console.warn('Audio synthesis error:', e);
            }
        }

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
                statusEl.className = base + "bg-blue-50 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300";
                icon = '<i class="fas fa-spinner fa-spin text-blue-600"></i>';
            } else {
                statusEl.className = base + "bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-400";
                icon = '<i class="fas fa-qrcode"></i>';
            }

            statusEl.innerHTML = icon + ' <span>' + text + '</span>';
        }

        // Set Presensi Mode: Masuk vs Pulang
        function setMode(mode) {
            currentMode = mode;
            const btnMasuk = document.getElementById("btnModeMasuk");
            const btnPulang = document.getElementById("btnModePulang");

            if (mode === 'masuk') {
                btnMasuk.className = "flex-1 text-center py-2.5 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white text-emerald-600 shadow-sm dark:bg-slate-800 dark:text-emerald-400";
                btnPulang.className = "flex-1 text-center py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300";
            } else {
                btnPulang.className = "flex-1 text-center py-2.5 rounded-xl text-sm font-extrabold transition-all duration-300 bg-white text-blue-600 shadow-sm dark:bg-slate-800 dark:text-blue-400";
                btnMasuk.className = "flex-1 text-center py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300";
            }

            if (activeTab === 'usb') {
                focusUsbInput();
            }
        }

        // Switch Tabs: Camera vs USB Scanner
        function switchTab(tab) {
            activeTab = tab;
            const tabCam = document.getElementById("tabCamera");
            const tabUsb = document.getElementById("tabUsb");
            const contentCam = document.getElementById("contentCamera");
            const contentUsb = document.getElementById("contentUsb");

            if (tab === 'camera') {
                tabCam.className = "flex-1 pb-3 text-center text-xs font-extrabold border-b-2 border-primary-500 text-primary-600 dark:text-primary-400";
                tabUsb.className = "flex-1 pb-3 text-center text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400";
                contentCam.classList.remove("hidden");
                contentUsb.classList.add("hidden");
                
                // Initialize/Start camera
                initCameraScanner();
            } else {
                tabUsb.className = "flex-1 pb-3 text-center text-xs font-extrabold border-b-2 border-primary-500 text-primary-600 dark:text-primary-400";
                tabCam.className = "flex-1 pb-3 text-center text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400";
                contentUsb.classList.remove("hidden");
                contentCam.classList.add("hidden");
                
                // Stop camera scanner
                stopCameraScanner();
                
                // Focus USB Input field
                focusUsbInput();
            }
        }

        function focusUsbInput() {
            setTimeout(() => {
                if (usbInput) {
                    usbInput.value = '';
                    usbInput.focus();
                }
            }, 100);
        }

        // Automatically keep USB input focused
        document.addEventListener("click", function(e) {
            if (activeTab === 'usb' && e.target !== usbInput) {
                focusUsbInput();
            }
        });

        // Initialize camera listing
        function initCameraScanner() {
            if (!html5QrCode) return;
            
            Html5Qrcode.getCameras().then(cameras => {
                cameraSelect.innerHTML = '';
                
                if (cameras.length === 0) {
                    const opt = document.createElement("option");
                    opt.text = "Kamera tidak ditemukan";
                    cameraSelect.appendChild(opt);
                    setStatus("Perangkat kamera tidak ditemukan.", "error");
                    return;
                }

                cameras.forEach(cam => {
                    const opt = document.createElement("option");
                    opt.value = cam.id;
                    opt.text = cam.label || `Kamera ${cameraSelect.length + 1}`;
                    cameraSelect.appendChild(opt);
                });

                setStatus("Menginisialisasi kamera...", "loading");
                startScannerEnv();

            }).catch(err => {
                setStatus("Gagal memuat kamera: " + err, "error");
            });
        }

        function startScannerId() {
            if (!html5QrCode) return;
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
                setStatus("Arahkan kamera ke QR Code Kartu Santri", "info");
            }).catch(err => {
                setStatus("Kamera gagal diaktifkan: " + err, "error");
            });
        }

        function startScannerEnv() {
            if (!html5QrCode) return;
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
                setStatus("Kamera aktif. Arahkan ke QR Code Kartu Santri", "info");
                if (html5QrCode.getActiveCamera()) {
                    cameraSelect.value = html5QrCode.getActiveCamera().id;
                }
            }).catch(err => {
                if (cameraSelect.options.length > 0 && cameraSelect.options[0].value) {
                    cameraSelect.selectedIndex = 0;
                    startScannerId();
                } else {
                    setStatus("Kamera tidak merespon izin akses.", "error");
                }
            });
        }

        function stopCameraScanner() {
            if (html5QrCode && (html5QrCode.getState() === Html5QrcodeScannerState.SCANNING || html5QrCode.getState() === Html5QrcodeScannerState.PAUSED)) {
                html5QrCode.stop().catch(err => console.warn(err));
            }
        }

        function onScanFailure(error) {
            // Silently ignore scanner lookup frame fails
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            playBeepSound('success');
            setStatus("QR Code terdeteksi!", "success");
            
            // Stop camera scan immediately to avoid duplicate submits
            stopCameraScanner();

            sendScanPayload(decodedText);
        }

        // Handle Keyboard / USB scanner submit
        function handleUsbSubmit(event) {
            event.preventDefault();
            const tokenValue = usbInput.value.trim();
            
            if (tokenValue === '') return;
            if (isProcessing) return;
            isProcessing = true;

            setStatus("Kartu dibaca...", "success");
            sendScanPayload(tokenValue);
        }

        // Send scan to server via AJAX
        function sendScanPayload(tokenValue) {
            // Show Kiosk-style SweetAlert2 loading indicator
            Swal.fire({
                title: 'Memproses Absensi',
                html: 'Mohon tunggu sebentar, data kehadiran santri sedang diproses...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setStatus("Mengirim data absensi...", "loading");

            fetch("<?= base_url('qrcode/sendScanPembiasaanSiswa') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    token: tokenValue,
                    mode: currentMode,
                    ket: 'hadir'
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        throw new Error("HTTP " + res.status + ": " + text.substring(0, 100));
                    });
                }
                return res.text();
            })
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("Respon Server Tidak Valid (Bukan JSON): " + text.substring(0, 100));
                }
            })
            .then(res => {
                if (res.valid) {
                    playBeepSound('success');
                    
                    const badgeBg = res.type === 'MASUK' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800';
                    const messageColor = res.type === 'MASUK' ? '#10b981' : '#3b82f6';
                    
                    Swal.fire({
                        title: '<div class="flex flex-col items-center justify-center gap-2">' +
                               '<span class="text-sm font-extrabold uppercase tracking-widest px-4 py-1 rounded-full ' + badgeBg + '">ABSENSI ' + res.type + '</span>' +
                               '<span class="text-3xl font-black mt-2 text-slate-800 dark:text-white">' + res.siswa + '</span>' +
                               '</div>',
                        html: '<div style="font-size: 1.25rem; font-weight: 700; color: ' + messageColor + '; margin-top: 10px;">✅ ABSENSI BERHASIL!</div>' +
                              '<div style="font-size: 0.9rem; color: #64748b; margin-top: 5px;">NIS: ' + res.nis + ' | Pukul ' + res.waktu + ' WIB</div>',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                    });
 
                    setStatus(res.message, "success");
                    setTimeout(() => {
                        resetScannerAfterScan();
                    }, 1000);
                } else {
                    playBeepSound('error');
 
                    Swal.fire({
                        title: '<span style="font-size: 1.8rem; font-weight: 900; color: #ef4444;">ABSENSI DITOLAK</span>',
                        html: '<div style="font-size: 1.2rem; font-weight: 700; color: #475569; margin-top: 10px;" class="dark:text-slate-300">' + res.message + '</div>',
                        icon: 'error',
                        timer: 1200,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                    });
 
                    setStatus(res.message, "error");
                    setTimeout(() => {
                        resetScannerAfterScan();
                    }, 1200);
                }
            })
            .catch(err => {
                playBeepSound('error');
                console.error("Absensi error:", err);
 
                Swal.fire({
                    title: '<span style="font-size: 1.8rem; font-weight: 900; color: #f59e0b;">KESALAHAN SISTEM</span>',
                    html: '<div style="font-size: 1.1rem; font-weight: 600; color: #475569; margin-top: 10px;" class="dark:text-slate-300">' + err.message + '</div>',
                    icon: 'warning',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                });
 
                setStatus("Koneksi gagal: " + err.message, "error");
                setTimeout(() => {
                    resetScannerAfterScan();
                }, 1500);
            });
        }

        function resetScannerAfterScan() {
            isProcessing = false;
            setStatus("Menunggu scan kartu berikutnya...", "info");
            
            if (activeTab === 'camera') {
                startScannerEnv();
            } else {
                focusUsbInput();
            }
        }

        // Camera Source Switcher event
        cameraSelect.addEventListener("change", function() {
            if (!this.value) return;
            stopCameraScanner();
            startScannerId();
        });

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
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url("auth/logout") ?>';
                }
            });
        }

        // Sidebar Drawer toggle logic
        const sidebarDrawer = document.getElementById('sidebarDrawer');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeDrawer = document.getElementById('closeDrawer');

        function openMenu() {
            sidebarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sidebarOverlay.classList.add('opacity-100');
                sidebarDrawer.classList.remove('-translate-x-full');
            }, 50);
        }

        function closeMenu() {
            sidebarOverlay.classList.remove('opacity-100');
            sidebarDrawer.classList.add('-translate-x-full');
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        }

        sidebarOverlay.addEventListener('click', closeMenu);
        closeDrawer.addEventListener('click', closeMenu);

        // Auto start camera if tab starts as camera
        window.addEventListener("load", function() {
            initCameraScanner();
        });
    </script>
</body>
</html>
