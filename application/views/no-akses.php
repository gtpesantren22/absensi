<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tidak Memiliki Akses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center 
             bg-gray-100 text-gray-800
             dark:bg-gray-900 dark:text-gray-100
             transition-colors duration-300">

    <div class="w-full max-w-lg mx-auto px-6 py-10">

        <div class="bg-white dark:bg-gray-800 
                    shadow-xl rounded-2xl 
                    p-8 sm:p-10 text-center">

            <!-- ICON -->
            <div class="mx-auto mb-6 w-20 h-20 sm:w-24 sm:h-24
                        flex items-center justify-center
                        rounded-full
                        bg-red-100 dark:bg-red-900">

                <svg class="w-10 h-10 sm:w-12 sm:h-12
                            text-red-600 dark:text-red-300"
                    fill="none" stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                </svg>
            </div>

            <!-- CODE -->
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight">
                403
            </h1>

            <!-- TITLE -->
            <h2 class="mt-3 text-lg sm:text-xl font-semibold">
                Akses Ditolak
            </h2>

            <!-- DESCRIPTION -->
            <p class="mt-4 text-sm sm:text-base
                      text-gray-500 dark:text-gray-400 leading-relaxed">
                Anda tidak memiliki izin untuk mengakses halaman ini.
                Jika ini adalah kesalahan, silakan hubungi administrator.
            </p>

            <!-- ROLE (Optional) -->
            <?php if ($this->session->userdata('level')): ?>
                <p class="mt-2 text-xs text-gray-400">
                    Role Anda: <?= $this->session->userdata('level') ?>
                </p>
            <?php endif; ?>

            <!-- BUTTONS -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">

                <a href="<?= base_url() ?>"
                    class="w-full sm:w-auto px-5 py-2.5
                          text-sm font-medium rounded-lg
                          bg-blue-600 text-white
                          hover:bg-blue-700
                          focus:outline-none focus:ring-2 focus:ring-blue-400
                          transition">
                    Kembali ke Dashboard
                </a>

                <button onclick="history.back()"
                    class="w-full sm:w-auto px-5 py-2.5
                               text-sm font-medium rounded-lg
                               bg-gray-200 text-gray-700
                               hover:bg-gray-300
                               dark:bg-gray-700 dark:text-gray-200
                               dark:hover:bg-gray-600
                               focus:outline-none focus:ring-2 focus:ring-gray-400
                               transition">
                    Kembali
                </button>

            </div>

        </div>

    </div>

</body>

</html>


