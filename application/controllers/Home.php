<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->library('Dynamic_db'); // load dulu
		$this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()
		$this->mustLogin();
		$this->level = $this->session->userdata('level');
		$this->iduser = $this->session->userdata('id_user');
	}

	public function index()
	{
		$usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();

		if ($this->level === 'admin') {
			$data['menu'] = 'home';
			$data['sub'] = 'dashboard';

			$data['jumlah_guru'] = $this->db->query("SELECT registrasi.id_guru FROM registrasi JOIN guru ON registrasi.id_guru=guru.id_guru WHERE registrasi.id_lembaga = '$usrdtl->id_lembaga'")->num_rows();
			$data['jumlah_siswa'] = $this->db->query("SELECT registrasi_siswa.id_siswa FROM registrasi_siswa JOIN siswa ON registrasi_siswa.id_siswa=siswa.id_siswa WHERE registrasi_siswa.id_lembaga = '$usrdtl->id_lembaga'")->num_rows();
			$data['jumlah_kelas'] = $this->db_active->count_all('kelas');
			$data['jumlah_jadwal'] = $this->db->query("SELECT hari FROM jadwal WHERE id_lembaga = '$usrdtl->id_lembaga'")->num_rows();

			$data['sekolah'] = $this->db->query("SELECT s.nama, s.alamat, s.nickname FROM user u JOIN lembaga s ON u.id_lembaga=s.id_lembaga WHERE id_user = '$this->iduser' ")->row();
			$idguru = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();

			$data['idguru'] = $idguru->id_guru;
			$data['hadir'] = $this->model->getBy2('kehadiran_guru', 'id_guru', $idguru->id_guru, 'tanggal', date('Y-m-d'))->row();

			$this->load->view('admin/home', $data);
		} else if ($this->level === 'super_admin') {
			$data['menu'] = 'home';
			$data['sub'] = 'dashboard';

			$data['jumlah_guru'] = $this->db->count_all('guru');
			$data['jumlah_siswa'] = $this->db->count_all('siswa');
			$data['jumlah_kelas'] = $this->db_active->count_all('kelas');
			$data['jumlah_jadwal'] = $this->db->count_all('jadwal');

			$idguru = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
			$data['hadir'] = $this->model->getBy2('kehadiran_guru', 'id_guru', $idguru->id_guru, 'tanggal', date('Y-m-d'))->row();
			$data['idguru'] = $idguru->id_guru;

			$data['sekolah'] = $this->db->query("SELECT s.nama, s.alamat, s.nickname FROM user u JOIN lembaga s ON u.id_lembaga=s.id_lembaga WHERE id_user = '$this->iduser' ")->row();

			$this->load->view('admin/home', $data);
		} else {
			$data['menu'] = 'home';
			$data['sub'] = 'dashboard';
			$data['days'] = date('l');

			$idguru = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();

			$data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idguru->id_lembaga' ")->row();
			$data['guru'] = $this->db->query("SELECT * FROM guru WHERE id_guru = '$idguru->id_guru' ")->row();
			$data['idguru'] = $idguru->id_guru;

			$data['hadir'] = $this->model->getBy2('kehadiran_guru', 'id_guru', $idguru->id_guru, 'tanggal', date('Y-m-d'))->row();

			$data['lmb'] =  $this->db
				->select('
					j.id_lembaga,
					l.nama AS nama_lembaga,
					MIN(j.jam_dari) AS jam_pertama
				')
				->from('jadwal j')
				->join('lembaga l', 'l.id_lembaga = j.id_lembaga')
				->where('j.hari', $data['days'])
				->where('j.id_guru', $idguru->id_guru)
				->group_by('j.id_lembaga, l.nama')
				->order_by('jam_pertama', 'ASC')
				->get()
				->result();
			$data['user'] = $idguru;

			$this->load->view('guru/home', $data);
		}
	}
}
