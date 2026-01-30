<script>
    // Toggle between different error pages (for demo only)
    const errorButtons = document.querySelectorAll('[data-error]');
    const errorPages = {
        '404': document.getElementById('error404'),
        '403': document.getElementById('error403'),
        '500': document.getElementById('error500'),
        'connection': document.getElementById('errorConnection')
    };

    // Function to show specific error page
    function showErrorPage(errorType) {
        // Hide all error pages
        Object.values(errorPages).forEach(page => {
            if (page) page.classList.add('hidden');
        });

        // Show selected error page
        if (errorPages[errorType]) {
            errorPages[errorType].classList.remove('hidden');

            // Update document title
            let errorTitle = '';
            switch (errorType) {
                case '404':
                    errorTitle = '404 - Halaman Tidak Ditemukan';
                    break;
                case '403':
                    errorTitle = '403 - Akses Ditolak';
                    break;
                case '500':
                    errorTitle = '500 - Kesalahan Server';
                    break;
                case 'connection':
                    errorTitle = 'Koneksi Terputus';
                    break;
            }
            document.title = errorTitle + ' - Aplikasi Absensi Sekolah';
        }
    }

    // Add event listeners to error buttons
    errorButtons.forEach(button => {
        button.addEventListener('click', function() {
            const errorType = this.getAttribute('data-error');
            showErrorPage(errorType);
        });
    });

    // Copy error details for 500 page
    const copyErrorBtn = document.getElementById('copyError');
    if (copyErrorBtn) {
        copyErrorBtn.addEventListener('click', function() {
            const errorDetails = document.querySelector('code').textContent;
            navigator.clipboard.writeText(errorDetails)
                .then(() => {
                    // Show success message
                    const originalText = copyErrorBtn.innerHTML;
                    copyErrorBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Tersalin!';
                    copyErrorBtn.classList.add('text-green-500');

                    setTimeout(() => {
                        copyErrorBtn.innerHTML = originalText;
                        copyErrorBtn.classList.remove('text-green-500');
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                });
        });
    }

    // Retry button for 500 page
    const retryButton = document.getElementById('retryButton');
    if (retryButton) {
        retryButton.addEventListener('click', function() {
            // Show loading state
            const originalText = retryButton.innerHTML;
            retryButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memuat ulang...';
            retryButton.disabled = true;

            // Simulate retry process
            setTimeout(() => {
                // Show success message
                showAlert('success', 'Berhasil terhubung kembali! Mengarahkan ke dashboard...');

                // Redirect after delay
                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 1500);
            }, 2000);
        });
    }

    // Report error button for 500 page
    const reportErrorBtn = document.getElementById('reportError');
    if (reportErrorBtn) {
        reportErrorBtn.addEventListener('click', function() {
            showAlert('info', 'Laporan masalah telah dikirim ke tim teknis. Terima kasih!');

            // Show loading state briefly
            const originalText = reportErrorBtn.innerHTML;
            reportErrorBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Melaporkan...';
            reportErrorBtn.disabled = true;

            setTimeout(() => {
                reportErrorBtn.innerHTML = originalText;
                reportErrorBtn.disabled = false;
            }, 1500);
        });
    }

    // Request access button for 403 page
    const requestAccessBtn = document.getElementById('requestAccess');
    if (requestAccessBtn) {
        requestAccessBtn.addEventListener('click', function() {
            showAlert('info', 'Permintaan akses telah dikirim ke administrator. Anda akan diberitahu via email.');

            // Show loading state briefly
            const originalText = requestAccessBtn.innerHTML;
            requestAccessBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
            requestAccessBtn.disabled = true;

            setTimeout(() => {
                requestAccessBtn.innerHTML = originalText;
                requestAccessBtn.disabled = false;
            }, 1500);
        });
    }

    // Reconnect button for connection error page
    const reconnectButton = document.getElementById('reconnectButton');
    const diagnoseButton = document.getElementById('diagnoseButton');

    if (reconnectButton) {
        reconnectButton.addEventListener('click', function() {
            // Show loading state
            const originalText = reconnectButton.innerHTML;
            reconnectButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghubungkan...';
            reconnectButton.disabled = true;

            // Simulate reconnection process
            setTimeout(() => {
                // Check if online (simulated)
                const isOnline = Math.random() > 0.3; // 70% chance of success

                if (isOnline) {
                    showAlert('success', 'Koneksi berhasil dipulihkan! Mengarahkan ke dashboard...');

                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1500);
                } else {
                    showAlert('error', 'Gagal terhubung. Periksa koneksi internet Anda.');
                    reconnectButton.innerHTML = originalText;
                    reconnectButton.disabled = false;
                }
            }, 2000);
        });
    }

    if (diagnoseButton) {
        diagnoseButton.addEventListener('click', function() {
            // Show diagnostic process
            const originalText = diagnoseButton.innerHTML;
            diagnoseButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mendiagnosa...';
            diagnoseButton.disabled = true;

            // Simulate diagnostic process
            setTimeout(() => {
                // Show results
                showAlert('info', 'Diagnosa selesai. Hasil: Koneksi internet terputus. Periksa router atau koneksi WiFi Anda.');

                diagnoseButton.innerHTML = originalText;
                diagnoseButton.disabled = false;
            }, 2500);
        });
    }

    // Search functionality for 404 page
    const searchErrorInput = document.getElementById('searchError');
    if (searchErrorInput) {
        searchErrorInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    showAlert('info', `Mencari: "${query}"... (Fitur pencarian halaman dalam demo)`);
                }
            }
        });
    }

    // Alert Function
    function showAlert(type, message) {
        // Remove existing alert
        const existingAlert = document.querySelector('.alert-message');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message fixed top-4 right-4 z-50 max-w-sm rounded-lg p-4 shadow-lg transform transition-all duration-300 ${
                type === 'success' 
                    ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300' 
                    : type === 'error'
                    ? 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300'
                    : 'bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300'
            }`;

        alertDiv.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas ${
                            type === 'success' ? 'fa-check-circle' : 
                            type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'
                        } text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

        // Add to body
        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.opacity = '0';
                alertDiv.style.transform = 'translateX(100%)';

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 300);
            }
        }, 5000);

        // Close button functionality
        const closeButton = alertDiv.querySelector('button');
        closeButton.addEventListener('click', () => {
            alertDiv.style.opacity = '0';
            alertDiv.style.transform = 'translateX(100%)';

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        });
    }

    // Check for dark mode preference
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Add dark mode toggle
        const themeToggle = document.createElement('button');
        themeToggle.className = 'fixed top-4 right-4 z-40 p-2 rounded-full bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm text-gray-800 dark:text-white hover:bg-white/30 dark:hover:bg-gray-800/30';
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        themeToggle.title = 'Toggle dark mode';

        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');

            // Update icon
            const icon = themeToggle.querySelector('i');
            if (document.documentElement.classList.contains('dark')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        // Set initial icon
        if (document.documentElement.classList.contains('dark')) {
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

        document.body.appendChild(themeToggle);

        // Show 404 page by default
        showErrorPage('404');
    });
</script>
</body>

</html>