<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 p-6">
    <div id="capture" class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
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
                        Rekap Kehadiran Mengajar Guru
                    </h1>
                    <h1 class="text-lg font-bold text-gray-800">
                        <?= $lembaga->nama ?>
                    </h1>
                </div>

            </div>
        </div>
        <!-- END KOP -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-gray-600">Hari: <span class="font-semibold"><?= $hari ?></span></p>
                <p class="text-gray-600">Tanggal: <span class="font-semibold"><?= tanggal_indo($tanggal) ?></span></p>
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">No</th>
                        <th rowspan="2" class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th rowspan="2" class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Kehadiran</th>
                        <th colspan="4" class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Absensi Mengajar
                        </th>
                    </tr>
                    <tr>
                        <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-700">Jam Wajib</th>
                        <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-700">Mengajar</th>
                        <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-700">%</th>
                        <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Data Guru 1 -->
                    <?php
                    $no = 1;
                    foreach ($data as $row) :
                    ?>
                        <tr class="bg-white">
                            <td class="py-3 px-4 border-b text-sm text-gray-700"><?= $no++ ?></td>
                            <td class="py-3 px-4 border-b text-sm font-medium text-gray-800"><?= $row['nama_guru'] ?></td>
                            <td class="py-3 px-4 border-b">
                                <?php if ($row['hadir'] == 'hadir') { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">hadir</span>
                                <?php } elseif ($row['hadir'] == 'izin') { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">izin</span>
                                <?php } elseif ($row['hadir'] == 'alpha') { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">alpha</span>
                                <?php } else { ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">cuti</span>
                                <?php } ?>
                            </td>
                            <td class="py-3 px-4 border-b text-sm text-center text-gray-700"><?= $row['jam'] ?></td>
                            <td class="py-3 px-4 border-b text-sm text-center text-gray-700"><?= $row['masuk'] ?></td>
                            <td class="py-3 px-4 border-b text-sm text-center font-medium"><?= round($row['persen'], 1) ?>%</td>
                            <td class="py-3 px-4 border-b text-sm text-center text-green-600"><?= $row['alasan'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="2" class="py-3 px-4 border-t text-sm font-semibold text-gray-700 text-right">Total:</td>
                        <td class="py-3 px-4 border-t text-sm font-semibold text-gray-700 text-center"><?= $totalkehadiran && $totalguru != '' ? round($totalkehadiran / $totalguru * 100, 1) : 0 ?>%</td>
                        <td class="py-3 px-4 border-t text-sm font-semibold text-gray-700 text-center"><?= $totaljamwajib ?></td>
                        <td class="py-3 px-4 border-t text-sm font-semibold text-gray-700 text-center"><?= $totaljammasuk ?></td>
                        <td class="py-3 px-4 border-t text-sm font-semibold text-blue-600 text-center"><?= $totaljammasuk && $totaljamwajib != '' ? round($totaljammasuk / $totaljamwajib * 100, 1) : '' ?>%</td>
                        <td class="py-3 px-4 border-t text-sm font-semibold text-gray-700 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>Catatan: Rekap ini dihasilkan secara otomatis pada <?= date('d-m-Y H:i:s') ?></p>
        </div>
    </div>
</body>

</html>