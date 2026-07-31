<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Otorisasi Ditolak</title>
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
        
        <!-- Header Error -->
        <div class="space-y-2">
            <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto text-2xl shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Akses Otorisasi Ditolak</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Hak akses akun Anda tidak memiliki wewenang untuk tindakan ini.
            </p>
        </div>

        <!-- Warning Message -->
        <div class="bg-red-50 dark:bg-red-950/20 p-4 rounded-2xl border border-red-200/50 dark:border-red-900/40 text-left space-y-3">
            <p class="text-xs text-red-800 dark:text-red-300 leading-relaxed font-medium">
                Pendaftaran dan otorisasi perangkat **Terminal Absensi Sekolah** baru hanya dapat disetujui oleh akun berlevel <strong class="underline">Super Admin</strong>.
            </p>
            <hr class="border-red-200/40 dark:border-red-900/20">
            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                <div>Anda saat ini login sebagai:</div>
                <div class="font-bold text-slate-850 dark:text-slate-200">
                    <i class="fas fa-user mr-1.5 opacity-60"></i><?= htmlspecialchars($current_user) ?> 
                    <span class="ml-1.5 px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-extrabold uppercase text-slate-700 dark:text-slate-350">
                        <?= htmlspecialchars($current_level) ?>
                    </span>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed px-4">
            Silakan keluar (logout) terlebih dahulu, kemudian masuk menggunakan akun **Super Admin** yang sah untuk melakukan otorisasi terminal.
        </p>

        <!-- Action Links -->
        <div class="flex flex-col gap-2.5">
            <!-- Logout & Login Super Admin -->
            <a href="<?= base_url('auth/logout') ?>" 
               class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-md shadow-red-500/10 hover:shadow-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Keluar &amp; Login Super Admin
            </a>
            
            <!-- Back to Home -->
            <a href="<?= base_url() ?>" 
               class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
