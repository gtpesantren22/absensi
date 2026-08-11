<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Multimode - <?= htmlspecialchars($app_name) ?></title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">

    <!-- QRCodeJS (Generate System QR & Pairing QR) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- HTML5 QRCode (Camera Scanner for Physical Cards) -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <!-- SweetAlert2 for Terminal Popups -->
    <link rel="stylesheet" href="<?= base_url('assets/sw/sweetalert2.min.css') ?>">
    <script src="<?= base_url('assets/sw/sweetalert2.all.min.js') ?>"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    }
                }
            }
        }

        // Sync theme from localStorage
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
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .dark .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="w-screen h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 overflow-x-hidden overflow-y-auto transition-colors duration-200">

    <!-- LOADING SCREEN -->
    <div id="loadingScreen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-50 dark:bg-slate-950">
        <div class="text-center space-y-4">
            <div class="animate-spin text-4xl text-primary-600 dark:text-primary-400">
                <i class="fas fa-circle-notch"></i>
            </div>
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Menghubungkan ke server...</p>
        </div>
    </div>

    <!-- STATE 1: UNREGISTERED DEVICE (PAIRING QR ala WA WEB) -->
    <div id="pairingContainer" class="hidden min-h-screen flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-4xl glass-card rounded-3xl shadow-2xl p-6 md:p-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <!-- Left Info Panel -->
            <div class="w-full md:w-1/2 space-y-6">
                <div class="flex items-center gap-3">
                    <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                        <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="w-12 h-12 rounded-xl object-contain shadow">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-xl bg-primary-600 flex items-center justify-center text-white text-xl font-bold shadow">
                            <i class="fas fa-school"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-slate-150"><?= htmlspecialchars($app_name) ?></h1>
                        <p class="text-xs text-slate-500">Terminal Otorisasi Baru</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-tight">Hubungkan Perangkat Terminal Absensi</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-350 leading-relaxed">
                        Untuk menggunakan komputer/tablet ini sebagai Terminal Absensi Guru, silakan ikuti petunjuk berikut:
                    </p>
                    <ol class="list-decimal list-inside text-xs text-slate-600 dark:text-slate-350 space-y-2.5 pl-1.5 font-medium">
                        <li>Buka aplikasi Absensi di HP Anda (harus login sebagai <strong class="text-red-500 dark:text-red-400">Super Admin</strong>).</li>
                        <li>Gunakan menu scan QR HP Anda untuk men-scan kode QR di sebelah kanan.</li>
                        <li>Masukkan nama terminal di HP Anda (misal: "Webcam Kantor MA") dan setujui otorisasi.</li>
                        <li>Layar komputer sekolah ini akan otomatis terbuka setelah disetujui.</li>
                    </ol>
                </div>
            </div>

            <!-- Right QR Code Box -->
            <div class="w-full md:w-1/2 flex flex-col items-center justify-center">
                <div class="p-5 bg-white rounded-2xl shadow-lg border border-slate-100 dark:border-slate-800 flex items-center justify-center">
                    <div id="pairingQr" class="w-[240px] h-[240px] flex items-center justify-center">
                        <!-- QR Code Pairing generated by JS -->
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 rounded-full border border-amber-200 dark:border-amber-900/30">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        <span>Menunggu Otorisasi Super Admin...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATE 2: REGISTERED DEVICE (DUAL-MODE ATTENDANCE TERMINAL) -->
    <div id="terminalContainer" class="hidden w-full min-h-screen flex flex-col p-4 md:p-8">
        <div class="w-full max-w-6xl mx-auto flex-1 flex flex-col justify-between">

            <!-- Terminal Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-200 dark:border-slate-800/80">
                <div class="flex items-center gap-3">
                    <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                        <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="w-12 h-12 rounded-xl object-contain">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-xl bg-primary-600 flex items-center justify-center text-white text-xl">
                            <i class="fas fa-school"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-wide">
                            Absensi Kehadiran Guru, Karyawan, dan Siswa
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Terminal Aktif: <strong id="lblDeviceName" class="text-slate-800 dark:text-slate-200">...</strong>
                            (<span id="lblLembagaNama" class="text-slate-500 font-semibold">...</span>)
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="disconnectTerminal()"
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-xl border border-red-200/50 dark:border-red-900/40 shadow-sm transition">
                        <i class="fas fa-link-slash mr-1.5"></i>Putuskan
                    </button>
                    <button onclick="toggleFullscreen()"
                        class="p-2 bg-white dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 transition">
                        ⛶
                    </button>
                </div>
            </div>

            <!-- Terminal Cards Grid -->
            <div class="grid grid-cols-1 landscape:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6 items-stretch flex-1">

                <!-- CARD 1: SYSTEM QR CODE (FOR TEACHERS WITH PHONES) -->
                <div class="glass-card rounded-3xl shadow-lg p-5 md:p-6 lg:p-8 flex flex-col justify-between text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600"></div>

                    <div>
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Metode 1</span>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">QR Code System (Scan via HP)</h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                            Scan QR Code dinamis ini menggunakan HP Guru (Izin lokasi GPS terverifikasi otomatis).
                        </p>

                        <!-- System QR Canvas -->
                        <div class="flex justify-center mb-4">
                            <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center">
                                <div id="qrcode" class="flex items-center justify-center transition-opacity duration-300"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <!-- System QR Status Badge -->
                        <div id="qrStatus" class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                            QR System Aktif
                        </div>
                        <p class="mt-3 text-[10px] text-slate-400 dark:text-slate-500 font-mono" id="tokenText"></p>
                    </div>
                </div>

                <!-- CARD 2: SCANNER CAMERA (FOR PHYSICAL QR CARDS) -->
                <div class="glass-card rounded-3xl shadow-lg p-5 md:p-6 lg:p-8 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-600"></div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Metode 2</span>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Scan Kartu Fisik Guru/Siswa</h2>
                            </div>
                        </div>
                        <!-- <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                            Tunjukkan QR Code Kartu Guru fisik ke kamera terminal untuk melakukan absensi:
                        </p> -->

                        <!-- Controls -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Kamera Terminal:</label>
                                <select id="cardCameraSelect"
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
                                    <option value="">Mengakses kamera...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Mode Absen:</label>
                                <select id="cardScanMode"
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                                    <option value="auto">Otomatis</option>
                                    <option value="masuk">Masuk</option>
                                    <option value="pulang">Pulang</option>
                                </select>
                            </div>
                        </div>

                        <!-- Webcam Viewport -->
                        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black/5 dark:bg-black/40 min-h-[180px] md:min-h-[220px] lg:min-h-[250px] flex items-center justify-center relative shadow-inner">
                            <div id="cardReader" class="w-full"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <!-- Card Scan Status Badge -->
                        <div id="cardScanStatus"
                            class="w-full text-center px-4 py-2.5 rounded-xl text-xs md:text-sm font-semibold bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-400 transition-all duration-300 shadow-sm border border-slate-200/50 dark:border-slate-700/50">
                            Siap memindai kartu fisik...
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Copy -->
            <p class="text-[10px] text-center text-slate-400 mt-6">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($app_name) ?>. All rights reserved.
            </p>
        </div>
    </div>

    <!-- MAIN TERMINAL VERIFICATION LOGIC -->
    <script>
        const terminalTokenKey = "terminal_auth_token";
        let checkPairingInterval;

        document.addEventListener("DOMContentLoaded", function() {
            verifyTerminalStatus();
        });

        function verifyTerminalStatus() {
            // Get token from localStorage or Cookie
            let token = localStorage.getItem(terminalTokenKey) || getCookie(terminalTokenKey);

            if (token) {
                // Verify with backend
                fetch("<?= base_url('qrcode/checkTerminalToken') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            token: token
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        document.getElementById("loadingScreen").classList.add("hidden");
                        if (res.valid) {
                            // Authorized!
                            setCookie(terminalTokenKey, token, 3650); // 10 years cookie
                            localStorage.setItem(terminalTokenKey, token);

                            document.getElementById("lblDeviceName").innerText = res.device_name;
                            document.getElementById("lblLembagaNama").innerText = res.lembaga_nama;

                            startRegisteredTerminal();
                        } else {
                            // Invalid token
                            clearTerminalAuth();
                            startPairingProcess();
                        }
                    })
                    .catch(() => {
                        document.getElementById("loadingScreen").classList.add("hidden");
                        // Offline fallback: if we have token, allow offline view of terminal
                        startRegisteredTerminal();
                    });
            } else {
                document.getElementById("loadingScreen").classList.add("hidden");
                startPairingProcess();
            }
        }

        function clearTerminalAuth() {
            localStorage.removeItem(terminalTokenKey);
            eraseCookie(terminalTokenKey);
        }

        function startPairingProcess() {
            document.getElementById("pairingContainer").classList.remove("hidden");
            document.getElementById("terminalContainer").classList.add("hidden");

            // Request pairing session from server
            fetch("<?= base_url('qrcode/generatePairingSession') ?>")
                .then(res => res.json())
                .then(data => {
                    if (data.pairing_id) {
                        document.getElementById("pairingQr").innerHTML = "";
                        new QRCode(document.getElementById("pairingQr"), {
                            text: data.pairing_url,
                            width: 240,
                            height: 240,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                        // Start polling pairing status
                        if (checkPairingInterval) clearInterval(checkPairingInterval);
                        checkPairingInterval = setInterval(() => {
                            checkPairingStatus(data.pairing_id);
                        }, 2000);
                    }
                });
        }

        function checkPairingStatus(pairingId) {
            fetch("<?= base_url('qrcode/checkPairingStatus/') ?>" + pairingId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'paired') {
                        clearInterval(checkPairingInterval);

                        // Save token & reload
                        localStorage.setItem(terminalTokenKey, data.device_token);
                        setCookie(terminalTokenKey, data.device_token, 3650);

                        location.reload();
                    }
                });
        }

        function disconnectTerminal() {
            if (confirm("Apakah Anda yakin ingin memutus otorisasi terminal perangkat ini?")) {
                clearTerminalAuth();
                location.reload();
            }
        }

        // Cookie Helpers
        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function eraseCookie(name) {
            document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
    </script>

    <!-- SCRIPT CARD 1: SYSTEM QR CODE LOGIC -->
    <script>
        let qr;
        let isLoadingQR = false;

        function loadQR() {
            if (isLoadingQR) return;
            isLoadingQR = true;
            document.getElementById('qrcode').classList.add('opacity-10');
            setStatus('used');
            fetch("<?= base_url('qrcode/getToken/10') ?>")
                .then(res => res.json())
                .then(data => {
                    tampilQR()
                })
                .catch(err => {
                    console.error('Fetch QR error:', err);
                    setStatus('error');
                    isLoadingQR = false;
                });
        }

        function tampilQR() {
            fetch("<?= base_url('qrcode/getActiveToken') ?>")
                .then(res => res.json())
                .then(data => {
                    if (data.token) {
                        const qrContainer = document.getElementById('qrcode');
                        qrContainer.innerHTML = '';
                        qrContainer.classList.remove('opacity-10');

                        // Determine responsive QR size based on window width and orientation (Enlarged)
                        let isLandscape = window.innerWidth > window.innerHeight;
                        let qrWidth = window.innerWidth >= 1200 ? 320 : 
                                      (window.innerWidth >= 1024 ? 280 : 
                                      (isLandscape && window.innerWidth >= 768 ? 220 : 280));

                        // Apply dynamic sizes to container
                        qrContainer.style.width = qrWidth + "px";
                        qrContainer.style.height = qrWidth + "px";

                        qr = new QRCode(qrContainer, {
                            text: data.token,
                            width: qrWidth,
                            height: qrWidth,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                        setStatus('active');
                        isLoadingQR = false;
                    } else {
                        setStatus('error');
                        isLoadingQR = false;
                        loadQR();
                    }
                })
                .catch(err => {
                    console.error('Fetch active token error:', err);
                    setStatus('error');
                    isLoadingQR = false;
                });
        }

        function checkStatus() {
            fetch("<?= base_url('qrcode/checkStatus') ?>")
                .then(res => res.json())
                .then(data => {
                    if (!data.ready) {
                        loadQR();
                    } else if (!isLoadingQR) {
                        document.getElementById('qrcode').classList.remove('opacity-10');
                        setStatus('active');
                    }
                })
                .catch(err => {
                    console.error('Check status online error:', err);
                    setStatus('error');
                });
        }

        function setStatus(status) {
            const el = document.getElementById('qrStatus');

            if (status === 'active') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold ' +
                    'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 shadow-sm';
                el.innerText = 'QR System Aktif';
            } else if (status === 'used') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold ' +
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 shadow-sm';
                el.innerText = 'QR Terpakai – Memperbarui...';
            } else if (status === 'error') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold ' +
                    'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 shadow-sm';
                el.innerText = 'Menunggu Jaringan...';
                document.getElementById('qrcode').classList.add('opacity-10');
            }
        }
    </script>

    <!-- SCRIPT CARD 2: PHYSICAL CARD CAMERA SCANNER & AUDIO BEEP -->
    <script>
        if (typeof Html5QrcodeScannerState === 'undefined') {
            window.Html5QrcodeScannerState = {
                UNKNOWN: 1,
                NOT_STARTED: 2,
                SCANNING: 3,
                PAUSED: 4
            };
        }
        const html5CardQrCode = new Html5Qrcode("cardReader");
        const cardCamSelect = document.getElementById("cardCameraSelect");
        const cardStatusEl = document.getElementById("cardScanStatus");
        let isCardCooldown = false;

        // Web Audio API Synthesizer Beep
        function playBeepSound(type = 'success') {
            try {
                const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
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

        function setCardStatus(text, type = 'info') {
            const baseClass = "w-full text-center px-4 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition-all duration-300 shadow-sm border ";
            if (type === 'success') {
                cardStatusEl.className = baseClass + "bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-300 border-emerald-300 dark:border-emerald-900 animate-pulse";
            } else if (type === 'error') {
                cardStatusEl.className = baseClass + "bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-300 border-red-300 dark:border-red-900";
            } else {
                cardStatusEl.className = baseClass + "bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-400 border-slate-200 dark:border-slate-800/80";
            }
            cardStatusEl.innerText = text;
        }

        function startRegisteredTerminal() {
            document.getElementById("terminalContainer").classList.remove("hidden");
            document.getElementById("pairingContainer").classList.add("hidden");

            // Init Dynamic QR (Metode 1)
            loadQR();
            setInterval(checkStatus, 1000);
            setInterval(tampilQR, 2000);

            // Init Card Scanner (Metode 2)
            Html5Qrcode.getCameras().then(cameras => {
                cardCamSelect.innerHTML = "";
                if (cameras && cameras.length > 0) {
                    cameras.forEach((cam, index) => {
                        const opt = document.createElement("option");
                        opt.value = cam.id;
                        opt.text = cam.label || `Kamera ${index + 1}`;
                        cardCamSelect.appendChild(opt);
                    });
                    startCardScanner();
                } else {
                    const opt = document.createElement("option");
                    opt.value = "";
                    opt.text = "Kamera tidak terdeteksi";
                    cardCamSelect.appendChild(opt);
                    setCardStatus("Tidak ada kamera terhubung", "error");
                }
            }).catch(err => {
                console.error("Gagal membaca daftar kamera:", err);
                cardCamSelect.innerHTML = "<option value=''>Kamera gagal dimuat</option>";
                setCardStatus("Gagal mengakses izin kamera", "error");
            });

            cardCamSelect.addEventListener("change", function() {
                if (!this.value) return;
                if (
                    html5CardQrCode.getState() === Html5QrcodeScannerState.SCANNING ||
                    html5CardQrCode.getState() === Html5QrcodeScannerState.PAUSED
                ) {
                    html5CardQrCode.stop().then(startCardScanner);
                } else {
                    startCardScanner();
                }
            });
        }

        function startCardScanner() {
            if (!cardCamSelect.value) return;

            // Adjust qrbox size for smaller tablet landscape viewport
            let isLandscape = window.innerWidth > window.innerHeight;
            let boxSize = window.innerWidth >= 1200 ? 200 : 
                          (window.innerWidth >= 1024 ? 170 : 
                          (isLandscape && window.innerWidth >= 768 ? 130 : 180));

            html5CardQrCode.start(
                cardCamSelect.value, {
                    fps: 10,
                    qrbox: {
                        width: boxSize,
                        height: boxSize
                    }
                },
                onCardScanSuccess,
                () => {}
            ).catch(err => {
                console.error("Gagal memulai kamera scanner:", err);
                setCardStatus("Gagal membuka aliran video kamera", "error");
            });
        }

        function onCardScanSuccess(decodedText) {
            if (isCardCooldown) return;
            isCardCooldown = true;

            const mode = document.getElementById("cardScanMode").value || 'auto';

            fetch("<?= base_url('qrcode/sendScanCard') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        qr_code: decodedText,
                        jenis: mode,
                        terminal_token: localStorage.getItem(terminalTokenKey)
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

                        const headerColor = res.type === 'MASUK' ? '#10b981' : '#3b82f6';
                        const badgeBg = res.type === 'MASUK' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800';

                        Swal.fire({
                            title: '<div class="flex flex-col items-center justify-center gap-2">' +
                                '<span class="text-sm font-extrabold uppercase tracking-widest px-4 py-1 rounded-full ' + badgeBg + '">ABSENSI ' + res.type + '</span>' +
                                '<span class="text-3xl font-black mt-2 text-slate-800 dark:text-white">' + res.guru + '</span>' +
                                '</div>',
                            html: '<div style="font-size: 1.25rem; font-weight: 700; color: #10b981; margin-top: 10px;">✅ BERHASIL DICATAT!</div>' +
                                '<div style="font-size: 1rem; color: #64748b; margin-top: 5px; font-weight: 500;">Pukul ' + res.waktu + ' WIB</div>',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                        });

                        setCardStatus("✅ " + res.message, "success");
                        setTimeout(() => {
                            setCardStatus("Siap memindai kartu fisik guru atau siswa berikutnya...", "info");
                            isCardCooldown = false;
                        }, 2000);
                    } else {
                        playBeepSound('error');

                        Swal.fire({
                            title: '<span style="font-size: 1.8rem; font-weight: 900; color: #ef4444;">ABSENSI DITOLAK</span>',
                            html: '<div style="font-size: 1.2rem; font-weight: 700; color: #475569; margin-top: 10px;" class="dark:text-slate-300">' + res.message + '</div>',
                            icon: 'error',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                        });

                        setCardStatus("❌ " + res.message, "error");
                        setTimeout(() => {
                            setCardStatus("Siap memindai kartu fisik guru...", "info");
                            isCardCooldown = false;
                        }, 3000);
                    }
                })
                .catch(err => {
                    console.error("API sendScanCard error:", err);
                    playBeepSound('error');

                    Swal.fire({
                        title: '<span style="font-size: 1.8rem; font-weight: 900; color: #f59e0b;">KESALAHAN SISTEM</span>',
                        html: '<div style="font-size: 1.1rem; font-weight: 600; color: #475569; margin-top: 10px;" class="dark:text-slate-300">' + err.message + '</div>',
                        icon: 'warning',
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                    });

                    setCardStatus("⚠️ " + err.message, "error");
                    setTimeout(() => {
                        setCardStatus("Siap memindai kartu fisik guru...", "info");
                        isCardCooldown = false;
                    }, 4000);
                });
        }

        function toggleFullscreen() {
            const el = document.documentElement;
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (el.requestFullscreen) el.requestFullscreen().catch(() => {});
                else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            }
        }
    </script>

</body>

</html>