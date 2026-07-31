<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <title>QR Absensi</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- QRCodeJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="w-screen h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">

    <!-- OVERLAY VERIFIKASI -->
    <div id="locationCheck"
        class="fixed inset-0 z-50 flex items-center justify-center
            bg-gray-900/90 text-white">

        <div class="text-center max-w-md px-6">
            <div id="locLoading" class="animate-pulse text-2xl mb-4">📍</div>
            <h2 id="locTitle" class="text-xl font-semibold mb-2">Memverifikasi Lokasi</h2>
            <p id="locDesc" class="text-sm opacity-80">
                Mohon izinkan akses lokasi untuk melanjutkan absensi
            </p>
            <p id="locError" class="mt-4 text-red-400 text-sm hidden"></p>
        </div>
    </div>

    <div id="fullscreenWrap" class="hidden w-full h-full flex items-center justify-center px-6">

        <div class="w-full max-w-2xl">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10 text-center relative">

                <!-- Fullscreen Button -->
                <button onclick="toggleFullscreen()"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800
                       dark:text-gray-400 dark:hover:text-white text-xl">
                    ⛶
                </button>

                <!-- Header -->
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">
                    QR Absensi
                </h1>
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">
                    Scan QR untuk melakukan absensi
                </p>

                <!-- QR -->
                <div class="flex justify-center mb-6">
                    <div id="qrcode"
                        class="p-6 bg-white rounded-2xl shadow-lg dark:bg-gray-700
                           w-[320px] h-[320px] flex items-center justify-center transition-opacity duration-300">
                    </div>
                </div>

                <!-- Status -->
                <div id="qrStatus"
                    class="inline-flex items-center px-6 py-3 rounded-full
                       text-base font-semibold
                       bg-green-100 text-green-700
                       dark:bg-green-900 dark:text-green-300">
                    QR Aktif
                </div>

                <!-- Token -->
                <p class="mt-6 text-sm text-gray-400 dark:text-gray-500" id="tokenText"></p>

            </div>
        </div>

    </div>

    <script>
        function verifyLocation() {
            if (!navigator.geolocation) {
                showError('Browser tidak mendukung GPS');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                pos => {
                    fetch('<?= base_url("qrcode/verifyLocation") ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                lat: pos.coords.latitude,
                                lon: pos.coords.longitude
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.allow === true) {
                                startApp();
                            } else {
                                showError(res.message ?? 'Lokasi tidak valid');
                            }
                        })
                        .catch(() => showError('Gagal verifikasi lokasi'));
                },
                err => {
                    showError('Izin lokasi ditolak / GPS error');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function showError(msg) {
            const el = document.getElementById('locError');
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        function startApp() {
            if (document.getElementById('locationCheck')) {
                document.getElementById('locationCheck').remove();
            }
            document.getElementById('fullscreenWrap').classList.remove('hidden');

            function enterFS() {
                let el = document.documentElement;
                if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                    if (el.requestFullscreen) {
                        el.requestFullscreen().catch(() => {});
                    } else if (el.webkitRequestFullscreen) {
                        /* Safari */
                        el.webkitRequestFullscreen();
                    } else if (el.msRequestFullscreen) {
                        /* IE11 */
                        el.msRequestFullscreen();
                    }
                }
            }

            enterFS();

            document.addEventListener('click', enterFS, {
                once: true
            });
            document.addEventListener('touchstart', enterFS, {
                once: true
            });
        }

        document.addEventListener('DOMContentLoaded', verifyLocation);
    </script>


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
                        document.getElementById('qrcode').innerHTML = '';
                        document.getElementById('qrcode').classList.remove('opacity-10');

                        qr = new QRCode(document.getElementById("qrcode"), {
                            text: data.token,
                            width: 280,
                            height: 280,
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
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ' +
                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
                el.innerText = 'QR Aktif';
            } else if (status === 'used') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ' +
                    'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
                el.innerText = 'QR Terpakai – Memperbarui...';
            } else if (status === 'error') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ' +
                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
                el.innerText = 'Menunggu Jaringan...';
                document.getElementById('qrcode').classList.add('opacity-10');
            }
        }

        // INIT
        loadQR();

        // Refresh status
        setInterval(checkStatus, 1000);
        setInterval(tampilQR, 2000);

        setTimeout(() => {
            document.body.classList.add('opacity-20');
            setTimeout(() => location.reload(), 1000);
        }, 60 * 60 * 1000);
    </script>
    <script>
        function toggleFullscreen() {
            const el = document.documentElement;

            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (el.requestFullscreen) {
                    el.requestFullscreen().catch(() => {});
                } else if (el.webkitRequestFullscreen) {
                    el.webkitRequestFullscreen();
                } else if (el.msRequestFullscreen) {
                    el.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }
    </script>

</body>

</html>