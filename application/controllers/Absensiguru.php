<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absensiguru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

        $this->mustLogin();
        $this->onlyPiket();

        $this->iduser = $this->session->userdata('id_user');
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
        $this->id_lembaga = $usrdtl->id_lembaga;
    }

    public function pembiasaan()
    {
        $data['title'] = "Absensi Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "absensiguru_pembiasaan";

        // $data['absensiguru'] = $this->model->getAbsensiGuru($this->db_active)->result();

        $this->load->view('absensi/pembiasaan_guru', $data);
    }

    public function pembiasaanData()
    {
        // $result = $this->Model_mapel->getData($params);
        $search   = $this->input->get('search') ?? '';
        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
        $sortBy   = $this->input->get('sortBy') ?? 'tanggal';
        $sortDir  = strtoupper($this->input->get('sortDir') ?? 'DESC');

        $offset = ($page - 1) * $perPage;


        // ================= TOTAL =================
        $this->db_active->from('apel_guru');

        if (!empty($search)) {
            $this->db_active->group_start()
                ->like('tanggal', $search)
                ->group_end();
        }

        // count DISTINCT tanggal
        $this->db_active->select('COUNT(DISTINCT tanggal) AS total');
        $total = $this->db_active->get()->row()->total;


        // ================= DATA =================
        $this->db_active->select('
            apel_guru.tanggal,
            COUNT(apel_guru.id_guru) AS jumlah_guru
        ');

        $this->db_active->from('apel_guru');

        if (!empty($search)) {
            $this->db_active->group_start()
                ->like('apel_guru.tanggal', $search)
                ->group_end();
        }

        $this->db_active->group_by('apel_guru.tanggal');
        $this->db_active->order_by($sortBy, $sortDir);
        $this->db_active->limit($perPage, $offset);

        $data = $this->db_active->get()->result_array();

        $result = [
            'data'      => $data,
            'total'     => (int) $total,
            'page'      => (int) $page,
            'perPage'   => (int) $perPage,
            'lastPage'  => ceil($total / $perPage),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function set_pembiasaan()
    {
        $data['title'] = "Setting Pembiasaan Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "absensiguru_pembiasaan";

        $guruList = $this->db
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru=guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result_array();

        $apelList = $this->db_active
            ->select('id_guru, GROUP_CONCAT(TRIM(hari) ORDER BY hari SEPARATOR ",") AS daftar_hari')
            ->from('apel_sett')
            ->group_by('id_guru')
            ->get()
            ->result_array();

        $apelMap = [];

        foreach ($apelList as $a) {
            $apelMap[$a['id_guru']] = $a['daftar_hari'];
        }

        $datakirim = [];

        foreach ($guruList as $g) {
            $datakirim[] = [
                'id_guru'     => $g['id_guru'],
                'nama'        => $g['nama'],
                'daftar_hari' => $apelMap[$g['id_guru']] ?? '0,0,0',
            ];
        }

        $data['guru'] = $datakirim;



        $this->load->view('absensi/set_pembiasaan_guru', $data);
    }

    public function setPembiasaan()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $id_guru = $input['id_guru'] ?? null;
        $hari = $input['hari'] ?? null;
        $status = $input['status'] ?? null;

        if ($status == 1) {
            // Cek apakah data sudah ada
            $this->db_active->where('id_guru', $id_guru);
            $this->db_active->where('hari', $hari);
            $query = $this->db_active->get('apel_sett');

            if ($query->num_rows() < 1) {
                // Insert new record
                $this->db_active->insert('apel_sett', [
                    'id_guru' => $id_guru,
                    'hari' => $hari
                ]);
            }

            echo json_encode(['success' => true]);
        } else {
            // Hapus record jika ada
            $this->db_active->where('id_guru', $id_guru);
            $this->db_active->where('hari', $hari);
            $this->db_active->delete('apel_sett');

            echo json_encode(['success' => true]);
        }
    }

    public function pembiasaan_add($tgl = null)
    {
        $data['title'] = "Tambah Absensi Pembiasaan Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "absensiguru_pembiasaan";

        if ($tgl == null) {
            $tanggal = date('Y-m-d');
            $harini = date('l');
        } else {
            $tanggal = $tgl;
            $harini = date('l', strtotime($tanggal));
        }
        $data['tanggal'] = $tanggal;

        $apelSett = $this->db_active
            ->select('id_guru')
            ->from('apel_sett')
            ->where('hari', $harini)
            ->get()
            ->result_array();

        $guruIds = array_column($apelSett, 'id_guru');
        if (empty($guruIds)) {
            $data['data'] = [];
            return;
        }
        $guruList = $this->db
            ->select('id_guru, nama')
            ->where_in('id_guru', $guruIds)
            ->order_by('nama', 'ASC')
            ->get('guru')
            ->result_array();

        $guruMap = [];
        foreach ($guruList as $g) {
            $guruMap[$g['id_guru']] = $g['nama'];
        }
        $apelGuru = $this->db_active
            ->select('id_guru, ket')
            ->from('apel_guru')
            ->where('tanggal', $tanggal)
            ->where_in('id_guru', $guruIds)
            ->get()
            ->result_array();

        $apelMap = [];
        foreach ($apelGuru as $a) {
            $apelMap[$a['id_guru']] = $a['ket'];
        }
        $datakirim = [];

        foreach ($guruIds as $idGuru) {
            $datakirim[] = [
                'id_guru' => $idGuru,
                'nama'    => $guruMap[$idGuru] ?? '-',
                'ket'     => $apelMap[$idGuru] ?? '-',
            ];
        }

        $data['data'] = $datakirim;
        $this->load->view('absensi/pembiasaan_guru_add', $data);
    }

    public function saveApelGuru()
    {
        $data = $this->input->post('data', true);
        $tanggal = $this->input->post('tanggal', true);
        if (!empty($data)) {
            foreach ($data as $item) {
                $cek = $this->model->getBy2('apel_guru', 'tanggal', $tanggal, 'id_guru', $item['id_guru'])->row();
                $dtsm = [
                    'tanggal' => $tanggal,
                    'id_guru' => $item['id_guru'],
                    'ket' => isset($item['ket']) ? $item['ket'] : '',
                ];
                if (!$cek) {
                    $sql = $this->model->tambah('apel_guru', $dtsm);
                    if ($item['ket'] == 'hadir') {
                        // juga simpan ke tabel kehadiran_guru
                        $this->model->tambah('kehadiran_guru', [
                            'tanggal' => $tanggal,
                            'id_guru' => $item['id_guru'],
                            'ket' => 'hadir',
                        ]);
                    }
                } else {
                    $sql = $this->model->edit2('apel_guru',  'tanggal', $tanggal, 'id_guru', $item['id_guru'], $dtsm);
                }
            }
            if ($sql) {
                $this->session->set_flashdata('ok', 'Input Absen Berhasil');
                redirect('absensiguru/pembiasaan');
            } else {
                $this->session->set_flashdata('error', 'Input Absen Gagal');
                redirect('absensiguru/pembiasaan');
            }
        }
    }

    public function hapusPembiasaan()
    {
        $id = $this->input->post('id', true);
        $this->model->hapus3('apel_guru', 'tanggal', $id, 'id_guru !=', '0', 'ket !=', '');
        echo json_encode(['success' => true]);
    }

    public function screenApelGuru($tgl)
    {
        $lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga' ")->row();
        $nick = $lembaga->nickname;
        $curl2 = curl_init();

        curl_setopt_array(
            $curl2,
            array(
                CURLOPT_URL => 'http://31.97.179.141:3100/capture?url=' . base_url() . 'screen/apel_guru/' . $tgl . '/' . $lembaga->id_lembaga . '&filename=APEL-GURU-' . $nick . '_' . $tgl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET'
            )
        );

        $response = curl_exec($curl2);
        curl_close($curl2);

        $result = json_decode($response, true);

        // === VALIDASI RESPONSE ===
        if (!$result || !isset($result['status'])) {
            show_error('Response API tidak valid');
        }

        // === JIKA STATUS TRUE ===
        if ($result['status'] === true) {

            // URL FILE SUDAH DITENTUKAN
            $fileUrl = "http://31.97.179.141:3100/capture-result/APEL-GURU-$nick"  . "_$tgl.png";
            $fileName = "APEL-GURU-$nick"  . "_$tgl.png";

            // === AMBIL FILE DARI URL ===
            $ch = curl_init($fileUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 60,
            ]);

            $fileData = curl_exec($ch);

            if ($fileData === false) {
                $error = curl_error($ch);
                curl_close($ch);
                show_error('Gagal download file: ' . $error);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                show_error('Gagal download file. HTTP Code: ' . $httpCode);
            }

            // === FORCE DOWNLOAD ===
            header('Content-Description: File Transfer');
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . strlen($fileData));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            echo $fileData;
            exit;
        } else {
            show_error('Status false, download dibatalkan');
        }
    }
}
