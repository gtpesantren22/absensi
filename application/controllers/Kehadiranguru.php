<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kehadiranguru extends MY_Controller
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


        // ================= TOTAL =================
        $this->db_active->from('kehadiran_guru');

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
            kehadiran_guru.tanggal,
            COUNT(kehadiran_guru.id_guru) AS jumlah_guru
        ');

        $this->db_active->from('kehadiran_guru');

        if (!empty($search)) {
            $this->db_active->group_start()
                ->like('kehadiran_guru.tanggal', $search)
                ->group_end();
        }

        $this->db_active->group_by('kehadiran_guru.tanggal');
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
            ->join('guru', 'registrasi.id_guru=guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->where('registrasi.satminkal', 1)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result_array();

        $hadirList = $this->db_active
            ->select('id_guru, ket')
            ->from('kehadiran_guru')
            ->where('tanggal', $tanggal)
            ->get()
            ->result_array();
        $hadirMap = [];
        foreach ($hadirList as $h) {
            $hadirMap[$h['id_guru']] = $h['ket'];
        }
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
                    'waktu' => date('H:i:s')
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
            'waktu' => date('H:i:s')
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

    public function screenhadir($tgl) {}
}
