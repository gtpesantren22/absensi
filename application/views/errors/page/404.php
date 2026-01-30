<div id="error404" class="text-center">
    <!-- Error Illustration -->
    <div class="mb-8 error-animation">
        <div class="relative inline-block">
            <div class="w-48 h-48 rounded-full bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 flex items-center justify-center mx-auto">
                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-red-200 to-orange-200 dark:from-red-800/30 dark:to-orange-800/30 flex items-center justify-center">
                    <i class="fas fa-map-signs text-6xl text-red-500 dark:text-red-400"></i>
                </div>
            </div>
            <div class="absolute -top-2 -right-2 w-16 h-16 bg-yellow-400 dark:bg-yellow-500 rounded-full flex items-center justify-center">
                <i class="fas fa-question text-2xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Error Code & Message -->
    <div class="mb-8">
        <h1 class="error-code text-8xl font-bold text-gray-800 dark:text-white mb-4">
            404
        </h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
            <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
            Halaman Tidak Ditemukan
        </h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
            Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman telah dihapus, dipindahkan, atau tidak pernah ada.
        </p>
    </div>

    <!-- Search Bar -->
    <div class="mb-8 max-w-md mx-auto">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input
                type="text"
                id="searchError"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                placeholder="Cari halaman lainnya...">
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <a href="dashboard.html" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-home mr-2"></i>
            Kembali ke Dashboard
        </a>
        <a href="javascript:history.back()" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium transition duration-200 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <!-- Additional Help -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 max-w-lg mx-auto">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-blue-500 dark:text-blue-400 text-lg mt-1"></i>
            </div>
            <div class="ml-3">
                <h4 class="font-medium text-blue-800 dark:text-blue-300">Butuh bantuan?</h4>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                    Hubungi administrator sistem di <span class="font-medium">admin@sekolahcerdas.sch.id</span> atau telepon <span class="font-medium">(021) 1234-5678</span>
                </p>
            </div>
        </div>
    </div>
</div>