<?php
function translateDay($englishDay, $language)
{
    $translations = array(
        "Sunday" => array(
            "id" => "Ahad",
            "es" => "Domingo",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Monday" => array(
            "id" => "Senin",
            "es" => "Lunes",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Tuesday" => array(
            "id" => "Selasa",
            "es" => "Martes",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Wednesday" => array(
            "id" => "Rabu",
            "es" => "Miércoles",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Thursday" => array(
            "id" => "Kamis",
            "es" => "Jueves",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Friday" => array(
            "id" => "Jum'at",
            "es" => "Viernes",
            // Tambahkan terjemahan ke bahasa lain di sini
        ),
        "Saturday" => array(
            "id" => "Sabtu",
            "es" => "Sábado",
            // Tambahkan terjemahan ke bahasa lain di sini
        )
    );

    return $translations[$englishDay][$language] ?? $englishDay;
}

function kirim_person($no_hp, $pesan, $apiKey)
{
    $CI =& get_instance();
    $wa_api_url_db = $CI->db->get_where('setting', ['key' => 'wa_api_url'])->row('isi');
    $wa_api_url = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');

    if (empty($wa_api_url)) {
        return;
    }

    $curl2 = curl_init();
    curl_setopt_array(
        $curl2,
        array(
            CURLOPT_URL => rtrim($wa_api_url, '/') . '/send-personal',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'number=' . $no_hp . '&message=' . $pesan . '&apiKey=' . $apiKey,
        )
    );
    $response = curl_exec($curl2);
    curl_close($curl2);
}

function kirim_group($id_group, $pesan, $apiKey)
{
    $CI =& get_instance();
    $wa_api_url_db = $CI->db->get_where('setting', ['key' => 'wa_api_url'])->row('isi');
    $wa_api_url = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');

    if (empty($wa_api_url)) {
        return;
    }

    $curl2 = curl_init();
    curl_setopt_array(
        $curl2,
        array(
            CURLOPT_URL => rtrim($wa_api_url, '/') . '/send-group',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'groupId=' . $id_group . '&message=' . $pesan . '&apiKey=' . $apiKey,
        )
    );
    $response = curl_exec($curl2);
    curl_close($curl2);
}

function bulan($bulan)
{
    switch ($bulan) {
        case 0:
            $bulan = "";
            break;
        case 1:
            $bulan = "Januari";
            break;
        case 2:
            $bulan = "Februari";
            break;
        case 3:
            $bulan = "Maret";
            break;
        case 4:
            $bulan = "April";
            break;
        case 5:
            $bulan = "Mei";
            break;
        case 6:
            $bulan = "Juni";
            break;
        case 7:
            $bulan = "Juli";
            break;
        case 8:
            $bulan = "Agustus";
            break;
        case 9:
            $bulan = "September";
            break;
        case 10:
            $bulan = "Oktober";
            break;
        case 11:
            $bulan = "November";
            break;
        case 12:
            $bulan = "Desember";
            break;
        default:
            $bulan = Date('F');
            break;
    }
    return $bulan;
}

function colorCell($grid)
{
    if ($grid > 48) {
        return '990000';
    } elseif ($grid > 24 && $grid <= 48) {
        return 'FF0000';
    } elseif ($grid > 16 && $grid <= 24) {
        return 'FFFF00';
    } elseif ($grid > 8 && $grid <= 16) {
        return '009999';
    } elseif ($grid > 0 && $grid <= 8) {
        return '0099FF';
    } elseif ($grid < 1) {
        return 'FFFFFF';
    }
}


function tanggal_indo($tanggal, $tampilkan_hari = false)
{
    $hari_array = array(
        0 => 'Ahad',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu'
    );

    $bulan_array = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );

    // Format input harus YYYY-MM-DD
    $tanggal_obj = strtotime($tanggal);
    $hari = date('w', $tanggal_obj);
    $tgl = date('j', $tanggal_obj);
    $bln = date('n', $tanggal_obj);
    $thn = date('Y', $tanggal_obj);

    $format_indo = $tgl . ' ' . $bulan_array[$bln] . ' ' . $thn;

    if ($tampilkan_hari) {
        $format_indo = $hari_array[$hari] . ', ' . $format_indo;
    }

    return $format_indo;
}

function cariBulan(array $tanggal)
{
    if (empty($tanggal)) {
        return '';
    }

    $bulanUnik = [];
    $tahunUnik = [];

    foreach ($tanggal as $tgl) {
        $time = strtotime($tgl);
        if (!$time) continue;

        $bulanUnik[] = date('n', $time); // angka bulan
        $tahunUnik[] = date('Y', $time);
    }

    $bulanUnik = array_unique($bulanUnik);
    $tahunUnik = array_unique($tahunUnik);

    // Mapping nama bulan Indonesia
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $bulanText = array_map(fn($b) => $namaBulan[$b], $bulanUnik);

    sort($bulanText);

    return implode(', ', $bulanText) . ' ' . implode(', ', $tahunUnik);
}
function kodeFromNumber($num)
{
    $kode = '';
    while ($num > 0) {
        $num--;
        $kode = chr(65 + ($num % 26)) . $kode;
        $num = intdiv($num, 26);
    }
    return $kode;
}

function generateUsernameUnique($nama, $table = 'user', $field = 'username')
{
    $CI = &get_instance();

    // 1. Normalisasi nama
    $clean = strtolower(trim($nama));
    $clean = preg_replace('/[^a-z0-9\s]/', '', $clean);
    $clean = preg_replace('/\s+/', ' ', $clean);

    if ($clean === '') {
        $clean = 'user';
    }

    // 2. Ambil maksimal 2 kata biar pendek
    $parts = explode(' ', $clean);
    $base = implode('', array_slice($parts, 0, 2));

    // 3. Hash pendek base36 (lebih ringkas)
    $hash = substr(rand(0, 99), 0);

    $username = $base . $hash;

    // 4. Safety check (fallback)
    if ($CI->db->where($field, $username)->count_all_results($table) > 0) {
        $username .= substr(base_convert(time(), 10, 36), -2);
    }

    return $username;
}

function generatePassword6()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $password = '';

    for ($i = 0; $i < 6; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $password;
}

function inisial($nama){
    $nama = trim($nama);
    $words = explode(" ", $nama);

    if(count($words) > 1){
        return strtoupper($words[0][0] . $words[1][0]);
    }else{
        return strtoupper(substr($nama,0,2));
    }
}
