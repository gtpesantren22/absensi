<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screen Rekap Pembiasaan Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }
        .container-print {
            width: 1280px;
            padding: 44px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-white">
 
    <div class="container-print" id="capture">
        
        <!-- Header -->
        <div class="border-b-4 border-slate-900 pb-5 mb-7 flex justify-between items-center">
            <div class="flex items-center gap-5">
                <?php if (!empty($lembaga->logo) && file_exists(FCPATH . 'assets/logo/' . $lembaga->logo)): ?>
                    <img src="<?= base_url('assets/logo/' . $lembaga->logo) ?>" alt="Logo" class="w-20 h-20 object-contain">
                <?php else: ?>
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Rekapitulasi Absensi Pembiasaan Siswa</h1>
                    <p class="text-base font-bold text-slate-500 uppercase mt-0.5">
                        <?= htmlspecialchars($lembaga->nama) ?>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tanggal Rekap</p>
                <p class="text-xl font-extrabold text-slate-900 whitespace-nowrap"><?= tanggal_indo($tanggal) ?></p>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl">
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Santri Wajib</span>
                <span class="text-2xl font-black text-slate-900"><?= $total_wajib ?> Siswa</span>
            </div>
            
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl">
                <span class="block text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Total Hadir</span>
                <span class="text-2xl font-black text-emerald-700"><?= $total_hadir ?> Siswa</span>
            </div>
            
            <div class="bg-rose-50 border border-rose-100 p-4 rounded-2xl">
                <span class="block text-xs font-bold text-rose-600 uppercase tracking-wider mb-1">Belum Hadir</span>
                <span class="text-2xl font-black text-rose-700"><?= $total_belum_hadir ?> Siswa</span>
            </div>
            
            <?php 
            $persen = $total_wajib > 0 ? round(($total_hadir / $total_wajib) * 100, 1) : 0;
            $colorClass = $persen >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : ($persen >= 75 ? 'text-amber-700 bg-amber-50 border-amber-100' : 'text-rose-700 bg-rose-50 border-rose-100');
            ?>
            <div class="border p-4 rounded-2xl <?= $colorClass ?>">
                <span class="block text-xs font-bold uppercase tracking-wider mb-1">Persentase Kehadiran</span>
                <span class="text-2xl font-black"><?= $persen ?>%</span>
            </div>
        </div>

        <!-- Split Content (Chart and Table) -->
        <div class="grid grid-cols-12 gap-8 items-start mb-8">
            
            <!-- Left Side: Doughnut Chart -->
            <div class="col-span-5 flex flex-col items-center justify-center bg-slate-50/50 border border-slate-100 p-6 rounded-3xl">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-6">Grafik Kehadiran</h3>
                <div class="w-64 h-64">
                    <canvas id="chartKehadiran"></canvas>
                </div>
            </div>
            
            <!-- Right Side: Class Breakdown Table -->
            <div class="col-span-7 border border-slate-200 rounded-3xl overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-4 py-3">
                    <h3 class="font-bold text-sm text-slate-800 uppercase tracking-wider">Kehadiran Per Kelas</h3>
                </div>
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/50 text-slate-600 border-b border-slate-200">
                            <th class="py-3 px-4 font-bold">Nama Kelas</th>
                            <th class="py-3 px-4 font-bold text-center">Wajib</th>
                            <th class="py-3 px-4 font-bold text-center">Hadir</th>
                            <th class="py-3 px-4 font-bold text-center">Belum Hadir</th>
                            <th class="py-3 px-4 font-bold text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 text-slate-700">
                        <?php foreach ($data_kelas as $row): ?>
                            <?php 
                            $persen_kelas = $row['wajib'] > 0 ? round(($row['hadir'] / $row['wajib']) * 100, 1) : 0;
                            ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-extrabold text-slate-900"><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td class="py-3 px-4 text-center font-bold text-slate-500"><?= $row['wajib'] ?></td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-600"><?= $row['hadir'] ?></td>
                                <td class="py-3 px-4 text-center font-bold text-rose-500"><?= $row['belum_hadir'] ?></td>
                                <td class="py-3 px-4 text-right font-black text-slate-900"><?= $persen_kelas ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Footer Note -->
        <div class="border-t border-slate-200 pt-4 flex justify-between items-center text-xs text-slate-400 font-medium">
            <p>Catatan: Rekap ini didownload pada <?= date('d-m-Y H:i:s') ?></p>
            <p class="font-bold text-slate-500">APLIKASI PRESENSI PEMBIASAAN SANTRI</p>
        </div>

    </div>

    <!-- Chart rendering Script -->
    <script>
        const ctx = document.getElementById('chartKehadiran').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Belum Hadir'],
                datasets: [{
                    data: [<?= $total_hadir ?>, <?= $total_belum_hadir ?>],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                animation: false, // MANDATORY: disable animation so server captures fully drawn immediately
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Outfit',
                                size: 12,
                                weight: 'bold'
                            },
                            color: '#1e293b'
                        }
                    }
                },
                cutout: '65%'
            }
        });
    </script>
</body>
</html>
