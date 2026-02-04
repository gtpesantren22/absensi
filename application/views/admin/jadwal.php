<?php $this->load->view('admin/head'); ?>
<style>
    .jadwal-item {
        transition: all 0.2s ease;
        border-left-width: 4px;
    }

    .jadwal-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .dark .jadwal-item:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: 80px repeat(8, 1fr);
        gap: 1px;
        background-color: #e5e7eb;
    }

    .dark .schedule-grid {
        background-color: #4b5563;
    }

    .schedule-cell {
        min-height: 50px;
        background-color: white;
    }

    .dark .schedule-cell {
        background-color: #1f2937;
    }

    .schedule-header {
        background-color: #f9fafb;
        font-weight: 600;
    }

    .dark .schedule-header {
        background-color: #111827;
    }

    .schedule-time {
        background-color: #f3f4f6;
    }

    .dark .schedule-time {
        background-color: #374151;
    }
</style>


<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Data Jadwal Pelajaran</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman kelola data jadwal pelajaran</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
        <!-- Filter -->
        <!-- <div class="relative">
                <select id="" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option> -Pilih Hari- </option>
                    <?php
                    $days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

                    foreach ($days as $index => $day) {
                        echo "<option value=\"$day\"";

                        // Periksa apakah nama hari saat ini sesuai dengan $day dalam bahasa Inggris
                        if (date('l') === $day) {
                            echo "";
                        }

                        echo ">" . translateDay($day, 'id') . "</option>";
                    }
                    ?>
                </select>
            </div> -->

        <!-- Tombol Tambah Siswa -->
        <!-- <button onclick="openModal('tambahJadwalModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Jadwal
            </button> -->
        <button onclick="window.location.href=''" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <i class="fas fa-calendar mr-2"></i>
            Cek Full Jadwal
        </button>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl w-full  max-h-[90vh] overflow-y-auto mb-6">

    <!-- Form Tambah  -->
    <div class="p-6 ">
        <form id="formTambahMapel" action="<?= base_url('jadwal/add') ?>" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-3">
                <!-- Pilih Hari -->
                <div>
                    <label class="block text-sm font-medium mb-2">Hari <span class="text-red-500">*</span></label>
                    <div class="time-input-container">
                        <select id="selectDay" name="hari" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            <option value="">-- Pilih Hari --</option>
                            <?php
                            $days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

                            foreach ($days as $index => $day) {
                                echo "<option value=\"$day\"";

                                // Periksa apakah nama hari saat ini sesuai dengan $day dalam bahasa Inggris
                                if (date('l') === $day) {
                                    echo "";
                                }

                                echo ">" . translateDay($day, 'id') . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Kelas <span class="text-red-500">*</span></label>
                    <select id="selectKelas" name="id_kelas" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k) : ?>
                            <option value="<?= $k->id_kelas ?>"><?= $k->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>



                <!-- Jam Dari -->
                <div>
                    <label class="block text-sm font-medium mb-2">Dari jam <span class="text-red-500">*</span></label>
                    <div class="time-input-container">
                        <input type="number" id="jamDari" name="jam_dari" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                </div>

                <!-- Jam Sampai -->
                <div>
                    <label class="block text-sm font-medium mb-2">Sampai <span class="text-red-500">*</span></label>
                    <div class="time-input-container">
                        <input type="number" id="jamSampai" name="jam_sampai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                </div>


                <!-- Pilih Guru -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Guru <span class="text-red-500">*</span></label>
                    <select id="selectGuru" name="id_guru" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($guru as $g) : ?>
                            <option value="<?= $g->id_guru ?>"><?= $g->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pilih Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih MaPel <span class="text-red-500">*</span></label>
                    <select id="selectMapel" name="id_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapel as $m) : ?>
                            <option value="<?= $m->id_mapel ?>"><?= $m->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"> <span class="text-red-500">*</span></label>
                    <button type="submit" id="simpanJadwal" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                        Simpan Data
                    </button>
                </div>

            </div>

            <!-- <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('tambahJadwalModal')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                </div> -->
        </form>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">
    <!-- Header Jadwal -->
    <!-- <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-lg">Jadwal Pelajaran <span id="kelasDisplay">XII IPA 1</span></h3>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span id="dateRangeDisplay">15 - 20 April 2024</span>
                </div>
            </div>
        </div> -->

    <!-- Grid Jadwal -->
    <div class="overflow-x-auto custom-scrollbar">

    </div>
    <div id="showJadwal"></div>

    <!-- Footer Jadwal -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                    <div class="flex items-center text-yellow-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-semibold">Informasi Jadwal Bentrok</span>
                    </div>

                    <hr class="my-2 border-yellow-200">

                    <div id="showBentrok" class="space-y-2 text-sm">
                        <!-- hasil bentrok akan di-render di sini -->
                    </div>
                </div>

            </div>
            <!-- <div class="flex items-center mt-2 md:mt-0">
                    <i class="fas fa-user-clock mr-2"></i>
                    <span>Total jam pelajaran: <span class="font-medium">34 jam</span></span>
                </div> -->
        </div>
    </div>
</div>

<div id="editJadwalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold">Edit Data Jadwal</h3>
            <button onclick="closeModal('editJadwalModal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Tambah  -->
        <div class="p-6">
            <form id="formEditMapel" action="<?= base_url('jadwal/update') ?>" method="POST">
                <input type="hidden" name="id_jadwal" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <!-- Pilih Hari -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Hari <span class="text-red-500">*</span></label>
                        <div class="time-input-container">
                            <select id="selectDayModal" name="hari" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Saturday">Sabtu</option>
                                <option value="Sunday">Ahad</option>
                                <option value="Monday">Senin</option>
                                <option value="Tuesday">Selasa</option>
                                <option value="Wednesday">Rabu</option>
                                <option value="Thursday">Kamis</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Kelas -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Pilih Kelas <span class="text-red-500">*</span></label>
                        <select id="selectKelas" name="id_kelas" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k) : ?>
                                <option value="<?= $k->id_kelas ?>"><?= $k->nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <!-- Jam Dari -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Dari jam <span class="text-red-500">*</span></label>
                        <div class="time-input-container">
                            <input type="number" id="jamDari" name="jam_dari" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                    </div>

                    <!-- Jam Sampai -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Sampai <span class="text-red-500">*</span></label>
                        <div class="time-input-container">
                            <input type="number" id="jamSampai" name="jam_sampai" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                    </div>


                    <!-- Pilih Guru -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Pilih Guru <span class="text-red-500">*</span></label>
                        <select id="selectGuru" name="id_guru" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($guru as $g) : ?>
                                <option value="<?= $g->id_guru ?>"><?= $g->nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pilih Mata Pelajaran -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Pilih Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select id="selectMapel" name="id_mapel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <?php foreach ($mapel as $m) : ?>
                                <option value="<?= $m->id_mapel ?>"><?= $m->nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="flex items-center justify-between">
                    <!-- KIRI: HAPUS -->
                    <button
                        type="button" data-id=""
                        id="hapusJadwal"
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium tombol-hapus">
                        Hapus Jadwal
                    </button>

                    <!-- KANAN: BATAL + SIMPAN -->
                    <div class="flex space-x-3">
                        <button
                            type="button"
                            onclick="closeModal('editJadwalModal')"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Batal
                        </button>

                        <button
                            type="submit"
                            id="updateJadwal"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                            Simpan Data
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>



<?php $this->load->view('admin/foot'); ?>

<script>
    let selectedDayLive = '';
    let currentIdJadwal = null;

    document.getElementById('selectDay').addEventListener('change', function() {
        var selectedDay = this.value;
        selectedDayLive = selectedDay;
        showJadwal(selectedDay);
    });

    function showJadwal(day) {
        // Lakukan permintaan AJAX untuk mendapatkan data jadwal berdasarkan hari yang dipilih
        fetch('<?= base_url('jadwal/fetch_jadwal/') ?>' + day)
            .then(response => response.text())
            .then(data => {
                document.getElementById('showJadwal').innerHTML = data;
            })
            .catch(error => console.error('Error fetching jadwal:', error));
        showBentrok(day);
    }

    function showBentrok(day) {
        fetch('<?= base_url('jadwal/check_bentrok/') ?>' + day)
            .then(response => response.text())
            // .then(response => response.json())
            .then(data => {
                document.getElementById('showBentrok').innerHTML = data;
                // console.log(data.status);
                // console.log(data.message);
                // console.log(data.total);
                // console.log(data.data);

            })
            .catch(error => console.error('Error fetching bentrok:', error));
    }

    document.getElementById('simpanJadwal').addEventListener('click', function(event) {
        event.preventDefault();

        var form = document.getElementById('formTambahMapel');
        var formData = new FormData(form);
        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status == 'success') {
                    // alert('Jadwal berhasil ditambahkan!');
                    // closeModal('tambahJadwalModal');
                    showJadwal(formData.get('hari'));
                } else {
                    // alert('Gagal menambahkan jadwal: ' + data.message);
                    Swal.fire('Terhapus!', data.message, 'error');
                }
            })
            .catch(error => console.error('Error adding jadwal:', error));
    });

    document.getElementById('updateJadwal').addEventListener('click', function(event) {
        event.preventDefault();

        var form = document.getElementById('formEditMapel');
        var formData = new FormData(form);
        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status == 'success') {
                    // alert('Jadwal berhasil diupdate!');
                    closeModal('editJadwalModal');
                    showJadwal(formData.get('hari'));
                } else {
                    // alert('Gagal mengupdate jadwal: ' + data.message);
                    Swal.fire('Terhapus!', data.message, 'error');
                }
            })
            .catch(error => console.error('Error updating jadwal:', error));
    });

    document.addEventListener('click', function(e) {
        const item = e.target.closest('.item-jadwal');
        if (!item) return;

        const id = item.dataset.jadwalId;
        currentIdJadwal = item.dataset.jadwalId;
        console.log('Klik jadwal ID:', id);

        // contoh:
        openModalEdit(id);
    });

    function openModalEdit(id) {
        // Isi data modal edit berdasarkan ID jadwal yang diklik
        // Contoh: fetch data jadwal dari server dan isi form di modal edit
        fetch('<?= base_url('jadwal/get_jadwal/') ?>' + id)
            .then(response => response.json())
            .then(data => {
                // Isi form di modal edit dengan data yang diterima
                document.querySelector('#editJadwalModal input[name="id_jadwal"]').value = data.id_jadwal;
                document.querySelector('#editJadwalModal select[name="hari"]').value = data.hari;
                document.querySelector('#editJadwalModal select[name="id_kelas"]').value = data.id_kelas;
                document.querySelector('#editJadwalModal select[name="id_guru"]').value = data.id_guru;
                document.querySelector('#editJadwalModal select[name="id_mapel"]').value = data.id_mapel;
                document.querySelector('#editJadwalModal input[name="jam_dari"]').value = data.jam_dari;
                document.querySelector('#editJadwalModal input[name="jam_sampai"]').value = data.jam_sampai;
            })
            .catch(error => console.error('Error fetching jadwal data:', error));

        // Tampilkan modal edit
        openModal('editJadwalModal');
    }

    $(document).on('click', '.tombol-hapus', function(e) {
        e.preventDefault();

        const id = currentIdJadwal;
        const base_url = '<?= base_url() ?>';

        Swal.fire({
            title: 'Yakin?',
            text: 'Data Jadwal akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'jadwal/hapus', {
                        id
                    })
                    .done(() => {
                        Swal.fire('Terhapus!', 'Data jadwal telah dihapus.', 'success');
                        closeModal('editJadwalModal');
                        showJadwal(selectedDayLive);
                    })
            }
        });
        // alert(id)
    });
</script>