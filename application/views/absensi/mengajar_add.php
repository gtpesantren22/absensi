<?php $this->load->view('admin/head'); ?>


<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Absensi Mengajar Guru</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi mengajar guru</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

        <!-- <button onclick="window.location.href='<?= base_url('absensiguru/pembiasaan') ?>'" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </button> -->
    </div>
</div>


<!-- Tabel Data -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Tabel dengan Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Absensi Hari ini <?= tanggal_indo($tanggal, true) ?></h3>


    </div>

    <!-- Tabel -->

    <div class="overflow-x-auto ">
        <input type="hidden" name="tanggal" value="<?= $tanggal ?>">

        <table class="min-w-full text-sm border border-gray-300 dark:border-gray-600 border-collapse">
            <thead class="bg-slate-100 dark:bg-gray-800 text-slate-700 dark:text-gray-200">
                <tr>
                    <th class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-left">
                        Guru/Jam
                    </th>
                    <?php for ($i = 1; $i <= $jml_jp; $i++) : ?>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-center">
                            <?= $i ?>
                        </th>
                    <?php endfor; ?>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900">
                <?php
                $hariini = $tanggal;
                $id_semester_aktif = $this->session->userdata('id_semester_aktif');
                foreach ($data as $row) :
                    $guru = $row['id_guru'];

                    for ($i = 1; $i <= $jml_jp; $i++) {
                        ${"cek$i"} = $this->db
                            ->query("SELECT * FROM mengajar WHERE id_guru='$guru' AND tanggal='$hariini' AND jam=$i AND id_lembaga = '$id_lembaga'")
                            ->row();
                    }

                    // Get daily attendance status
                    $cek_hadir = $this->db->get_where('kehadiran_guru', [
                        'id_guru' => $guru,
                        'tanggal' => $hariini,
                        'id_semester' => $id_semester_aktif
                    ])->row();

                    $forcedLabel = '';
                    if ($cek_hadir && in_array(strtolower($cek_hadir->ket), ['izin', 'sakit', 'alpha', 'alfa', 'cuti'])) {
                        $ketAbsen = strtolower($cek_hadir->ket);
                        $badgeColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                        if ($ketAbsen === 'izin') {
                            $badgeColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
                        } else if ($ketAbsen === 'sakit') {
                            $badgeColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
                        }
                        $forcedLabel = ' <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold capitalize ' . $badgeColor . '">' . $ketAbsen . '</span>';
                    }
                ?>

                    <tr class="odd:bg-white even:bg-slate-50 dark:odd:bg-gray-900 dark:even:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 transition">

                        <!-- Nama Guru -->
                        <td class="px-3 py-2 border border-gray-300 dark:border-gray-600 font-medium">
                            <div class="flex items-center gap-1.5">
                                <a
                                    data-guru="<?= $row['id_guru'] ?>"
                                    class="hover:underline cursor-pointer show-rinci">
                                    <?= $row['nama'] ?>
                                </a>
                                <?= $forcedLabel ?>
                            </div>
                        </td>

                        <!-- Jam 1 - 8 -->
                        <?php for ($i = 1; $i <= $jml_jp; $i++) : ?>
                            <td class="
                        px-3 py-2 border border-gray-300 dark:border-gray-600 text-center
                        <?= in_array($i, $row['jam'])
                                ? 'bg-orange-200 dark:bg-orange-600/30 text-orange-900 dark:text-orange-200 font-semibold'
                                : '' ?>
                    ">
                                <?= ${"cek$i"} ? ${"cek$i"}->ket : '-' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>


    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">

    </div>


</div>



<!-- Modal Tambah Mapel -->
<div id="inputModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-l font-bold">Input absensi mengajar</h3>
            <button onclick="closeModal('inputModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Tambah Mapel -->
        <div class="px-6 py-4">
            <div id="showHasil"></div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/foot'); ?>
<script>
    document.querySelectorAll('.show-rinci').forEach(function(element) {
        element.addEventListener('click', function() {
            const guru = this.getAttribute('data-guru');
            const tanggal = "<?= $tanggal ?>";
            // Lakukan sesuatu dengan data guru, misalnya tampilkan di modal
            fetch(`<?= base_url('mengajar/rincian_guru') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `guru=${guru}&tanggal=${tanggal}`
                })
                .then(response => response.text())
                .then(data => {
                    // Tampilkan data di modal
                    document.getElementById('showHasil').innerHTML = data;
                    openModal('inputModal');
                })
                .catch(error => console.error('Error:', error));
        });
    });

    $(document).on('submit', '#form-absensi', function(e) {
        e.preventDefault();
        const formData = [];

        // Loop melalui setiap input radio yang dipilih
        $('input[type="radio"]:checked').each(function() {
            // const name = $(this).attr('name');
            const value = $(this).val(); // e.g., "Main"
            const jam = $(this).data('jam'); // e.g., "1"
            const guru = $(this).data('guru'); // e.g., "Hobi bermain"
            const alasan = $(this).closest('tr').find('textarea').val();

            // Tambahkan data ke array formData
            formData.push({
                value: value,
                jam: jam,
                guru: guru,
                alasan: alasan
            });
        });

        // console.log(formData);

        // Kirim data ke server menggunakan AJAX
        $.ajax({
            url: '<?= site_url("mengajar/simpanJam") ?>',
            type: 'POST',
            data: {
                datas: formData,
                tanggal: '<?= $tanggal ?>'
            },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan data!');
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menyimpan data.');
            }
        });
    });
</script>