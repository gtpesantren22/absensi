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
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();
        $this->id_lembaga = $usrdtl->id_lembaga;
    }

    public function index()
    {
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
            ->where('registrasi.id_lembaga', $this->id_lembaga)
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
        ", [$day, $this->id_lembaga])->result_array();


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
        $dtlembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga' ")->row();
        $idnew = $this->uuid->v4();
        $data = [
            'id_jadwal' => $idnew,
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_guru' => $id_guru,
            'jam_dari' => $jam_dari,
            'jam_sampai' => $jam_sampai,
            'id_lembaga' => $this->id_lembaga
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

            echo '
                <span class="ml-2 inline-flex items-center gap-2 
                    text-green-700 dark:text-green-400 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tidak ada bentrok jadwal
                </span>';
        } else {

            echo '
                <div class="ml-2 mt-2">
                    <ul class="space-y-2">';

            foreach ($bentrok as $b) {

                $dt = $this->db
                    ->query("SELECT * FROM jadwal_dtl WHERE id_jadwal = ?", [$b->id_jadwal])
                    ->row();

                if (!$dt) continue;
                if ($b->id_lembaga === $this->id_lembaga) {
                    echo '
                    <li class="
                            px-3 py-2
                            rounded-lg border
                            border-red-200 dark:border-red-800
                            bg-red-50 dark:bg-red-900/20
                            text-red-800 dark:text-red-800
                            text-sm
                            flex items-center justify-between
                            gap-3
                            whitespace-nowrap
                        ">
                            <!-- Info Guru -->
                            <span class="truncate font-medium">
                                Guru <strong>' . $dt->id_guru . '</strong>
                            </span>

                            <!-- Badge -->
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="
                                    text-xs px-2 py-0.5 rounded-full
                                    bg-red-100 dark:bg-red-800
                                    text-red-700 dark:text-red-200
                                    font-semibold
                                ">
                                    Bentrok
                                </span>

                                <span 
                                onclick="cekBentrokOne(\'' . $dt->id_jadwal . '\')"
                                class="cursor-pointer
                                    text-xs px-2 py-0.5 rounded-full
                                    bg-blue-100 dark:bg-blue-800
                                    text-blue-700 dark:text-blue-200
                                    font-semibold
                                ">
                                    Cek
                                </span>
                            </div>
                        </li>    
                    ';
                }
            }

            echo '
                                </ul>
                                </div>';
        }
    }


    // Jam <strong>' . $dt->jam_dari . '-' . $dt->jam_sampai . '</strong> • 
    //                             Kelas <strong>' . $dt->id_kelas . '</strong> 
    //                             <span class="opacity-70">(' . $dt->id_mapel . ')</span> • 
    //                             Lembaga <strong>' . $dt->id_lembaga . '</strong>

    public function cek_bentrok_hari($hari)
    {
        // 1️⃣ Ambil semua jadwal di hari tersebut
        $jadwal = $this->db
            ->where('hari', $hari)
            ->order_by('id_guru')
            ->order_by('jam_dari')
            ->get('jadwal')
            ->result();

        if (count($jadwal) === 0) {
            return $data = [];
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

        if (empty($bentrok)) {
            $data = [];
        } else {
            $data = array_values($bentrok);
        }
        return $data;
    }

    public function cek_bentrok_one()
    {
        $id_jadwal = $this->input->post('id_jadwal');

        // Jadwal acuan
        $jadwal = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->get('jadwal')
            ->row();
        $jadwalDt = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->get('jadwal_dtl')
            ->row();

        if (!$jadwal) {
            echo json_encode(['status' => false]);
            return;
        }

        // Jadwal bentrok
        $bentrok = $this->db
            ->select('jadwal_dtl.*')
            ->join('jadwal_dtl', 'jadwal.id_jadwal=jadwal_dtl.id_jadwal')
            ->where('jadwal.hari', $jadwal->hari)
            ->where('jadwal.id_guru', $jadwal->id_guru)
            ->where('jadwal.id_jadwal !=', $id_jadwal)
            ->where('jadwal.jam_dari <=', $jadwal->jam_sampai)
            ->where('jadwal.jam_sampai >=', $jadwal->jam_dari)
            ->get('jadwal')
            ->result();

        echo json_encode([
            'status' => true,
            'guru' => $jadwalDt->id_guru,
            'jadwal' => $jadwalDt,
            'bentrok' => $bentrok
        ]);
    }
}
