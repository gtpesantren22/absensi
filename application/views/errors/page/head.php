<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Aplikasi Absensi Sekolah</title>
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

        .error-bg {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .dark .error-bg {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        .error-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .error-code {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dark .error-code {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="error-bg min-h-screen flex items-center justify-center p-4 transition-colors duration-200">
    <div id="errorContainer" class="w-full max-w-2xl">