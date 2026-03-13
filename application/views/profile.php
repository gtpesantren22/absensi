<?php $this->load->view('admin/head'); ?>
<!-- Header Halaman -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">User Profile</h2>
        <p class="text-gray-600 dark:text-gray-400">Informasi profil pengguna sistem</p>
    </div>
</div>

<!-- Card Profile -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">

    <!-- Header Card -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Profil Pengguna</h3>
    </div>

    <!-- Body -->
    <div class="p-6">

        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

            <!-- Foto Profil -->
            <div class="flex-shrink-0">
                <img
                    src="https://ui-avatars.com/api/?name=<?= inisial($guru->nama) ?>&background=0D8ABC&color=fff"
                    class="w-28 h-28 rounded-full border-4 border-gray-200 dark:border-slate-700 shadow">
            </div>

            <!-- Informasi User -->
            <div class="flex-1 w-full">

                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100"><?= $guru->nama ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Username</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100"><?= $data->username ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No. HP</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100"><?= $guru->no_hp ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Role</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100"><?= strtoupper($data->level) ?></p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Card Edit Akun -->
<div class="bg-white dark:bg-gray-800 rounded-xl flux-shadow overflow-hidden mb-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-lg">Edit Username & Password</h3>
    </div>

    <!-- Body -->
    <div class="p-6">

        <form action="<?= base_url('profile/update_account') ?>" method="post">

            <div class="grid md:grid-cols-2 gap-4">

                <!-- Username -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        value="<?= $data->username ?>"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-gray-800 dark:text-gray-100 focus:ring focus:ring-blue-200">
                </div>

                <!-- Password Baru -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Password Baru
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="password_baru"
                            name="password_baru"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-gray-800 dark:text-gray-100">

                        <button type="button"
                            onclick="togglePassword('password_baru', this)"
                            class="absolute right-2 top-2.5 text-gray-500 hover:text-gray-700">

                            👁
                        </button>
                    </div>
                </div>


                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Konfirmasi Password Baru
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="password_konfirmasi"
                            name="password_konfirmasi"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-gray-800 dark:text-gray-100">

                        <button type="button"
                            onclick="togglePassword('password_konfirmasi', this)"
                            class="absolute right-2 top-2.5 text-gray-500 hover:text-gray-700">

                            👁
                        </button>
                    </div>

                    <p id="password_message" class="text-sm mt-1"></p>
                </div>

            </div>

            <!-- Button -->
            <div class="mt-6">
                <button
                    id="btn_submit"
                    type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow transition">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>
<?php $this->load->view('admin/foot'); ?>
<script>
    function togglePassword(id, btn) {

        const input = document.getElementById(id);

        if (input.type === "password") {
            input.type = "text";
            btn.innerHTML = "🙈";
        } else {
            input.type = "password";
            btn.innerHTML = "👁";
        }

    }
</script>
<script>
    const passwordBaru = document.getElementById("password_baru");
    const passwordKonfirmasi = document.getElementById("password_konfirmasi");
    const message = document.getElementById("password_message");
    const btnSubmit = document.getElementById("btn_submit");

    function cekPassword() {

        const pass1 = passwordBaru.value;
        const pass2 = passwordKonfirmasi.value;

        // jika password kosong semua -> hanya ubah username
        if (pass1 === "" && pass2 === "") {
            message.innerHTML = "";
            btnSubmit.disabled = false;
            btnSubmit.classList.remove("opacity-50", "cursor-not-allowed");
            return;
        }

        // jika salah satu kosong
        if (pass1 !== "" && pass2 === "") {
            message.innerHTML = "Konfirmasi password belum diisi";
            message.className = "text-sm mt-1 text-yellow-500";

            btnSubmit.disabled = true;
            btnSubmit.classList.add("opacity-50", "cursor-not-allowed");
            return;
        }

        // jika password cocok
        if (pass1 === pass2) {

            message.innerHTML = "Password cocok";
            message.className = "text-sm mt-1 text-green-500";

            btnSubmit.disabled = false;
            btnSubmit.classList.remove("opacity-50", "cursor-not-allowed");

        } else {

            message.innerHTML = "Password tidak sama";
            message.className = "text-sm mt-1 text-red-500";

            btnSubmit.disabled = true;
            btnSubmit.classList.add("opacity-50", "cursor-not-allowed");

        }

    }

    passwordBaru.addEventListener("keyup", cekPassword);
    passwordKonfirmasi.addEventListener("keyup", cekPassword);
</script>