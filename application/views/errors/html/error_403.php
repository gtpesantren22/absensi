<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <title>403 | Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 max-w-md text-center">
        <h1 class="text-5xl font-bold text-red-600">403</h1>
        <p class="mt-3 text-gray-700 dark:text-gray-300">
            Akses ditolak. Anda tidak memiliki izin.
        </p>

        <a href="<?= base_url() ?>"
            class="mt-6 inline-block px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
            Kembali ke Beranda
        </a>
    </div>

</body>

</html>