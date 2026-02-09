<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Screen extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

    }

    public function apel_guru($tgl, $idl)
    {
        $hari_ini = $tgl;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->model->getBy('apel_guru', 'tanggal', $hari_ini)->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        $dataJadwal = $this->db_active->query("SELECT * FROM apel_guru WHERE tanggal = '$tglni' ");
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();
        $data['hadir'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'hadir' ")->row();
        $data['izin'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'izin' ")->row();
        $data['alpha'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'alpha' ")->row();

        $data['data'] = $dataJadwal->result();
        $data['tanggal'] = $tglni;


        $this->load->view('absensi/screen_apel_guru', $data);
    }
}
