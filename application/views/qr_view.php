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

<body class="h-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center">

            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">
                QR Absensi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Scan QR untuk melakukan absensi
            </p>

            <!-- QR Container -->
            <div class="flex justify-center mb-4">
                <div id="qrcode"
                    class="p-4 bg-white rounded-xl shadow dark:bg-gray-700">
                </div>
            </div>

            <!-- Status -->
            <div id="qrStatus"
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                    bg-green-100 text-green-700
                    dark:bg-green-900 dark:text-green-300">
                QR Aktif
            </div>

            <!-- Token (optional debugging) -->
            <p class="mt-4 text-xs text-gray-400 dark:text-gray-500" id="tokenText"></p>

        </div>
    </div>

    <script>
        let qr;

        function loadQR() {
            fetch("<?= base_url('qrcode/getToken/10') ?>")
                .then(res => res.json())
                .then(data => {

                    document.getElementById('qrcode').innerHTML = '';

                    qr = new QRCode(document.getElementById("qrcode"), {
                        text: data.token,
                        width: 220,
                        height: 220,
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

</body>

</html>