<?php $this->load->view('admin/head'); ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    tailwind.config = {
        darkMode: 'class'
    }
</script>
<main class="flex-1 p-4 md:p-6 overflow-y-auto">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-5">

            <!-- Header -->
            <h1 class="text-xl font-bold text-gray-800 dark:text-white text-center">
                Scan QR Absensi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                Arahkan kamera ke QR Code
            </p>

            <!-- Camera Select -->
            <select id="cameraSelect"
                class="w-full mb-3 rounded-lg border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-700
                   text-gray-700 dark:text-gray-200
                   text-sm p-2">
                <option value="">Pilih Kamera</option>
            </select>

            <!-- QR Scanner -->
            <div class="rounded-xl overflow-hidden border
                    border-gray-200 dark:border-gray-700 mb-4">
                <div id="reader" class="w-full"></div>
            </div>

            <!-- Status -->
            <div id="scanStatus"
                class="text-center text-sm font-medium px-4 py-2 rounded-full
                   bg-gray-100 text-gray-600
                   dark:bg-gray-700 dark:text-gray-300">
                Menunggu scan...
            </div>

        </div>
    </div>
</main>
<?php $this->load->view('admin/foot'); ?>
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
        const base = "text-center text-sm font-medium px-4 py-2 rounded-full ";
        if (type === 'success')
            statusEl.className = base + "bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300";
        else if (type === 'error')
            statusEl.className = base + "bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300";
        else
            statusEl.className = base + "bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300";

        statusEl.innerText = text;
    }

    // Load camera list
    Html5Qrcode.getCameras().then(cameras => {
        cameras.forEach(cam => {
            const opt = document.createElement("option");
            opt.value = cam.id;
            opt.text = cam.label || `Camera ${cameraSelect.length + 1}`;
            cameraSelect.appendChild(opt);
        });
    });

    // On camera change
    cameraSelect.addEventListener("change", function() {
        if (!this.value) return;

        // ✅ STOP hanya jika sedang jalan
        if (
            html5QrCode.getState() === Html5QrcodeScannerState.SCANNING ||
            html5QrCode.getState() === Html5QrcodeScannerState.PAUSED
        ) {
            html5QrCode.stop().then(startScanner);
        } else {
            startScanner();
        }
    });

    function startScanner() {
        html5QrCode.start(
            cameraSelect.value, {
                fps: 10,
                qrbox: {
                    width: 220,
                    height: 220
                }
            },
            onScanSuccess,
            () => {}
        );
    }

    function onScanSuccess(decodedText) {
        setStatus("QR berhasil discan", "success");

        html5QrCode.stop(); // stop setelah sukses

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

        sendScanPayload(decodedText, null, null, null);
    }

    function sendScanPayload(decodedText, lat, lon, accuracy) {

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
                        startScanner();
                    });
                }
            })
            .catch(err => {
                setStatus("Kesalahan koneksi jaringan", "error");
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal menghubungi server. Silakan coba lagi.'
                }).then(() => {
                    startScanner();
                });
            });
    }
</script>