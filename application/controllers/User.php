<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class User extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->library('Dynamic_db'); // load dulu
		$this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

		$this->mustLogin();
		$this->AdminOrSuper();
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
	}

	public function index()
	{
		$data['judul'] = 'Data User';
		$data['menu'] = 'master';
		$data['sub'] = 'user';


		$this->load->view('admin/user', $data);
	}

	public function datatable()
	{
		// $result = $this->Model_mapel->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= BASE QUERY ================= */
		$this->db->from('user');

		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('username', $search)
				->or_like('pass_v', $search)
				->group_end();
		}

		$this->db->where_not_in('level', ['admin', 'super_admin']);
		$this->db->where('id_lembaga', $this->id_lembaga);

		/* ================= TOTAL ================= */
		$db_total = clone $this->db;
		$total = $db_total->count_all_results();

		/* ================= DATA ================= */
		$this->db->order_by($sortBy, $sortDir);
		$this->db->limit($perPage, $offset);

		$data = $this->db->get()->result_array();

		$result = [
			'data'      => $data,
			'total'     => $total,
			'page'      => $page,
			'perPage'   => $perPage,
			'lastPage'  => ceil($total / $perPage),
		];

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));
	}

	public function render()
	{
		$gurudata = $this->db->query("SELECT guru.* FROM registrasi JOIN guru ON registrasi.id_guru=guru.id_guru WHERE registrasi.id_lembaga = '$this->id_lembaga' AND satminkal = 1 ")->result();
		foreach ($gurudata as $guru) {
			$cek = $this->db->query("SELECT * FROM user WHERE id_guru = '$guru->id_guru' ")->row();
			$passs = generatePassword6();
			if (!$cek) {
				$userdata = [
					'id_user' => $this->uuid->v4(),
					'nama' => $guru->nama,
					'jabatan' => 'Guru',
					'username' => generateUsernameUnique($guru->nama),
					'password' => password_hash($passs, PASSWORD_BCRYPT),
					'pass_v' => $passs,
					'level' => 'guru',
					'id_guru' => $guru->id_guru,
					'id_lembaga' => $this->id_lembaga
				];
				$sql = $this->db->insert('user', $userdata);
				if ($sql) {
					echo json_encode(['status' => true]);
				} else {
					echo json_encode(['status' => false]);
				}
			} else {
				continue;
			}
		}
	}

	public function add()
	{
		$kode_mapel    = $this->input->post('kode_mapel', true);
		$nama         = $this->input->post('nama', true);

		$data = [
			'kode_mapel'    => $kode_mapel,
			'nama'         => $nama,
		];

		$sql = $this->model->tambah('mapel', $data);

		if ($sql) {
			$this->session->set_flashdata('ok', 'Data mapel berhasil ditambahkan.');
		} else {
			$this->session->set_flashdata('error', 'Data mapel gagal ditambahkan.');
		}
		redirect('mapel');
	}

	public function hapus()
	{
		$id = $this->input->get('id', true);
		$sql = $this->db->where('id_user', $id)->delete('user');
		if (!$sql) {
			$this->session->set_flashdata('error', 'Data mapel gagal dihapus.');
			redirect('mapel');
		} else {
			$this->session->set_flashdata('ok', 'Data mapel berhasil dihapus.');
			redirect('mapel');
		}
	}

	public function getById($id)
	{
		$data = $this->model->getBy('mapel', 'id_mapel', $id)->row_array();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function update($id)
	{
		$id_mapel    = $id;
		$kode_mapel    = $this->input->post('kode_mapel', true);
		$nama         = $this->input->post('nama', true);

		$data = [
			'kode_mapel'    => $kode_mapel,
			'nama'         => $nama,
		];

		$sql = $this->model->edit('mapel', 'id_mapel', $id_mapel, $data);

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data mapel gagal diupdate.');
			redirect('mapel');
		} else {
			$this->session->set_flashdata('ok', 'Data mapel berhasil diupdate.');
			redirect('mapel');
		}
	}
}
