<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sistem extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');

		$this->mustLogin();
		$this->onlyAdminSuper();

		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
	}

	public function index()
	{
		$data['menu'] = 'sistem';
		$data['sub'] = 'sistem';

		// Fetch all tahun ajaran
		$data['tahuns'] = $this->db->order_by('nama_tahun', 'DESC')->get('tahun_ajaran')->result();

		// Fetch all semesters with year name
		$data['semesters'] = $this->db->select('s.*, t.nama_tahun')
			->from('semester s')
			->join('tahun_ajaran t', 's.id_tahun = t.id_tahun', 'left')
			->order_by('t.nama_tahun', 'DESC')
			->order_by('s.nama_semester', 'DESC')
			->get()
			->result();

		// Retrieve API Token settings
		$api_token_db = $this->model->getBy('setting', 'key', 'api_token')->row('isi');
		$this->config->load('api', TRUE);
		$api_token_config = $this->config->item('api_token', 'api') ?: 'absensi_api_token_secret_xyz';
		$data['api_token'] = $api_token_db ?: $api_token_config;

		// Retrieve maintenance mode settings
		$maint_db = $this->model->getBy('setting', 'key', 'maintenance_mode')->row();
		$data['maintenance_mode'] = ($maint_db && $maint_db->isi === '1') ? true : false;

		$this->load->view('admin/sistem', $data);
	}

	// ==========================================
	// CRUD TAHUN AJARAN
	// ==========================================

	public function tambah_tahun()
	{
		$nama_tahun = $this->input->post('nama_tahun', TRUE);
		$tgl_mulai = $this->input->post('tanggal_mulai', TRUE) ?: null;
		$tgl_selesai = $this->input->post('tanggal_selesai', TRUE) ?: null;
		$is_active = $this->input->post('is_active', TRUE) ? 1 : 0;

		$this->db->trans_start();
		if ($is_active == 1) {
			$this->db->update('tahun_ajaran', ['is_active' => 0]);
			$this->db->update('semester', ['is_active' => 0]);
		}

		$this->db->insert('tahun_ajaran', [
			'nama_tahun' => $nama_tahun,
			'tanggal_mulai' => $tgl_mulai,
			'tanggal_selesai' => $tgl_selesai,
			'is_active' => $is_active
		]);

		if ($is_active == 1) {
			$new_id = $this->db->insert_id();
			// Find a semester under this new year to activate
			$sem = $this->db->order_by("FIELD(nama_semester, 'Ganjil', 'Genap')")->get_where('semester', ['id_tahun' => $new_id])->row();
			if ($sem) {
				$this->db->where('id_semester', $sem->id_semester)->update('semester', ['is_active' => 1]);
			}
		}
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Tahun ajaran berhasil ditambahkan');
		redirect('sistem');
	}

	public function edit_tahun($id)
	{
		$nama_tahun = $this->input->post('nama_tahun', TRUE);
		$tgl_mulai = $this->input->post('tanggal_mulai', TRUE) ?: null;
		$tgl_selesai = $this->input->post('tanggal_selesai', TRUE) ?: null;
		$is_active = $this->input->post('is_active', TRUE) ? 1 : 0;

		$this->db->trans_start();
		if ($is_active == 1) {
			$this->db->update('tahun_ajaran', ['is_active' => 0]);
			$this->db->update('semester', ['is_active' => 0]);
		}

		$this->db->where('id_tahun', $id)->update('tahun_ajaran', [
			'nama_tahun' => $nama_tahun,
			'tanggal_mulai' => $tgl_mulai,
			'tanggal_selesai' => $tgl_selesai,
			'is_active' => $is_active
		]);

		if ($is_active == 1) {
			$sem = $this->db->order_by("FIELD(nama_semester, 'Ganjil', 'Genap')")->get_where('semester', ['id_tahun' => $id])->row();
			if ($sem) {
				$this->db->where('id_semester', $sem->id_semester)->update('semester', ['is_active' => 1]);
			}
		}
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Tahun ajaran berhasil diupdate');
		redirect('sistem');
	}

	public function hapus_tahun($id)
	{
		$this->db->where('id_tahun', $id)->delete('tahun_ajaran');
		$this->session->set_flashdata('ok', 'Tahun ajaran berhasil dihapus');
		redirect('sistem');
	}

	public function toggle_tahun_active($id)
	{
		$this->db->trans_start();
		// Deactivate other years, activate selected
		$this->db->update('tahun_ajaran', ['is_active' => 0]);
		$this->db->where('id_tahun', $id)->update('tahun_ajaran', ['is_active' => 1]);

		// Deactivate semesters belonging to other years
		$this->db->update('semester', ['is_active' => 0]);

		// Find a semester under this year to activate
		$sem = $this->db->order_by("FIELD(nama_semester, 'Ganjil', 'Genap')")->get_where('semester', ['id_tahun' => $id])->row();
		if ($sem) {
			$this->db->where('id_semester', $sem->id_semester)->update('semester', ['is_active' => 1]);
		}
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Tahun ajaran aktif berhasil diperbarui');
		redirect('sistem');
	}

	// ==========================================
	// CRUD SEMESTER
	// ==========================================

	public function tambah_semester()
	{
		$id_tahun = $this->input->post('id_tahun', TRUE);
		$nama_semester = $this->input->post('nama_semester', TRUE);
		$tgl_mulai = $this->input->post('tanggal_mulai', TRUE) ?: null;
		$tgl_selesai = $this->input->post('tanggal_selesai', TRUE) ?: null;
		$is_active = $this->input->post('is_active', TRUE) ? 1 : 0;

		$this->db->trans_start();
		if ($is_active == 1) {
			$this->db->update('semester', ['is_active' => 0]);
			// Also activate parent year
			$this->db->update('tahun_ajaran', ['is_active' => 0]);
			$this->db->where('id_tahun', $id_tahun)->update('tahun_ajaran', ['is_active' => 1]);
		}

		$this->db->insert('semester', [
			'id_tahun' => $id_tahun,
			'nama_semester' => $nama_semester,
			'tanggal_mulai' => $tgl_mulai,
			'tanggal_selesai' => $tgl_selesai,
			'is_active' => $is_active
		]);
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Semester berhasil ditambahkan');
		redirect('sistem');
	}

	public function edit_semester($id)
	{
		$id_tahun = $this->input->post('id_tahun', TRUE);
		$nama_semester = $this->input->post('nama_semester', TRUE);
		$tgl_mulai = $this->input->post('tanggal_mulai', TRUE) ?: null;
		$tgl_selesai = $this->input->post('tanggal_selesai', TRUE) ?: null;
		$is_active = $this->input->post('is_active', TRUE) ? 1 : 0;

		$this->db->trans_start();
		if ($is_active == 1) {
			$this->db->update('semester', ['is_active' => 0]);
			// Also activate parent year
			$this->db->update('tahun_ajaran', ['is_active' => 0]);
			$this->db->where('id_tahun', $id_tahun)->update('tahun_ajaran', ['is_active' => 1]);
		}

		$this->db->where('id_semester', $id)->update('semester', [
			'id_tahun' => $id_tahun,
			'nama_semester' => $nama_semester,
			'tanggal_mulai' => $tgl_mulai,
			'tanggal_selesai' => $tgl_selesai,
			'is_active' => $is_active
		]);
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Semester berhasil diupdate');
		redirect('sistem');
	}

	public function hapus_semester($id)
	{
		$this->db->where('id_semester', $id)->delete('semester');
		$this->session->set_flashdata('ok', 'Semester berhasil dihapus');
		redirect('sistem');
	}

	public function toggle_semester_active($id)
	{
		$this->db->trans_start();
		// Fetch semester to find parent year
		$sem = $this->db->get_where('semester', ['id_semester' => $id])->row();
		if ($sem) {
			// Activate parent year and deactivate other years
			$this->db->update('tahun_ajaran', ['is_active' => 0]);
			$this->db->where('id_tahun', $sem->id_tahun)->update('tahun_ajaran', ['is_active' => 1]);

			// Activate selected semester and deactivate other semesters
			$this->db->update('semester', ['is_active' => 0]);
			$this->db->where('id_semester', $id)->update('semester', ['is_active' => 1]);
		}
		$this->db->trans_complete();

		$this->session->set_flashdata('ok', 'Semester dan Tahun Ajaran aktif berhasil diperbarui');
		redirect('sistem');
	}

	// ==========================================
	// UTILITIES
	// ==========================================

	public function backup_sql()
	{
		$this->load->dbutil();

		$prefs = array(
			'format'      => 'txt',
			'filename'    => 'backup.sql',
			'add_drop'    => TRUE,
			'add_insert'  => TRUE,
			'newline'     => "\n"
		);

		$backup = $this->dbutil->backup($prefs);

		$this->load->helper('download');
		force_download('backup-db-absensi-' . date('Y-m-d-H-i-s') . '.sql', $backup);
	}

	public function save_api_token()
	{
		$token = $this->input->post('api_token', TRUE);

		$cek = $this->model->getBy('setting', 'key', 'api_token')->row();
		if ($cek) {
			$this->model->edit('setting', 'key', 'api_token', ['isi' => $token]);
		} else {
			$this->model->tambah('setting', ['key' => 'api_token', 'isi' => $token]);
		}

		$this->session->set_flashdata('ok', 'API Token berhasil diupdate');
		redirect('sistem');
	}

	public function save_app_settings()
	{
		$app_name = trim($this->input->post('app_name', TRUE));

		if ($app_name !== '') {
			$cek = $this->model->getBy('setting', 'key', 'app_name')->row();
			if ($cek) {
				$this->model->edit('setting', 'key', 'app_name', ['isi' => $app_name]);
			} else {
				$this->model->tambah('setting', ['key' => 'app_name', 'isi' => $app_name]);
			}
		}

		// Handle Logo Upload
		if (!empty($_FILES['app_logo']['name'])) {
			$config['upload_path']   = './uploads/logo/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg|ico|svg';
			$config['max_size']      = 2048; // 2MB
			$config['file_name']     = 'logo_' . time();

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('app_logo')) {
				// delete old logo
				$old_logo = $this->model->getBy('setting', 'key', 'app_logo')->row('isi');
				if ($old_logo && file_exists('./uploads/logo/' . $old_logo)) {
					@unlink('./uploads/logo/' . $old_logo);
				}

				$upload_data = $this->upload->data();
				$new_logo = $upload_data['file_name'];

				$cek_logo = $this->model->getBy('setting', 'key', 'app_logo')->row();
				if ($cek_logo) {
					$this->model->edit('setting', 'key', 'app_logo', ['isi' => $new_logo]);
				} else {
					$this->model->tambah('setting', ['key' => 'app_logo', 'isi' => $new_logo]);
				}
			} else {
				$this->session->set_flashdata('error', $this->upload->display_errors('', ''));
				redirect('sistem');
			}
		}

		$this->session->set_flashdata('ok', 'Pengaturan aplikasi berhasil disimpan');
		redirect('sistem');
	}

	public function toggle_maintenance()
	{
		header('Content-Type: application/json');
		$status = $this->input->post('status', TRUE);
		$val = ($status == 1) ? '1' : '0';
		$msg = ($status == 1) ? 'Mode Maintenance berhasil diaktifkan.' : 'Mode Maintenance berhasil dinonaktifkan.';

		$cek = $this->model->getBy('setting', 'key', 'maintenance_mode')->row();
		if ($cek) {
			$this->model->edit('setting', 'key', 'maintenance_mode', ['isi' => $val]);
		} else {
			$this->model->tambah('setting', ['key' => 'maintenance_mode', 'isi' => $val]);
		}

		echo json_encode([
			'status' => true,
			'message' => $msg
		]);
		exit;
	}
}
