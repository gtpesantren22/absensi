<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Piket extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->mustLogin();
        $this->AdminOrSuper();

        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }

    public function index()
    {
        $data['title'] = "Data Guru Piket";
        $data['menu'] = "jadwal";
        $data['sub'] = "piket";

        $guruList = $this->db
            ->select('guru.id_guru, guru.nama')
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru = guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result_array();

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $apelList = $this->db
            ->select('id_guru, GROUP_CONCAT(TRIM(hari) ORDER BY hari SEPARATOR ",") AS daftar_hari')
            ->from('piket')
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_semester', $id_semester_aktif)
            ->group_by('id_guru')
            ->get()
            ->result_array();

        /* ===== Mapping piket ===== */
        $apelMap = array_column($apelList, 'daftar_hari', 'id_guru');

        /* ===== Gabungkan data ===== */
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

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        if ($status == 1) {
            // Cek apakah data sudah ada
            $this->db->where('id_guru', $id_guru);
            $this->db->where('hari', $hari);
            $this->db->where('id_lembaga', $this->id_lembaga);
            $this->db->where('id_semester', $id_semester_aktif);
            $query = $this->db->get('piket');

            if ($query->num_rows() < 1) {
                // Insert new record
                $this->db->insert('piket', [
                    'id_guru' => $id_guru,
                    'hari' => $hari,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ]);
            }

            echo json_encode(['success' => true]);
        } else {
            // Hapus record jika ada
            $this->db->where('id_guru', $id_guru);
            $this->db->where('hari', $hari);
            $this->db->where('id_lembaga', $this->id_lembaga);
            $this->db->where('id_semester', $id_semester_aktif);
            $this->db->delete('piket');

            echo json_encode(['success' => true]);
        }
    }
}
