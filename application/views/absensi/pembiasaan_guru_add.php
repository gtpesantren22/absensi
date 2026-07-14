<?php $this->load->view('admin/head'); ?>


    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold">Data Absensi Pembiasaan Guru</h2>
            <p class="text-gray-600 dark:text-gray-400">Halaman kelola data absensi pembiasaan guru</p>
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
        <form action="<?= base_url('absensiguru/saveApelGuru') ?>" method="post">
            <div class="overflow-x-auto ">
                <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                <table class="w-full" id="datatable">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-sm text-gray-500 dark:text-gray-400">
                            <th class="py-3 px-4 font-medium">No</th>
                            <th class="py-3 px-4 font-medium cursor-pointer">Nama</th>
                            <th class="py-3 px-4 font-medium">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php
                        $no  = 1;
                        $nou = 1;
                        foreach ($data as $row):
                            $rowDisabled = !empty($row['is_forced_absent']);
                        ?>
                            <input type="hidden" name="data[<?= $no ?>][id_guru]" value="<?= $row['id_guru'] ?>">
                            <?php if ($rowDisabled): ?>
                                <input type="hidden" name="data[<?= $no ?>][ket]" value="<?= $row['ket'] ?>">
                            <?php endif; ?>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 text-center">
                                    <?= $nou++ ?>
                                </td>

                                <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span><?= $row['nama'] ?></span>
                                        <?php if ($rowDisabled): ?>
                                            <?php
                                            $badgeColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                                            if ($row['absent_reason'] === 'izin') {
                                                $badgeColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
                                            } else if ($row['absent_reason'] === 'sakit') {
                                                $badgeColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
                                            }
                                            ?>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold capitalize <?= $badgeColor ?>">
                                                Tercatat <?= $row['absent_reason'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <!-- HADIR -->
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                name="data[<?= $no ?>][ket]"
                                                value="hadir"
                                                class="peer hidden"
                                                <?= $row['ket'] === 'hadir' ? 'checked' : '' ?>
                                                <?= $rowDisabled ? 'disabled' : '' ?>>
                                            <span class="
                                            inline-flex items-center justify-center
                                            w-6 h-6 rounded-full text-xs font-bold
                                            border border-green-500
                                            text-green-600
                                            peer-checked:bg-green-500
                                            peer-checked:text-white
                                            peer-disabled:opacity-60
                                        ">
                                                H
                                            </span>
                                        </label>

                                        <!-- IZIN -->
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                name="data[<?= $no ?>][ket]"
                                                value="izin"
                                                class="peer hidden"
                                                <?= $row['ket'] === 'izin' ? 'checked' : '' ?>
                                                <?= $rowDisabled ? 'disabled' : '' ?>>
                                            <span class="
                                            inline-flex items-center justify-center
                                            w-6 h-6 rounded-full text-xs font-bold
                                            border border-yellow-500
                                            text-yellow-600
                                            peer-checked:bg-yellow-500
                                            peer-checked:text-white
                                            peer-disabled:opacity-60
                                        ">
                                                I
                                            </span>
                                        </label>

                                        <!-- ALPHA -->
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                name="data[<?= $no ?>][ket]"
                                                value="alpha"
                                                class="peer hidden"
                                                <?= $row['ket'] === 'alpha' ? 'checked' : '' ?>
                                                <?= $rowDisabled ? 'disabled' : '' ?>>
                                            <span class="
                                            inline-flex items-center justify-center
                                            w-6 h-6 rounded-full text-xs font-bold
                                            border border-red-500
                                            text-red-600
                                            peer-checked:bg-red-500
                                            peer-checked:text-white
                                            peer-disabled:opacity-60
                                        ">
                                                A
                                            </span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            $no++;
                        endforeach;
                        ?>
                    </tbody>
                </table>

            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                    Simpan Absensi
                </button>
            </div>
        </form>

    </div>


<?php $this->load->view('admin/foot'); ?>
<script>

</script>