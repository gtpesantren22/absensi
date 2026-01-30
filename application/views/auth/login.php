<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Absensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
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
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .login-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        }

        .dark .login-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        }

        .login-card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .dark .login-card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .dark .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>

<body class="login-bg min-h-screen flex items-center justify-center p-4 transition-colors duration-200">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl login-card p-8">
            <!-- Logo & Brand -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-xl bg-primary-600 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-school text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Absensi APP - DWK</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Sistem Absensi Digital</p>
            </div>

            <!-- Login Form -->
            <form id="loginForm">
                <!-- Username/Email -->
                <div class="mb-6">
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user mr-2"></i>Username atau Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            id="username" name="username"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 input-focus focus:outline-none transition duration-200"
                            placeholder="Masukkan username atau email"
                            required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input
                            type="password" name="password"
                            id="password"
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 input-focus focus:outline-none transition duration-200"
                            placeholder="Masukkan password"
                            required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="rememberMe"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <label for="rememberMe" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Ingat saya
                        </label>
                    </div>
                    <a href="#" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="loginButton"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <span id="buttonText">Masuk ke Sistem</span>
                    <i id="loadingIcon" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            -
                        </span>
                    </div>
                </div>


                <!-- Register Link -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Belum punya akun?
                        <a href="#" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-white dark:text-gray-300 opacity-80">
                © 2024 Aplikasi Absensi Sekolah. Hak cipta dilindungi.
            </p>
            <a
                href="https://smkdwk.sch.id"
                target="_blank"
                class="text-sm font-medium text-gray-300 dark:text-gray-400 
                   hover:text-emerald-600 dark:hover:text-emerald-400
                   transition">
                Powered by <span class="font-semibold">SMKDWK</span>
            </a>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            if (type === 'text') {
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });

        // Form Submission
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        const loadingIcon = document.getElementById('loadingIcon');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form values
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('rememberMe').checked;
            const form = this;

            // Validation
            if (!username.trim() || !password.trim()) {
                showAlert('error', 'Harap isi semua field yang diperlukan');
                return;
            }

            // Show loading state
            loginButton.disabled = true;
            buttonText.textContent = 'Memproses...';
            loadingIcon.classList.remove('hidden');

            // Simulate API call (replace with actual login logic)
            setTimeout(() => {


                fetch("<?= base_url('auth/login') ?>", {
                        method: "POST",
                        body: new FormData(form)
                    })
                    .then(res => res.json())
                    .then(res => {
                        // console.log(res);

                        if (res.status) {
                            showAlert('success', 'Login berhasil! Mengarahkan ke dashboard...');
                            setTimeout(() => window.location.href = res.redirect, 800);
                        } else {
                            showAlert('error', res.message);
                            loginButton.disabled = false;
                            buttonText.textContent = 'Masuk ke Sistem';
                            loadingIcon.classList.add('hidden');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showAlert('error', 'Response server bukan JSON');
                        loginButton.disabled = false;
                        buttonText.textContent = 'Masuk ke Sistem';
                        loadingIcon.classList.add('hidden');
                    });

            }, 2000);
        });

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
                    : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300'
            }`;

            alertDiv.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas ${
                            type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'
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

        // Demo credentials autofill for testing
        document.addEventListener('DOMContentLoaded', function() {
            // For demo purposes - remove this in production
            // Check for dark mode preference
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }

            // Add dark mode toggle for login page
            const themeToggle = document.createElement('button');
            themeToggle.className = 'fixed top-4 right-4 z-40 p-2 rounded-full bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm text-white hover:bg-white/30 dark:hover:bg-gray-800/30';
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
        });

        // Enter key to submit form
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !loginButton.disabled) {
                loginForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>

</html>