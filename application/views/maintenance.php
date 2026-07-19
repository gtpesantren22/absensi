<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Pemeliharaan - <?= htmlspecialchars($app_name) ?></title>
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
        
        // Sync dark mode theme from localStorage to prevent style flashing
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
        @keyframes gear-rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        .animate-gear {
            animation: gear-rotate 8s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-100 via-slate-50 to-blue-50 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-hidden transition-colors duration-200">
    
    <!-- Background blur spots -->
    <div class="absolute w-72 h-72 sm:w-96 sm:h-96 bg-primary-500/10 dark:bg-primary-600/20 rounded-full blur-3xl -top-20 -left-20"></div>
    <div class="absolute w-72 h-72 sm:w-96 sm:h-96 bg-indigo-400/10 dark:bg-indigo-500/10 rounded-full blur-3xl -bottom-20 -right-20"></div>

    <!-- Main Container -->
    <div class="w-full max-w-lg z-10 text-center space-y-6 sm:space-y-8">
        
        <!-- App Logo & Name -->
        <div class="flex flex-col items-center space-y-3">
            <?php if (!empty($app_logo) && file_exists('./uploads/logo/' . $app_logo)): ?>
                <img src="<?= base_url('uploads/logo/' . $app_logo) ?>" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-contain shadow-md">
            <?php else: ?>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white dark:bg-white/10 flex items-center justify-center backdrop-blur-md border border-slate-200 dark:border-white/20 shadow-md">
                    <i class="fas fa-school text-2xl sm:text-3xl text-indigo-500 dark:text-indigo-400"></i>
                </div>
            <?php endif; ?>
            <h1 class="text-lg sm:text-xl font-bold tracking-wide text-slate-800 dark:text-slate-200"><?= htmlspecialchars($app_name) ?></h1>
        </div>

        <!-- Glassmorphism Card (Responsive & Theme Adaptive) -->
        <div class="bg-white/75 dark:bg-slate-900/65 backdrop-blur-xl border border-slate-200/60 dark:border-white/10 rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl dark:shadow-2xl text-center space-y-6 relative overflow-hidden">
            
            <!-- Animated Icon Group (Responsive Sizes) -->
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto flex items-center justify-center">
                <!-- Large Rotating Gear -->
                <div class="absolute inset-0 flex items-center justify-center animate-gear text-slate-300/50 dark:text-slate-700/30">
                    <i class="fa-solid fa-cog text-7xl sm:text-8xl"></i>
                </div>
                <!-- Medium Counter-Rotating Gear -->
                <div class="absolute top-1 right-1 sm:top-2 sm:right-2 animate-gear text-indigo-500/70 dark:text-indigo-400/80" style="animation-direction: reverse; animation-duration: 5s;">
                    <i class="fa-solid fa-cog text-3xl sm:text-4xl"></i>
                </div>
                <!-- Center Tool Icon -->
                <div class="relative z-10 bg-gradient-to-br from-indigo-500 to-primary-600 w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl shadow-md flex items-center justify-center text-white scale-110">
                    <i class="fa-solid fa-tools text-xl sm:text-2xl animate-bounce"></i>
                </div>
            </div>

            <!-- Maintenance Messages -->
            <div class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-850 dark:text-white tracking-wide">Afwan, Masih e pateppak 🙏🙏</h2>
                <div class="h-1 w-16 bg-gradient-to-r from-indigo-500 to-primary-500 mx-auto rounded-full"></div>
                <p class="text-slate-600 dark:text-slate-200 text-sm leading-relaxed pt-2">
                    Mohon maaf atas ketidaknyamanannya. Aplikasi saat ini sedang menjalani pemeliharaan sistem berkala untuk meningkatkan performa dan fitur layanan.
                </p>
                <p class="text-slate-500 dark:text-slate-400 text-xs">
                    Sistem akan segera kembali online setelah pemeliharaan selesai. Terima kasih atas kesabaran Anda.
                </p>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-200 dark:border-white/10 pt-4 flex flex-col items-center justify-center space-y-3">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Hanya akun Super Admin yang dapat masuk saat ini:</span>
                <a href="<?= base_url('auth') ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/20 text-slate-850 dark:text-white font-bold text-xs tracking-wider uppercase transition border border-slate-300/50 dark:border-white/10 shadow-sm flex items-center gap-2">
                    <i class="fas fa-sign-in-alt text-xs"></i>
                    <span>Login Super Admin</span>
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-500">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($app_name) ?>. All rights reserved.
        </p>
    </div>
</body>
</html>
