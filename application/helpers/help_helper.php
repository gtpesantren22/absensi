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

function generate_kode_mapel($nama, $id_master = null)
{
    $nama_clean = trim($nama);
    $kode = $id_master ? 'M-' . $id_master : 'M-' . mt_rand(1000, 9999);

    // 1. Try extracting text inside parentheses first (e.g., "Ilmu Pengetahuan Alam (IPA)" -> "IPA")
    if (preg_match('/\(([^)]+)\)/', $nama_clean, $matches)) {
        $inner = trim($matches[1]);
        $inner_clean = preg_replace('/[^A-Za-z]/', '', $inner);
        $len = strlen($inner_clean);
        
        // If the inner text is a short alphabetic abbreviation (2 to 5 characters) and not a blacklist word
        $blacklist = ['umum', 'wajib', 'minat', 'peminatan', 'pemin', 'wjb', 'umm', 'kpm', 'sore', 'pagi'];
        if ($len >= 2 && $len <= 5 && !in_array(strtolower($inner_clean), $blacklist)) {
            return strtoupper($inner_clean);
        }
        
        // Otherwise, strip the parentheses and their contents and keep processing
        $nama_clean = trim(preg_replace('/\s*\([^)]+\)/', '', $nama_clean));
    }

    // 2. Clean multiple spaces and non-alphanumeric chars (keep spaces and letters)
    $nama_clean = preg_replace('/[^A-Za-z0-9\s-]/', '', $nama_clean);
    $nama_clean = preg_replace('/\s+/', ' ', $nama_clean);
    $nama_upper = strtoupper(trim($nama_clean));

    if (empty($nama_upper)) {
        return $kode;
    }

    // 3. Custom mappings for common Indonesian subjects (both multi-word and single-word)
    $custom_codes = [
        // Multi-word subjects
        'BAHASA INDONESIA' => 'BIN',
        'BAHASA INGGRIS' => 'BIG',
        'BAHASA ARAB' => 'BAR',
        'BAHASA DAERAH' => 'BDH',
        'PENDIDIKAN AGAMA ISLAM' => 'PAI',
        'PENDIDIKAN AGAMA ISLAM DAN BUDI PEKERTI' => 'PABP',
        'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN' => 'PPKN',
        'PENDIDIKAN PANCASILA' => 'PPN',
        'PENDIDIKAN JASMANI OLAHRAGA DAN KESEHATAN' => 'PJOK',
        'PENDIDIKAN JASMANI, OLAHRAGA, DAN KESEHATAN' => 'PJOK',
        'PENDIDIKAN JASMANI OLAHRAGA KESEHATAN' => 'PJOK',
        'ILMU PENGETAHUAN ALAM' => 'IPA',
        'ILMU PENGETAHUAN SOSIAL' => 'IPS',
        'SENI BUDAYA' => 'SBD',
        'SENI BUDAYA DAN KETERAMPILAN' => 'SBK',
        'SENI BUDAYA DAN PRAKARYA' => 'SBP',
        'PRAKARYA DAN KEWIRAUSAHAAN' => 'PKWU',
        'SEJARAH INDONESIA' => 'SIND',
        'BIMBINGAN CONSELING' => 'BK',
        'BIMBINGAN KONSELING' => 'BK',

        // 1-word subjects
        'MATEMATIKA' => 'MTK',
        'BIOLOGI' => 'BIO',
        'FISIKA' => 'FIS',
        'KIMIA' => 'KIM',
        'GEOGRAFI' => 'GEO',
        'SEJARAH' => 'SJR',
        'EKONOMI' => 'EKO',
        'SOSIOLOGI' => 'SOS',
        'TAFHIDZ' => 'TFZ',
        'TAHFIDZ' => 'TFZ',
        'HADITS' => 'HDT',
        'HADIS' => 'HDT',
        'FIQIH' => 'FQH',
        'FIKIH' => 'FQH',
        'AQIDAH' => 'AQD',
        'AKHLAK' => 'AKH',
        'NAHWU' => 'NHW',
        'SHOROF' => 'SRF',
        'TEMATIK' => 'TMK',
        'PRAMUKA' => 'PRM',
        'KEWIRAUSAHAAN' => 'KWU',
        'INFORMATIKA' => 'INF',
        'KERAJINAN' => 'KRJ',
        'OLAHRAGA' => 'OR',
        'SENI' => 'SEN',
        'BUDAYA' => 'BDY',
        'BAHASA' => 'BHS',
        'SASTRA' => 'SST',
        'DINIYAH' => 'DNY',
        'TAJWID' => 'TJW',
        'KHAT' => 'KHT',
        'IMLA' => 'IML',
        'INSYA' => 'INS',
        'MAHFUDZAT' => 'MFZ',
        'MUTALAAH' => 'MTL',
        'BALAGHAH' => 'BLG',
        'FAROIDH' => 'FRD',
        'KALIGRAFI' => 'KLG',
        'ASWAJA' => 'ASW',
        'KEASWAJAAN' => 'ASW',
        'PANCASILA' => 'PPN'
    ];

    if (isset($custom_codes[$nama_upper])) {
        return $custom_codes[$nama_upper];
    }

    $words = array_filter(explode(' ', $nama_clean));
    if (count($words) === 1) {
        // --- 1-word subject abbreviation logic ---
        $word = strtoupper($words[0]);
        $len = strlen($word);

        if ($len <= 3) {
            return $word;
        }

        // Identify consonants (exclude A, E, I, O, U)
        $consonants = [];
        for ($i = 0; $i < $len; $i++) {
            $char = $word[$i];
            if ($char >= 'A' && $char <= 'Z' && !in_array($char, ['A', 'E', 'I', 'O', 'U'])) {
                $consonants[] = $char;
            }
        }
        $unique_consonants = array_values(array_unique($consonants));

        $vowels = ['A', 'E', 'I', 'O', 'U'];
        $starts_with_vowel = in_array($word[0], $vowels);

        if ($starts_with_vowel) {
            // e.g. Aqidah -> A + Q + D = AQD
            if (count($unique_consonants) >= 2) {
                return $word[0] . $unique_consonants[0] . $unique_consonants[1];
            }
        } else {
            // e.g. Matematika -> M + T + K = MTK
            // e.g. Tematik -> T + M + K = TMK
            if (count($unique_consonants) >= 3) {
                return $unique_consonants[0] . $unique_consonants[1] . $unique_consonants[2];
            }
        }

        // Fallback: first 3 letters of the word
        return substr($word, 0, 3);
    } else {
        // --- Multi-word subject abbreviation logic ---
        $initials = '';
        foreach ($words as $w) {
            $w_clean = preg_replace('/[^A-Za-z0-9]/', '', $w);
            if (!empty($w_clean)) {
                $initials .= strtoupper($w_clean[0]);
            }
        }
        return (strlen($initials) >= 2) ? substr($initials, 0, 4) : $kode;
    }
}

