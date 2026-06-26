<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mapel extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');

		$this->mustLogin();
		$this->AdminOrSuper();
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');

		// Self-healing database setups
		$this->_ensure_master_mapel_table();
	}

	private function _ensure_master_mapel_table()
	{
		if (!$this->db->table_exists('master_mapel')) {
			$this->db->query("CREATE TABLE `master_mapel` (
				`id_master_mapel` INT AUTO_INCREMENT PRIMARY KEY,
				`kode_mapel` VARCHAR(50) NOT NULL,
				`nama` VARCHAR(100) NOT NULL,
				`jenis_lembaga` TEXT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		} else {
			// Drop unique key if it exists to prevent duplicate code errors
			$query = $this->db->query("SHOW KEYS FROM `master_mapel` WHERE Key_name='unique_kode'");
			if ($query->num_rows() > 0) {
				$this->db->query("ALTER TABLE `master_mapel` DROP INDEX `unique_kode` ");
			}
		}
		if (!$this->db->field_exists('id_master_mapel', 'mapel')) {
			$this->db->query("ALTER TABLE `mapel` ADD COLUMN `id_master_mapel` INT NULL;");
		}
	}

	public function index()
	{
		$data['judul'] = 'Data Mapel';
		$data['menu'] = 'master';
		$data['sub'] = 'mapel';

		$this->load->view('admin/mapel', $data);
	}

	public function datatable()
	{
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;
		$level = $this->session->userdata('level');

		if ($level === 'super_admin') {
			/* ================= SUPER ADMIN: MASTER MAPEL ================= */
			$this->db->from('master_mapel');
			if (!empty($search)) {
				$this->db->group_start()
					->like('nama', $search)
					->or_like('kode_mapel', $search)
					->group_end();
			}

			$total = $this->db->count_all_results('', false);

			$this->db->select('id_master_mapel as id_mapel, kode_mapel, nama, jenis_lembaga');
			$this->db->order_by($sortBy, $sortDir);
			$this->db->limit($perPage, $offset);
			$data = $this->db->get()->result_array();
		} else {
			/* ================= SCHOOL ADMIN: INSTITUTION MAPEL ================= */
			$this->db->from('mapel');
			if (!empty($search)) {
				$this->db->group_start()
					->like('nama', $search)
					->or_like('kode_mapel', $search)
					->group_end();
			}

			$this->db->where('id_lembaga', $this->id_lembaga);
			$total = $this->db->count_all_results('', false);

			$this->db->order_by($sortBy, $sortDir);
			$this->db->limit($perPage, $offset);
			$this->db->where('id_lembaga', $this->id_lembaga);
			$data = $this->db->get()->result_array();
		}

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

	public function add()
	{
		$level = $this->session->userdata('level');

		if ($level === 'super_admin') {
			// Super admin inserts into master_mapel
			$kode_mapel = $this->input->post('kode_mapel', true);
			$nama       = $this->input->post('nama', true);

			if (empty(trim($kode_mapel))) {
				$kode_mapel = generate_kode_mapel($nama);
			}

			$data = [
				'kode_mapel' => $kode_mapel,
				'nama'       => $nama
			];

			$sql = $this->model->tambah('master_mapel', $data);
			if ($sql) {
				$this->session->set_flashdata('ok', 'Data master mapel berhasil ditambahkan.');
			} else {
				$this->session->set_flashdata('error', 'Data master mapel gagal ditambahkan.');
			}
		} else {
			// School admin adds mapping from master_mapel to mapel table
			$id_master_mapel = $this->input->post('id_master_mapel', true);

			if (empty($id_master_mapel)) {
				$this->session->set_flashdata('error', 'Silakan pilih mata pelajaran.');
				redirect('mapel');
			}

			$master = $this->db->get_where('master_mapel', ['id_master_mapel' => $id_master_mapel])->row();
			if (!$master) {
				$this->session->set_flashdata('error', 'Mata pelajaran tidak ditemukan.');
				redirect('mapel');
			}

			// Check if already active
			$exists = $this->db->get_where('mapel', [
				'id_master_mapel' => $id_master_mapel,
				'id_lembaga'      => $this->id_lembaga
			])->row();

			if ($exists) {
				$this->session->set_flashdata('error', 'Mata pelajaran ini sudah ditambahkan.');
				redirect('mapel');
			}

			$data = [
				'id_master_mapel' => $id_master_mapel,
				'kode_mapel'      => $master->kode_mapel,
				'nama'            => $master->nama,
				'id_lembaga'      => $this->id_lembaga
			];

			$sql = $this->model->tambah('mapel', $data);
			if ($sql) {
				$this->session->set_flashdata('ok', 'Mata pelajaran berhasil diaktifkan.');
			} else {
				$this->session->set_flashdata('error', 'Mata pelajaran gagal diaktifkan.');
			}
		}

		redirect('mapel');
	}

	public function hapus()
	{
		$id = $this->input->post('id', true);
		$level = $this->session->userdata('level');

		if ($level === 'super_admin') {
			$sql = $this->model->hapus('master_mapel', 'id_master_mapel', $id);
			if ($sql) {
				// Cascade delete local mappings
				$this->db->where('id_master_mapel', $id)->delete('mapel');
			}
		} else {
			// Delete local active mapel entry for their school
			$sql = $this->db->where([
				'id_mapel'   => $id,
				'id_lembaga' => $this->id_lembaga
			])->delete('mapel');
		}

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data mapel gagal dihapus.');
		} else {
			$this->session->set_flashdata('ok', 'Data mapel berhasil dihapus.');
		}
	}

	public function getById($id)
	{
		$level = $this->session->userdata('level');
		if ($level === 'super_admin') {
			$data = $this->db->select('id_master_mapel as id_mapel, kode_mapel, nama')
				->get_where('master_mapel', ['id_master_mapel' => $id])
				->row_array();
		} else {
			$data = $this->model->getBy('mapel', 'id_mapel', $id)->row_array();
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function update($id)
	{
		$level = $this->session->userdata('level');
		if ($level !== 'super_admin') {
			$this->session->set_flashdata('error', 'Hanya administrator yang diizinkan mengubah mata pelajaran global.');
			redirect('mapel');
		}

		$kode_mapel = $this->input->post('kode_mapel', true);
		$nama       = $this->input->post('nama', true);

		if (empty(trim($kode_mapel))) {
			$kode_mapel = generate_kode_mapel($nama, $id);
		}

		$data = [
			'kode_mapel' => $kode_mapel,
			'nama'       => $nama,
		];

		$sql = $this->model->edit('master_mapel', 'id_master_mapel', $id, $data);

		if ($sql) {
			// Cascade to mapel tables
			$this->db->where('id_master_mapel', $id)->update('mapel', [
				'kode_mapel' => $kode_mapel,
				'nama'       => $nama
			]);
			$this->session->set_flashdata('ok', 'Data master mapel berhasil diupdate.');
		} else {
			$this->session->set_flashdata('error', 'Data master mapel gagal diupdate.');
		}
		redirect('mapel');
	}

	public function get_available_master()
	{
		$level = $this->session->userdata('level');
		if ($level === 'super_admin') {
			echo json_encode([]);
			return;
		}

		// Retrieve active master mapel IDs for this school
		$active_ids = $this->db->select('id_master_mapel')
			->where('id_lembaga', $this->id_lembaga)
			->where('id_master_mapel IS NOT NULL')
			->get('mapel')
			->result_array();
		$active_mapel_ids = array_column($active_ids, 'id_master_mapel');

		// Fetch the school's shape (jenjang)
		$lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $this->id_lembaga])->row();
		$jenjang = '';
		if ($lembaga) {
			if (!empty($lembaga->jenis_lembaga)) {
				if (strpos($lembaga->jenis_lembaga, '{') !== false) {
					$jl = json_decode($lembaga->jenis_lembaga, TRUE);
					$jenjang = is_array($jl) ? ($jl['nama'] ?? '') : $lembaga->jenis_lembaga;
				} else {
					$jenjang = $lembaga->jenis_lembaga;
				}
			}
			if (empty($jenjang)) {
				$jenjang = $lembaga->jenjang;
			}
		}

		// Get all master subjects
		$this->db->select('*');
		$this->db->from('master_mapel');
		$this->db->order_by('nama', 'ASC');
		$master_mapels = $this->db->get()->result_array();

		// Filter master subjects based on jenjang peruntukan
		$filtered = [];
		foreach ($master_mapels as $m) {
			$eligible = TRUE;
			if (!empty($jenjang) && !empty($m['jenis_lembaga'])) {
				$allowed = json_decode($m['jenis_lembaga'], TRUE) ?: [];
				
				// Handle array of objects or strings
				$allowed_names = [];
				foreach ($allowed as $item) {
					$allowed_names[] = strtoupper(is_array($item) ? ($item['nama'] ?? '') : (is_object($item) ? ($item->nama ?? '') : (is_string($item) ? $item : '')));
				}

				// Normalize jenjang (e.g. "SMP / sederajat" -> "SMP")
				$jenjang_clean = trim(preg_replace('/\/.*$/', '', strtoupper($jenjang)));

				if (!in_array($jenjang_clean, $allowed_names)) {
					$eligible = FALSE;
				}
			}
			if ($eligible) {
				$m['is_active'] = in_array($m['id_master_mapel'], $active_mapel_ids);
				$filtered[] = $m;
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($filtered));
	}


	public function save_selection()
	{
		$level = $this->session->userdata('level');
		if ($level === 'super_admin') {
			$this->session->set_flashdata('error', 'Super admin tidak memiliki filter pilihan lembaga.');
			redirect('mapel');
		}

		$selected_ids = $this->input->post('id_master_mapel', true) ?: []; // array of selected master_mapel IDs
		if (!is_array($selected_ids)) {
			$selected_ids = [];
		}

		// Get all current active mapels for this school
		$current_active = $this->db->get_where('mapel', [
			'id_lembaga' => $this->id_lembaga,
			'id_master_mapel IS NOT NULL' => NULL
		])->result_array();

		$current_active_ids = array_column($current_active, 'id_master_mapel', 'id_mapel'); // id_mapel => id_master_mapel

		$this->db->trans_start();

		// 1. Insert new selections
		foreach ($selected_ids as $id_master) {
			if (!in_array($id_master, $current_active_ids)) {
				// Insert new local mapel
				$master = $this->db->get_where('master_mapel', ['id_master_mapel' => $id_master])->row();
				if ($master) {
					$data = [
						'id_master_mapel' => $id_master,
						'kode_mapel'      => $master->kode_mapel,
						'nama'            => $master->nama,
						'id_lembaga'      => $this->id_lembaga
					];
					$this->db->insert('mapel', $data);
				}
			}
		}

		// 2. Delete unselected ones
		$cannot_delete_names = [];
		foreach ($current_active_ids as $id_mapel => $id_master) {
			if (!in_array($id_master, $selected_ids)) {
				// Check if used in jadwal or harian KBM logs
				$used_in_jadwal = $this->db->get_where('jadwal', ['id_mapel' => $id_mapel])->num_rows();
				$used_in_harian = 0;
				if ($this->db->table_exists('harian')) {
					$used_in_harian = $this->db->get_where('harian', ['id_mapel' => $id_mapel])->num_rows();
				}

				if ($used_in_jadwal > 0 || $used_in_harian > 0) {
					// Cannot delete because it is already used in schedules or KBM logs!
					$mapel_name = $this->db->get_where('mapel', ['id_mapel' => $id_mapel])->row('nama');
					$cannot_delete_names[] = $mapel_name;
				} else {
					// Safe to delete
					$this->db->where('id_mapel', $id_mapel)->delete('mapel');
				}
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal menyimpan pilihan mata pelajaran.');
		} else {
			if (!empty($cannot_delete_names)) {
				$msg = 'Pilihan berhasil disimpan. Catatan: Mata pelajaran berikut tidak dapat dinonaktifkan karena sudah digunakan dalam jadwal/jurnal KBM: ' . implode(', ', $cannot_delete_names);
				$this->session->set_flashdata('warning', $msg);
			} else {
				$this->session->set_flashdata('ok', 'Pilihan mata pelajaran berhasil disimpan.');
			}
		}

		redirect('mapel');
	}
}
