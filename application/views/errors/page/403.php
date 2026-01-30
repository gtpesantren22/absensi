<!-- Error 403 Page (Hidden by default) -->
<?php $this->load->view('errors/page/head'); ?>
<div id="" class="text-center ">
    <!-- Error Illustration -->
    <div class="mb-8 error-animation">
        <div class="relative inline-block">
            <div class="w-48 h-48 rounded-full bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 flex items-center justify-center mx-auto">
                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-amber-200 to-yellow-200 dark:from-amber-800/30 dark:to-yellow-800/30 flex items-center justify-center">
                    <i class="fas fa-ban text-6xl text-amber-500 dark:text-amber-400"></i>
                </div>
            </div>
            <div class="absolute -top-2 -right-2 w-16 h-16 bg-red-500 dark:bg-red-600 rounded-full flex items-center justify-center">
                <i class="fas fa-lock text-2xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Error Code & Message -->
    <div class="mb-8">
        <h1 class="error-code text-8xl font-bold text-gray-800 dark:text-white mb-4">
            403
        </h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
            <i class="fas fa-user-lock mr-2 text-amber-500"></i>
            Akses Ditolak
        </h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
            Anda tidak memiliki izin untuk mengakses halaman ini. Halaman ini memerlukan hak akses khusus yang tidak dimiliki oleh akun Anda.
        </p>
    </div>

    <!-- User Info -->
    <div class="mb-8 max-w-md mx-auto">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
            <div class="flex items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-4">
                    <span class="font-semibold text-primary-800 dark:text-primary-200">GU</span>
                </div>
                <div class="text-left">
                    <p class="font-medium">Akses Terbatas untuk Guru</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Anda login sebagai: <span class="font-medium">Guru Matematika</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <a href="dashboard.html" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Kembali ke Dashboard
        </a>
        <button id="requestAccess" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-hand-paper mr-2"></i>
            Minta Akses
        </button>
    </div>

    <!-- Additional Help -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 max-w-lg mx-auto">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-amber-500 dark:text-amber-400 text-lg mt-1"></i>
            </div>
            <div class="ml-3">
                <h4 class="font-medium text-amber-800 dark:text-amber-300">Perlu akses khusus?</h4>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                    Hubungi administrator sistem untuk meminta hak akses ke halaman ini. Pastikan Anda memiliki alasan yang valid.
                </p>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('errors/page/foot'); ?>