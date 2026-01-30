<?php $this->load->view('admin/head'); ?>
<!-- Style helper -->
<style>
    .time-input {
        @apply w-24 md:w-28 px-2 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500;
    }
</style>
<main class="flex-1 p-4 md:p-6 overflow-y-auto">
    <!-- Header Dashboard -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Setting aplikasi</h2>
        <p class="text-gray-600 dark:text-gray-400">Halaman setting utiliti applikasi.</p>
    </div>

    <div class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 mb-4">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <!-- KIRI : Identitas Sekolah -->
            <div class="flex items-center gap-4">

                <div>
                    <h1 class="text-lg font-semibold text-gray-800 dark:text-slate-100">
                        Jumlah JP
                    </h1>
                    <form action="<?= base_url('setting/jml_rombel') ?>" method="post">
                        <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-3">
                            <div>
                                <div class="time-input-container">
                                    <input type="number" name="jml_rombel" value="<?= $jml_rombel->isi ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                </div>
                            </div>
                            <div>
                                <button type="submit" id="simpanJadwal" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 md:p-6">

        <!-- Header -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Pengaturan Jam Ketentuan
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Input jam per hari (Sabtu – Kamis)
            </p>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">

                <!-- THEAD -->
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                            Ket
                        </th>
                        <th class="px-3 py-2 text-center">Sabtu</th>
                        <th class="px-3 py-2 text-center">Minggu</th>
                        <th class="px-3 py-2 text-center">Senin</th>
                        <th class="px-3 py-2 text-center">Selasa</th>
                        <th class="px-3 py-2 text-center">Rabu</th>
                        <th class="px-3 py-2 text-center">Kamis</th>
                    </tr>
                </thead>

                <!-- TBODY -->
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="loadJam">



                </tbody>
            </table>
        </div>
    </div>

</main>
<?php $this->load->view('admin/foot'); ?>
<script>
    function loadJam() {
        $.ajax({
            url: '<?= base_url('setting/loadjam') ?>',
            type: 'get',
            dataType: 'html',
            success: function(res) {
                $('#loadJam').html(res)
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        })
    }

    loadJam()

    $(document).on('change', '.jam-input', function() {

        let jam = $(this).data('jam');
        let hari = $(this).data('hari');
        let waktu = $(this).val();


        // validasi ringan
        if (!jam) return;

        $.ajax({
            url: '<?= base_url("setting/simpanJam") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                jam: jam,
                hari: hari,
                waktu: waktu
            },
            success: function(res) {
                if (res.status) {
                    console.log('Jam tersimpan');
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>