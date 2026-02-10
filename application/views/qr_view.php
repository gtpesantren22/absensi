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
            <div class="animate-pulse text-2xl mb-4">📍</div>
            <h2 class="text-xl font-semibold mb-2">Memverifikasi Lokasi</h2>
            <p class="text-sm opacity-80">
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
                           w-[320px] h-[320px] flex items-center justify-center">
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
                                document.getElementById('locationCheck').remove();
                                document.getElementById('fullscreenWrap').classList.remove('hidden');
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

        document.addEventListener('DOMContentLoaded', verifyLocation);
    </script>


    <script>
        // AUTO FULLSCREEN SAAT PAGE LOAD (OPSIONAL)
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            }, 500);
        });

        let qr;

        function loadQR() {
            fetch("<?= base_url('qrcode/getToken/10') ?>")
                .then(res => res.json())
                .then(data => {

                    document.getElementById('qrcode').innerHTML = '';

                    qr = new QRCode(document.getElementById("qrcode"), {
                        text: data.token,
                        width: 280,
                        height: 280,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });

                    // document.getElementById('tokenText').innerText = data.token;
                    setStatus('active');
                });
        }

        function checkStatus() {
            fetch("<?= base_url('qrcode/checkStatus') ?>")
                .then(res => res.json())
                .then(data => {
                    if (data.used) {
                        setStatus('used');
                        loadQR();
                    }
                });
        }

        function setStatus(status) {
            const el = document.getElementById('qrStatus');

            if (status === 'active') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ' +
                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
                el.innerText = 'QR Aktif';
            }

            if (status === 'used') {
                el.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ' +
                    'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
                el.innerText = 'QR Terpakai – Memperbarui...';
            }
        }

        // INIT
        loadQR();

        // Refresh status tiap detik
        setInterval(checkStatus, 1000);


        setTimeout(() => {
            document.body.classList.add('opacity-20');
            setTimeout(() => location.reload(), 1000);
        }, 60 * 60 * 1000);
        // }, 10000);
    </script>
    <script>
        function toggleFullscreen() {
            const el = document.documentElement;

            if (!document.fullscreenElement) {
                el.requestFullscreen().catch(err => {
                    alert('Fullscreen gagal: ' + err.message);
                });
            } else {
                document.exitFullscreen();
            }
        }
    </script>

</body>

</html>