<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otorisasi Terminal Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
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
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-xl p-6 border border-slate-200/60 dark:border-slate-700/60 text-center space-y-6">
        
        <!-- Header Success -->
        <div class="space-y-2">
            <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-2xl shadow-sm animate-bounce">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Otorisasi Berhasil!</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Perangkat terminal baru telah berhasil didaftarkan.
            </p>
        </div>

        <!-- Details -->
        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-2xl border border-slate-150 dark:border-slate-800 text-left space-y-2.5">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Nama Terminal:</span>
                <span class="text-slate-700 dark:text-slate-200 font-bold"><?= htmlspecialchars($device_name) ?></span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Lembaga Penugasan:</span>
                <span class="text-slate-700 dark:text-slate-200 font-bold"><?= htmlspecialchars($lembaga_nama) ?></span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Waktu Registrasi:</span>
                <span class="text-slate-700 dark:text-slate-200 font-bold"><?= date('d-m-Y H:i') ?> WIB</span>
            </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed px-4">
            Layar pada komputer terminal sekolah akan otomatis me-reload sekarang dan mengaktifkan panel absensi guru. Anda dapat menutup halaman ini sekarang.
        </p>

        <!-- Close button / Home link -->
        <a href="<?= base_url() ?>" 
           class="inline-flex w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-xl transition justify-center">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
