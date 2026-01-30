<!-- Error 500 Page (Hidden by default) -->
<div id="error500" class="text-center hidden">
    <!-- Error Illustration -->
    <div class="mb-8 error-animation">
        <div class="relative inline-block">
            <div class="w-48 h-48 rounded-full bg-gradient-to-r from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30 flex items-center justify-center mx-auto">
                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-red-200 to-pink-200 dark:from-red-800/30 dark:to-pink-800/30 flex items-center justify-center">
                    <i class="fas fa-bug text-6xl text-red-500 dark:text-red-400"></i>
                </div>
            </div>
            <div class="absolute -top-2 -right-2 w-16 h-16 bg-red-600 dark:bg-red-700 rounded-full flex items-center justify-center pulse-animation">
                <i class="fas fa-exclamation text-2xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Error Code & Message -->
    <div class="mb-8">
        <h1 class="error-code text-8xl font-bold text-gray-800 dark:text-white mb-4">
            500
        </h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
            <i class="fas fa-server mr-2 text-red-500"></i>
            Kesalahan Server
        </h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
            Terjadi kesalahan internal pada server. Tim teknis telah diberitahu dan sedang memperbaiki masalah ini.
        </p>
    </div>

    <!-- Error Details -->
    <div class="mb-8 max-w-lg mx-auto">
        <div class="bg-gray-800 dark:bg-gray-900 rounded-xl p-4 text-left">
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-medium text-gray-300">Detail Kesalahan:</h4>
                <button id="copyError" class="text-sm text-primary-400 hover:text-primary-300">
                    <i class="fas fa-copy mr-1"></i> Salin
                </button>
            </div>
            <div class="bg-black rounded-lg p-3 overflow-x-auto">
                <code class="text-sm text-gray-300">
                    <span class="text-green-400">[2024-04-15 10:30:45]</span>
                    <span class="text-red-400">ERROR</span>: Database connection failed<br>
                    <span class="text-yellow-400">at</span> Database.connect (database.js:45)<br>
                    <span class="text-yellow-400">at</span> Server.start (server.js:89)<br>
                    <span class="text-blue-400">Reference</span>: ERR-500-DB-7890
                </code>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <button id="retryButton" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-redo mr-2"></i>
            Coba Lagi
        </button>
        <a href="dashboard.html" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium transition duration-200 flex items-center">
            <i class="fas fa-home mr-2"></i>
            Kembali ke Dashboard
        </a>
        <button id="reportError" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-flag mr-2"></i>
            Laporkan Masalah
        </button>
    </div>

    <!-- Additional Help -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 max-w-lg mx-auto">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-tools text-red-500 dark:text-red-400 text-lg mt-1"></i>
            </div>
            <div class="ml-3">
                <h4 class="font-medium text-red-800 dark:text-red-300">Sedang dalam perbaikan</h4>
                <p class="text-sm text-red-700 dark:text-red-400 mt-1">
                    Tim teknis sedang bekerja untuk memperbaiki masalah ini. Coba refresh halaman beberapa menit lagi atau hubungi dukungan teknis.
                </p>
            </div>
        </div>
    </div>
</div>