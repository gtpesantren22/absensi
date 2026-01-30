<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Piket extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

        $this->mustLogin();
        $this->onlyAdminSuper();

        $this->iduser = $this->session->userdata('id_user');
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
        $this->id_lembaga = $usrdtl->id_lembaga;
    }

    public function index()
    {
        $data['title'] = "Data Guru Piket";
        $data['menu'] = "jadwal";
        $data['sub'] = "piket";

        $guruList = $this->db
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru=guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result_array();

        $apelList = $this->db_active
            ->select('id_guru, GROUP_CONCAT(hari ORDER BY hari SEPARATOR ",") AS daftar_hari')
            ->from('piket')
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



        $this->load->view('admin/set_piket', $data);
    }

    public function setPiket()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $id_guru = $input['id_guru'] ?? null;
        $hari = $input['hari'] ?? null;
        $status = $input['status'] ?? null;

        if ($status == 1) {
            // Cek apakah data sudah ada
            $this->db_active->where('id_guru', $id_guru);
            $this->db_active->where('hari', $hari);
            $query = $this->db_active->get('piket');

            if ($query->num_rows() < 1) {
                // Insert new record
                $this->db_active->insert('piket', [
                    'id_guru' => $id_guru,
                    'hari' => $hari
                ]);
            }

            echo json_encode(['success' => true]);
        } else {
            // Hapus record jika ada
            $this->db_active->where('id_guru', $id_guru);
            $this->db_active->where('hari', $hari);
            $this->db_active->delete('piket');

            echo json_encode(['success' => true]);
        }
    }
}
