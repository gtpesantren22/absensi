<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Otorisasi Terminal</title>
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
    <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-xl p-6 border border-slate-200/60 dark:border-slate-700/60 space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto text-2xl shadow-sm">
                <i class="fas fa-link"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Otorisasi Terminal Baru</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Hubungkan perangkat baru ini ke database absensi sekolah.
            </p>
        </div>

        <!-- Form -->
        <form action="" method="post" class="space-y-4">
            <input type="hidden" name="pairing_id" value="<?= htmlspecialchars($pairing_id) ?>">
            
            <!-- Nama Terminal -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pl-0.5">Nama Terminal / Perangkat:</label>
                <input type="text" name="device_name" placeholder="Contoh: Webcam Pos Utama / Laptop Piket" 
                       class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
            </div>

            <!-- Pilih Lembaga -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pl-0.5">Penugasan Lembaga:</label>
                <select name="id_lembaga" 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500 font-medium">
                    <option value="">Semua Lembaga (Global)</option>
                    <?php foreach ($lembagas as $l): ?>
                        <option value="<?= $l->id_lembaga ?>"><?= htmlspecialchars($l->nama) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="block text-[10px] text-slate-400 dark:text-slate-500 pl-0.5 leading-relaxed">
                    * Kosongkan jika terminal ini digunakan secara global untuk semua lembaga.
                </span>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="submit" value="1"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Setujui &amp; Otorisasi Perangkat
            </button>
        </form>
    </div>
</body>
</html>
