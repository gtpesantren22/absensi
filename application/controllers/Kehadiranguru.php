<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kehadiranguru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->mustLogin();
        $this->onlyPiket();

        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }

    public function index()
    {
        $data['title'] = "Absensi Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "kehadiranguru";


        $this->load->view('absensi/kehadiran_guru', $data);
    }

    public function kehadiranData()
    {
        // $result = $this->Model_mapel->getData($params);
        $search   = $this->input->get('search') ?? '';
        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
        $sortBy   = $this->input->get('sortBy') ?? 'tanggal';
        $sortDir  = strtoupper($this->input->get('sortDir') ?? 'DESC');

        $offset = ($page - 1) * $perPage;


        /* ================= TOTAL ================= */
        $this->db->from('kehadiran_guru');

        $this->db->where('kehadiran_guru.id_lembaga', $this->id_lembaga);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('kehadiran_guru.tanggal', $search)
                ->group_end();
        }

        // count DISTINCT tanggal
        $this->db->select('COUNT(DISTINCT kehadiran_guru.tanggal) AS total');

        $total = $this->db->get()->row()->total;


        /* ================= DATA ================= */
        $this->db->select('
            kehadiran_guru.tanggal,
            COUNT(kehadiran_guru.id_guru) AS jumlah_guru
        ');

        $this->db->from('kehadiran_guru');

        $this->db->where('kehadiran_guru.id_lembaga', $this->id_lembaga);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('kehadiran_guru.tanggal', $search)
                ->group_end();
        }

        $this->db->group_by('kehadiran_guru.tanggal');
        $this->db->order_by($sortBy, $sortDir);
        $this->db->limit($perPage, $offset);

        $data = $this->db->get()->result_array();

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


    public function kehadiran_add($tgl = null)
    {
        $data['title'] = "Tambah Absensi Kehadiran Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "kehadiranguru";

        if ($tgl == null) {
            $tanggal = date('Y-m-d');
            $harini = date('l');
        } else {
            $tanggal = $tgl;
            $harini = date('l', strtotime($tanggal));
        }
        $data['tanggal'] = $tanggal;

        $guruList = $this->db
            ->select('guru.id_guru, guru.nama')
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru = guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->where('registrasi.satminkal', 1)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result_array();

        $hadirList = $this->db
            ->select('id_guru, ket')
            ->from('kehadiran_guru')
            ->where('tanggal', $tanggal)
            ->where('id_lembaga', $this->id_lembaga)
            ->get()
            ->result_array();

        /* ===== Mapping kehadiran ===== */
        $hadirMap = array_column($hadirList, 'ket', 'id_guru');

        /* ===== Gabungkan data ===== */
        $datakirim = [];

        foreach ($guruList as $g) {
            $datakirim[] = [
                'id_guru' => $g['id_guru'],
                'nama'    => $g['nama'],
                'ket'     => $hadirMap[$g['id_guru']] ?? '-',
            ];
        }

        $data['data'] = $datakirim;


        $this->load->view('absensi/kehadiran_guru_add', $data);
    }

    public function saveHadirGuru1()
    {
        $data = $this->input->post('data', true);
        $tanggal = $this->input->post('tanggal', true);
        if (!empty($data)) {
            foreach ($data as $item) {
                $cek = $this->model->getBy2('kehadiran_guru', 'tanggal', $tanggal, 'id_guru', $item['id_guru'])->row();
                $dtsm = [
                    'tanggal' => $tanggal,
                    'id_guru' => $item['id_guru'],
                    'ket' => isset($item['ket']) ? $item['ket'] : '',
                    'waktu' => date('H:i:s'),
                    'id_lembaga' => $this->id_lembaga
                ];
                if (!$cek) {
                    $sql = $this->model->tambah('kehadiran_guru', $dtsm);
                } else {
                    $sql = $this->model->edit2('kehadiran_guru',  'tanggal', $tanggal, 'id_guru', $item['id_guru'], $dtsm);
                }
            }
            if ($sql) {
                $this->session->set_flashdata('ok', 'Input Kehadiran Berhasil');
                redirect('kehadiranguru');
            } else {
                $this->session->set_flashdata('error', 'Input Kehadiran Gagal');
                redirect('kehadiranguru');
            }
        }
    }

    public function saveHadirGuru()
    {
        $id_guru = $this->input->post('id', TRUE);
        $ket = $this->input->post('value', TRUE);
        $tanggal = $this->input->post('tanggal', TRUE);

        $cek = $this->model->getBy2('kehadiran_guru', 'tanggal', $tanggal, 'id_guru', $id_guru)->row();
        $dtsm = [
            'tanggal' => $tanggal,
            'id_guru' => $id_guru,
            'ket' => isset($ket) ? $ket : '',
            'waktu' => date('H:i:s'),
            'id_lembaga' => $this->id_lembaga
        ];
        if (!$cek) {
            $sql = $this->model->tambah('kehadiran_guru', $dtsm);
        } else {
            $sql = $this->model->edit2('kehadiran_guru',  'tanggal', $tanggal, 'id_guru', $id_guru, $dtsm);
        }

        if ($sql) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function hapusKehadiran()
    {
        $id = $this->input->post('id', true);
        $this->model->hapus3('kehadiran_guru', 'tanggal', $id, 'id_guru !=', '0', 'ket !=', '');
        echo json_encode(['success' => true]);
    }

    public function screenhadir($tgl)
    {
        $lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga' ")->row();
        $nick = $lembaga->nickname;
        $curl2 = curl_init();

        curl_setopt_array(
            $curl2,
            array(
                CURLOPT_URL => 'http://31.97.179.141:3100/capture?url=' . base_url() . 'screen/kehadiran_guru/' . $tgl . '/' . $lembaga->id_lembaga . '&filename=KEHADIRAN-GURU-' . $nick . '_' . $tgl,
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
            $fileUrl = "http://31.97.179.141:3100/capture-result/KEHADIRAN-GURU-$nick"  . "_$tgl.png";
            $fileName = "KEHADIRAN-GURU-$nick"  . "_$tgl.png";

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
