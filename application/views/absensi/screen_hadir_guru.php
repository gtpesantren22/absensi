<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="bg-gray-50 p-6">
    <div id="capture" class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-6">
        <!-- KOP -->
        <div class="flex justify-center mb-6">
            <div class="flex items-center gap-3">

                <!-- LOGO -->
                <img src="<?= base_url('assets/logo/' . $lembaga->logo) ?>"
                    alt="Logo"
                    class="w-16 h-16 object-contain">

                <!-- TEKS -->
                <div class="text-left leading-tight">
                    <h1 class="text-lg font-bold text-gray-800">
                        Rekap Kehadiran Guru
                    </h1>
                    <h1 class="text-lg font-bold text-gray-800">
                        <?= $lembaga->nama ?>
                    </h1>
                    <h1 class="text-sm font-semibold text-gray-600">
                        <?= tanggal_indo($tanggal, true) ?>
                    </h1>
                </div>

            </div>
        </div>
        <!-- END KOP -->


        <!-- Wrapper flex agar chart rapi di tengah -->
        <div class="overflow-x-auto">
            <div class="flex justify-center">
                <div id="chartKehadiran" class="mx-auto"></div>
            </div>
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="py-3 px-1 border-b text-left text-sm font-semibold text-gray-700">No</th>
                        <th rowspan="2" class="py-3 px-1 border-b text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th rowspan="2" class="py-3 px-1 border-b text-left text-sm font-semibold text-gray-700">Ket</th>
                        <th rowspan="2" class="py-3 px-1 border-b text-left text-sm font-semibold text-gray-700">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Data Guru 1 -->
                    <?php
                    $no = 1;
                    foreach ($data as $row) :
                        $gurudt = $this->db->query("SELECT nama FROM guru WHERE id_guru = '$row->id_guru' ")->row();
                    ?>
                        <tr class="bg-white">
                            <td class="py-3 px-1 border-b text-sm text-gray-700"><?= $no++ ?></td>
                            <td class="py-3 px-1 border-b text-sm font-medium text-gray-800"><?= $gurudt->nama ?></td>
                            <td class="py-3 px-1 border-b">
                                <?php if ($row->ket == 'hadir') { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-md font-medium bg-green-100 text-green-800">✅ Hadir</span>
                                <?php } elseif ($row->ket == 'izin') { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-md font-medium bg-yellow-100 text-yellow-800">ℹ️ Izin</span>
                                <?php } else { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-md font-medium bg-red-100 text-red-800">❌ Alpha</span>
                                <?php } ?>
                            </td>
                            <td class="py-3 px-1 border-b text-sm text-gray-700"><?= $row->ket != 'hadir' ?  '-' : $row->waktu ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>Catatan: Rekap ini didownload pad pada <?= date('d-m-Y H:i:s') ?></p>
        </div>
    </div>
</body>

<script>
    // Urutan: Hadir, Izin, Alpha
    const dataKehadiran = {
        labels: ['Hadir', 'Izin', 'Alpha'],
        values: [<?= $hadir->ttl ?>, <?= $izin->ttl ?>, <?= $alpha->ttl ?>] // contoh: 42 hadir, 5 izin, 3 alpha
    };

    // === 3) Konfigurasi ApexCharts ===
    const options = {
        series: dataKehadiran.values,
        labels: dataKehadiran.labels,
        chart: {
            type: 'donut',
            height: 270
        },
        colors: ['#16A34A', '#F59E0B', '#A3231B'], // hijau, kuning, abu kebiruan
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                // val = persentase, opts.w.config.labels[opts.seriesIndex] = label
                return `${val.toFixed(1)}%`;
            },
            style: {
                fontSize: '12px'
            }
        },
        legend: {
            show: true,
            position: 'bottom'
        }, // kita buat legend kustom di bawah
        stroke: {
            colors: ['#fff']
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: () => total
                        },
                        value: {
                            formatter: (v) => v
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value, {
                    seriesIndex,
                    w
                }) {
                    const label = w.config.labels[seriesIndex];
                    return `${value} (${toPercent(value)}) ${label}`;
                }
            }
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: {
                    height: 300
                }
            }
        }]
    };

    const chart = new ApexCharts(document.querySelector("#chartKehadiran"), options);
    chart.render();
</script>

</html>