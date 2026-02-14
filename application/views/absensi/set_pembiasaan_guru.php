<?php $this->load->view('admin/head'); ?>

<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold">Data Absensi Pembiasaan Guru</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi pembiasaan guru</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

        <button onclick="window.location.href='<?= base_url('absensiguru/pembiasaan') ?>'" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </button>
    </div>
</div>


<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Daftar Guru yang mengikuti pembiasaan</h3>

        <div class="flex items-center space-x-2 mt-2 md:mt-0">

        </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto px-4">
        <table class="w-full" id="datatable">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                    <th class="py-3 px-4 font-medium">No</th>
                    <th class="py-3 px-4 font-medium">Nama Guru</th>
                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center  space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Saturday">
                            <span>Sabtu</span>
                        </label>
                    </th>

                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Sunday">
                            <span>Ahad</span>
                        </label>
                    </th>

                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Monday">
                            <span>Senin</span>
                        </label>
                    </th>

                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Tuesday">
                            <span>Selasa</span>
                        </label>
                    </th>

                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Wednesday">
                            <span>Rabu</span>
                        </label>
                    </th>

                    <th class="py-3 px-4 font-medium text-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="check-all-day" data-day="Thursday">
                            <span>Kamis</span>
                        </label>
                    </th>
                    <!-- <th class="py-3 px-4 font-medium">Aksi</th> -->
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="">
                <!-- Baris Data 1 -->
                <?php foreach ($guru as $index => $row):
                    $hari_guru = array_map('trim', explode(',', $row['daftar_hari']));
                ?>
                    <tr class="border-b">
                        <td class="p-2"><?= $index + 1 ?></td>
                        <td class="p-2"><?= $row['nama'] ?></td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Saturday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Saturday" data-id="<?= $row['id_guru'] ?>" data-hari="Saturday">
                        </td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Sunday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Sunday" data-id="<?= $row['id_guru'] ?>" data-hari="Sunday">
                        </td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Monday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Monday" data-id="<?= $row['id_guru'] ?>" data-hari="Monday">
                        </td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Tuesday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Tuesday" data-id="<?= $row['id_guru'] ?>" data-hari="Tuesday">
                        </td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Wednesday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Wednesday" data-id="<?= $row['id_guru'] ?>" data-hari="Wednesday">
                        </td>
                        <td class="p-2">
                            <input type="checkbox" <?= in_array('Thursday', $hari_guru) ? 'checked' : '' ?> class="day-checkbox Thursday" data-id="<?= $row['id_guru'] ?>" data-hari="Thursday">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



<?php $this->load->view('admin/foot'); ?>
<script>
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const guruId = this.getAttribute('data-id');
            const hari = this.getAttribute('data-hari');
            const isChecked = this.checked ? 1 : 0;

            fetch(`<?= site_url('absensiguru/setPembiasaan') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_guru: guruId,
                        hari: hari,
                        status: isChecked
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Status pembiasaan diperbarui');
                    } else {
                        alert('Gagal memperbarui status pembiasaan');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });
    });

    document.querySelectorAll('.check-all-day').forEach(masterCheckbox => {
        masterCheckbox.addEventListener('change', function() {
            const day = this.getAttribute('data-day');
            const isChecked = this.checked;

            document.querySelectorAll(`.day-checkbox.${day}`).forEach(checkbox => {
                checkbox.checked = isChecked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    });
</script>