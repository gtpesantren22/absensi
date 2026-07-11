<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Portal Absensi</title>
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
    </script>
    <script>
        // Automatic dark mode detection based on localStorage
        const isDark = localStorage.getItem('theme') === 'dark' || 
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) {
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
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4 transition-colors duration-200">

    <div class="w-full max-w-md mx-auto relative">
        <!-- Background decorative ambient lights -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-blue-400/20 dark:bg-blue-600/10 rounded-full blur-3xl -z-10"></div>
        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-rose-400/20 dark:bg-rose-600/10 rounded-full blur-3xl -z-10"></div>

        <!-- Card Container -->
        <div class="glass-card shadow-2xl rounded-3xl p-8 sm:p-10 text-center space-y-6">
            
            <!-- Warning Visual -->
            <div class="relative flex items-center justify-center">
                <!-- Pulse Rings -->
                <span class="absolute inline-flex h-24 w-24 rounded-full bg-rose-500/10 animate-ping opacity-75"></span>
                <span class="absolute inline-flex h-20 w-20 rounded-full bg-rose-500/15"></span>
                
                <div class="relative w-16 h-16 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-3xl"></i>
                </div>
            </div>

            <!-- Error Code & Header -->
            <div class="space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-rose-500">Error 403</span>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Akses Ditolak
                </h1>
                <h2 class="text-sm font-bold text-slate-400 dark:text-slate-500">
                    Sistem Keamanan Portal
                </h2>
            </div>

            <!-- Description Message -->
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm mx-auto">
                Anda tidak memiliki izin atau tugas piket aktif untuk mengakses halaman kelola absensi guru piket hari ini.
            </p>

            <!-- Role Badge -->
            <?php if ($this->session->userdata('level')): ?>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-900 text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span>Role: <span class="capitalize text-slate-800 dark:text-slate-200"><?= $this->session->userdata('level') ?></span></span>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col gap-2.5">
                <a href="<?= base_url() ?>" 
                   class="w-full py-3.5 px-5 rounded-2xl bg-primary-600 hover:bg-primary-700 text-white font-extrabold text-sm shadow-lg hover:shadow-primary-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                <button onclick="history.back()" 
                        class="w-full py-3.5 px-5 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-extrabold text-sm border border-slate-200/50 dark:border-slate-700/50 active:scale-95 transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
            </div>

        </div>
    </div>

</body>
</html>
