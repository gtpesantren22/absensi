<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

        $this->iduser = $this->session->userdata('id_user');

        $this->mustLogin();
        $this->AdminOrSuper();

        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
        $this->id_lembaga = $usrdtl->id_lembaga;
    }

    public function index()
    {
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();
        $data['hideSidebar'] = true;
        $data['judul'] = 'Data Jadwal Pelajaran';
        $data['menu'] = 'jadwal';
        $data['sub'] = 'jadwal';

        $data['kelas'] = $this->db_active
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();
        $data['mapel'] = $this->db_active
            ->order_by('nama', 'ASC')
            ->get('mapel')
            ->result();
        $data['guru'] = $this->db
            ->select('guru.*')
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru=guru.id_guru')
            ->where('registrasi.id_lembaga', $usrdtl->id_lembaga)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result();


        $this->load->view('admin/jadwal', $data);
    }


    public function fetch_jadwal($day)
    {
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();

        $kelasList = $this->db_active
            // ->where('jenis', 'Utama')
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();

        $jadwalList = $this->db->query("
            SELECT 
                j.id_jadwal,
                j.id_kelas,
                j.id_mapel,
                j.id_guru,
                j.jam_dari,
                j.jam_sampai,
                g.kode_guru,
                g.warna
            FROM jadwal j
            LEFT JOIN guru g ON j.id_guru = g.id_guru
            WHERE j.hari = ? AND id_lembaga = ?
        ", [$day, $usrdtl->id_lembaga])->result_array();


        $mapelIds = array_unique(array_filter(array_column($jadwalList, 'id_mapel')));
        $mapelMap = [];
        if (!empty($mapelIds)) {
            $mapelList = $this->db_active
                ->where_in('id_mapel', $mapelIds)
                ->get('mapel')
                ->result_array();

            foreach ($mapelList as $m) {
                $mapelMap[$m['id_mapel']] = $m['kode_mapel'];
            }
        }


        $jadwalMap = [];

        foreach ($jadwalList as $j) {

            $kodeMapel = $mapelMap[$j['id_mapel']] ?? '-';

            for ($jam = (int)$j['jam_dari']; $jam <= (int)$j['jam_sampai']; $jam++) {
                $jadwalMap[$jam][$j['id_kelas']][] = [
                    'id_jadwal' => $j['id_jadwal'],
                    'mapel'     => $kodeMapel,
                    'guru'      => $j['kode_guru'] ?? '-',
                    'warna'     => $j['warna'] ?? '#000',
                ];
            }
        }

        // $jadwalMap[jam][id_kelas][] = jadwal;
        $totalJam = $this->model->getBy('setting', 'key', 'jml_jp')->row('isi');

        echo '<div class="relative overflow-x-auto">
            <table class="w-full min-w-[640px] border border-gray-300 dark:border-gray-700 text-sm">';

        /* ===== HEADER ATAS ===== */
        echo '<thead class="bg-gray-100 dark:bg-gray-800">';
        echo '<tr>';
        echo '<th class="border p-2">Jam / Kelas</th>';

        foreach ($kelasList as $kelas) {
            echo '<th class="border p-2 text-center">' . $kelas->nama . '</th>';
        }
        echo '</tr>';
        echo '</thead>';

        /* ===== BODY ===== */
        echo '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

        for ($jam = 1; $jam <= $totalJam; $jam++) {

            echo '<tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">';

            // Header samping (jam)
            echo '
                <th class="border p-2 bg-gray-50 dark:bg-gray-900 text-center font-medium">
                    Jam ke-' . $jam . '
                </th>
            ';

            foreach ($kelasList as $kelas) {

                echo '<td class="border p-1 align-top min-h-[70px]">';

                if (!empty($jadwalMap[$jam][$kelas->id_kelas])) {

                    foreach ($jadwalMap[$jam][$kelas->id_kelas] as $jj) {

                        $bg      = $jj['warna'] ?? '#000600';
                        $jid     = $jj['id_jadwal'] ?? '-';
                        $gurukd  = $jj['guru'] ?? '-';
                        $kdmapel = $jj['mapel'] ?? '-';


                        echo '
                            <div 
                                class="rounded p-1 mb-1 text-white item-jadwal text-md justify-items-center cursor-pointer fade-in"
                                style="background-color: ' . $bg . '"
                                data-jadwal-id="' . $jid . '">
                                <div class="font-medium">' . $gurukd . ' ' . $kdmapel . '</div>
                            </div>
                        ';
                    }
                }

                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table></div>';
    }

    public function add()
    {
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();

        $jam_dari = $this->input->post('jam_dari');
        $jam_sampai = $this->input->post('jam_sampai');

        $cekjam = $this->model->getBy('setting', 'key', 'jml_jp')->row();
        if ($jam_dari > $cekjam->isi || $jam_sampai > $cekjam->isi) {
            echo json_encode(['status' => 'error', 'message' => 'jam input melebihi. Max jam ke-' . $cekjam->isi]);
            exit;
        }
        $id_kelas = $this->input->post('id_kelas');
        $id_mapel = $this->input->post('id_mapel');
        $id_guru = $this->input->post('id_guru');

        $dtkelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();
        $dtmapel = $this->model->getBy('mapel', 'id_mapel', $id_mapel)->row();
        $dtguru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$id_guru' ")->row();
        $dtlembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$usrdtl->id_lembaga' ")->row();
        $idnew = $this->uuid->v4();
        $data = [
            'id_jadwal' => $idnew,
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_guru' => $id_guru,
            'jam_dari' => $jam_dari,
            'jam_sampai' => $jam_sampai,
            'id_lembaga' => $usrdtl->id_lembaga
        ];
        $dataDtl = [
            'id_jadwal' => $idnew,
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $dtkelas->nama,
            'id_mapel' => $dtmapel->nama,
            'id_guru' => $dtguru->nama,
            'jam_dari' => $jam_dari,
            'jam_sampai' => $jam_sampai,
            'id_lembaga' => $dtlembaga->nama
        ];

        $sql = $this->db->insert('jadwal', $data);
        $sql2 = $this->db->insert('jadwal_dtl', $dataDtl);
        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan jadwal. Silakan coba lagi.']);
        }
    }

    public function get_jadwal($id = null)
    {
        $jadwal = $this->db
            ->where('id_jadwal', $id)
            ->get('jadwal')
            ->row();

        echo json_encode($jadwal);
    }

    public function update()
    {
        $id_jadwal = $this->input->post('id_jadwal', TRUE);

        $id_kelas = $this->input->post('id_kelas');
        $id_mapel = $this->input->post('id_mapel');
        $id_guru = $this->input->post('id_guru');

        $dtkelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();
        $dtmapel = $this->model->getBy('mapel', 'id_mapel', $id_mapel)->row();
        $dtguru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$id_guru' ")->row();

        $data = [
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_guru' => $id_guru,
            'jam_dari' => $this->input->post('jam_dari', TRUE),
            'jam_sampai' => $this->input->post('jam_sampai', TRUE),
        ];

        $dataDtl = [
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $dtkelas->nama,
            'id_mapel' => $dtmapel->nama,
            'id_guru' => $dtguru->nama,
            'jam_dari' => $this->input->post('jam_dari'),
            'jam_sampai' => $this->input->post('jam_sampai'),
        ];

        $sql = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->update('jadwal', $data);
        $sql2 = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->update('jadwal_dtl', $dataDtl);

        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui jadwal. Silakan coba lagi.']);
        }
    }

    public function hapus()
    {
        $id_jadwal = $this->input->post('id', TRUE);

        $sql = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->delete('jadwal');
        $sql2 = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->delete('jadwal_dtl');

        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal. Silakan coba lagi.']);
        }
    }

    public function check_bentrok($day)
    {
        $bentrok = $this->cek_bentrok_hari($day);

        if (empty($bentrok)) {
            echo '<span class="ml-2 text-green-600 font-medium">Tidak ada bentrok jadwal.</span>';
        } else {
            echo '<div class="ml-2 text-red-600 dark:text-red-100 font-medium"><ul>';
            foreach ($bentrok as $b) {
                $dt = $this->db->where('id_jadwal', $b->id_jadwal)->get('jadwal_dtl')->row();
                echo "<li>Guru <strong>{$dt->id_guru}</strong> pada hari ini : <br>
                 => jam {$dt->jam_dari}-{$dt->jam_sampai} mengajar di kelas <strong>{$dt->id_kelas}</strong> ({$dt->id_mapel}) di lembaga <strong>{$dt->id_lembaga}</strong></li>";
            }
            echo '</ul></div>';
        }
    }

    public function cek_bentrok_hari($hari)
    {
        // 1️⃣ Ambil semua jadwal di hari tersebut
        $jadwal = $this->db
            ->where('hari', $hari)
            // ->where('id_lembaga', $this->id_lembaga)
            ->order_by('id_guru')
            ->order_by('jam_dari')
            ->get('jadwal')
            ->result();

        if (count($jadwal) === 0) {
            echo json_encode([
                'status' => true,
                'message' => 'Tidak ada jadwal pada hari ini',
                'data' => []
            ]);
            return;
        }

        // 2️⃣ Kelompokkan jadwal per guru
        $groupGuru = [];
        foreach ($jadwal as $j) {
            $groupGuru[$j->id_guru][] = $j;
        }

        // 3️⃣ Cari bentrok
        $bentrok = [];

        foreach ($groupGuru as $id_guru => $list) {

            $total = count($list);

            for ($i = 0; $i < $total; $i++) {
                for ($j = $i + 1; $j < $total; $j++) {

                    $a = $list[$i];
                    $b = $list[$j];

                    // OVERLAP CHECK
                    if (
                        $a->jam_dari <= $b->jam_sampai &&
                        $a->jam_sampai >= $b->jam_dari
                    ) {

                        // simpan dua-duanya
                        $bentrok[$a->id_jadwal] = $a;
                        $bentrok[$b->id_jadwal] = $b;
                    }
                }
            }
        }

        $id_lembaga_login = $this->id_lembaga;
        $adaBentrokLembaga = false;
        foreach ($bentrok as $row) {
            if ($row->id_lembaga === $id_lembaga_login) {
                $adaBentrokLembaga = true;
                break;
            }
        }

        // ❌ Kalau TIDAK ADA bentrok milik lembaga login
        if (!$adaBentrokLembaga) {
            $bentrok = [];
        }

        // 4️⃣ Output
        // if (empty($bentrok)) {
        //     $this->output
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode([
        //             'status' => true,
        //             'message' => 'Tidak ada jadwal bentrok',
        //             'data' => []
        //         ]));
        //     return;
        // }

        // $this->output
        //     ->set_content_type('application/json')
        //     ->set_output(json_encode([
        //         'status' => false,
        //         'message' => 'Jadwal bentrok ditemukan',
        //         'total' => count($bentrok),
        //         'data' => array_values($bentrok)
        //     ]));

        // OtputOk
        if (empty($bentrok)) {
            $data = [];
        } else {
            $data = array_values($bentrok);
        }
        return $data;
    }

    public function cek_bentrok_hari_old($hari)
    {
        $jadwal = $this->db
            ->select('
            j.id_jadwal,
            j.id_guru,
            g.nama AS guru,
            l.nama AS lembaga,
            j.id_kelas,
            j.id_mapel,
            j.hari,
            j.jam_dari,
            j.jam_sampai
        ')
            ->from('jadwal j')
            ->join('guru g', 'g.id_guru = j.id_guru')
            ->join('lembaga l', 'l.id_lembaga = j.id_lembaga')
            ->where('j.hari', $hari)
            ->order_by('j.id_guru, j.jam_dari')
            ->get()
            ->result();

        if (empty($jadwal)) {
            return [];
        }

        // ===============================
        // MAP KELAS
        // ===============================
        $kelasIds = array_unique(array_column($jadwal, 'id_kelas'));
        $kelasMap = [];

        if ($kelasIds) {
            $kelasList = $this->db_active
                ->select('id_kelas, nama')
                ->where_in('id_kelas', $kelasIds)
                ->get('kelas')
                ->result_array();

            foreach ($kelasList as $k) {
                $kelasMap[$k['id_kelas']] = $k['nama'];
            }
        }

        // ===============================
        // MAP MAPEL
        // ===============================
        $mapelIds = array_unique(array_column($jadwal, 'id_mapel'));
        $mapelMap = [];

        if ($mapelIds) {
            $mapelList = $this->db_active
                ->select('id_mapel, nama')
                ->where_in('id_mapel', $mapelIds)
                ->get('mapel')
                ->result_array();

            foreach ($mapelList as $m) {
                $mapelMap[$m['id_mapel']] = $m['nama'];
            }
        }

        // ===============================
        // GROUP PER GURU + HARI
        // ===============================
        $grouped = [];

        foreach ($jadwal as $j) {
            // CAST KE INTEGER (WAJIB!)
            $j->jam_dari   = (int) $j->jam_dari;
            $j->jam_sampai = (int) $j->jam_sampai;

            $key = $j->id_guru . '-' . $j->hari;
            $grouped[$key][] = $j;
        }

        // ===============================
        // CEK BENTROK
        // ===============================
        $bentrok = [];

        foreach ($grouped as $items) {

            usort($items, function ($a, $b) {
                return $a->jam_dari <=> $b->jam_dari;
            });

            $count = count($items);

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {

                    // sudah aman → break
                    if ($items[$i]->jam_sampai < $items[$j]->jam_dari) {
                        break;
                    }

                    // OVERLAP
                    if (
                        $items[$i]->jam_dari <= $items[$j]->jam_sampai &&
                        $items[$i]->jam_sampai >= $items[$j]->jam_dari
                    ) {
                        $bentrok[] = [
                            'guru'     => $items[$i]->guru,
                            'lembaga'  => $items[$i]->lembaga,
                            'hari'     => $items[$i]->hari,
                            'jam'      => 'Jam ' .
                                max($items[$i]->jam_dari, $items[$j]->jam_dari) .
                                '–' .
                                min($items[$i]->jam_sampai, $items[$j]->jam_sampai),
                            'kelas1'   => $kelasMap[$items[$i]->id_kelas] ?? '-',
                            'mapel1'   => $mapelMap[$items[$i]->id_mapel] ?? '-',
                            'kelas2'   => $kelasMap[$items[$j]->id_kelas] ?? '-',
                            'mapel2'   => $mapelMap[$items[$j]->id_mapel] ?? '-',
                        ];
                    }
                }
            }
        }

        // 🔥 INI YANG TADI HILANG
        return $bentrok;
    }
}
