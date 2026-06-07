<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');

		$this->mustLogin();
		$this->AdminOrSuper();

		$this->level = $this->session->userdata('level');
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
	}

	public function index()
	{

		$data['menu'] = 'setting';
		$data['sub'] = 'setting';

		$data['jml_rombel'] = $this->model->getBy2('setting', 'key', 'jml_jp', 'id_lembaga', $this->id_lembaga)->row();

		// Load WA API settings
		$wa_api_url_db = $this->model->getBy('setting', 'key', 'wa_api_url')->row('isi');
		$wa_api_key_db = $this->model->getBy('setting', 'key', 'wa_api_key')->row('isi');
		$wa_group_id_db = $this->model->getBy2('setting', 'key', 'wa_group_id', 'id_lembaga', $this->id_lembaga)->row('isi');
		$wa_group_name_db = $this->model->getBy2('setting', 'key', 'wa_group_name', 'id_lembaga', $this->id_lembaga)->row('isi');
		$wa_selected_groups_db = $this->model->getBy2('setting', 'key', 'wa_selected_groups', 'id_lembaga', $this->id_lembaga)->row('isi');

		// Retrieve new settings
		$waktu_info_jadwal_db = $this->model->getBy2('setting', 'key', 'waktu_info_jadwal', 'id_lembaga', $this->id_lembaga)->row('isi');
		$waktu_pembiasaan_db = $this->model->getBy2('setting', 'key', 'waktu_pembiasaan', 'id_lembaga', $this->id_lembaga)->row('isi');
		$waktu_kehadiran_db = $this->model->getBy2('setting', 'key', 'waktu_kehadiran', 'id_lembaga', $this->id_lembaga)->row('isi');

		// Retrieve session_id from lembaga table dynamically
		$lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga'")->row();
		$wa_api_session_id = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

		$data['wa_api_url'] = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');
		$data['wa_api_session_id'] = $wa_api_session_id;
		$data['wa_api_key'] = $wa_api_key_db ?: (getenv('WA_API_KEY') ?: '');
		$data['wa_group_id'] = $wa_group_id_db ?: "";
		$data['wa_group_name'] = $wa_group_name_db ?: "";
		$data['wa_selected_groups'] = $wa_selected_groups_db ?: '[]';
		$data['waktu_info_jadwal'] = $waktu_info_jadwal_db ?: "";
		$data['waktu_pembiasaan'] = $waktu_pembiasaan_db ?: "";
		$data['waktu_kehadiran'] = $waktu_kehadiran_db ?: "";

		$this->load->view('admin/setting', $data);
	}

	public function save_wa_api()
	{
		$url = $this->input->post('wa_api_url', TRUE);
		$session_id = $this->input->post('wa_api_session_id', TRUE);
		$api_key = $this->input->post('wa_api_key', TRUE);

		$settings = [
			'wa_api_url' => $url,
			'wa_api_session_id' => $session_id,
			'wa_api_key' => $api_key
		];

		foreach ($settings as $key => $val) {
			$cek = $this->model->getBy2('setting', 'key', $key, 'id_lembaga', $this->id_lembaga)->row();
			if ($cek) {
				$this->model->edit2('setting', 'key', $key, 'id_lembaga', $this->id_lembaga, ['isi' => $val]);
			} else {
				$this->model->tambah('setting', ['key' => $key, 'isi' => $val, 'id_lembaga' => $this->id_lembaga]);
			}
		}

		$this->session->set_flashdata('ok', 'Update Konfigurasi API berhasil');
		redirect('setting');
	}

	public function save_wa_group()
	{
		$id_group = $this->input->post('id_group', TRUE);
		$nama_group = $this->input->post('nama_group', TRUE);

		if (empty($id_group)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'message' => 'ID Group tidak boleh kosong']));
			return;
		}

		$cek = $this->model->getBy2('setting', 'key', 'wa_selected_groups', 'id_lembaga', $this->id_lembaga)->row();
		$groups = [];
		if ($cek && !empty($cek->isi)) {
			$groups = json_decode($cek->isi, true) ?: [];
		}

		// Check if already selected
		$exists = false;
		foreach ($groups as $g) {
			if ($g['id'] === $id_group) {
				$exists = true;
				break;
			}
		}

		if (!$exists) {
			// Limit to maximum 2 groups
			if (count($groups) >= 2) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(['status' => false, 'message' => 'Maksimal pilihan adalah 2 grup']));
				return;
			}

			$groups[] = [
				'id' => $id_group,
				'subject' => $nama_group
			];
			$json_val = json_encode($groups);

			if ($cek) {
				$this->model->edit2('setting', 'key', 'wa_selected_groups', 'id_lembaga', $this->id_lembaga, ['isi' => $json_val]);
			} else {
				$this->model->tambah('setting', ['key' => 'wa_selected_groups', 'isi' => $json_val, 'id_lembaga' => $this->id_lembaga]);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(['status' => true, 'message' => 'Grup berhasil ditambahkan']));
	}

	public function delete_wa_group()
	{
		$id_group = $this->input->post('id_group', TRUE);

		if (empty($id_group)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'message' => 'ID Group tidak boleh kosong']));
			return;
		}

		$cek = $this->model->getBy2('setting', 'key', 'wa_selected_groups', 'id_lembaga', $this->id_lembaga)->row();
		if ($cek && !empty($cek->isi)) {
			$groups = json_decode($cek->isi, true) ?: [];
			$new_groups = [];
			foreach ($groups as $g) {
				if ($g['id'] !== $id_group) {
					$new_groups[] = $g;
				}
			}
			$json_val = json_encode($new_groups);
			$this->model->edit2('setting', 'key', 'wa_selected_groups', 'id_lembaga', $this->id_lembaga, ['isi' => $json_val]);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(['status' => true, 'message' => 'Grup berhasil dihapus']));
	}

	private function _api_request($url, $method = 'GET', $post_data = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			if (is_array($post_data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			} else if ($post_data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
		}

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return [
			'code' => $http_code ?: 500,
			'body' => $response ?: json_encode(['status' => false, 'message' => 'API connection timeout or empty response'])
		];
	}

	public function wa_status()
	{
		$wa_api_url_db = $this->model->getBy('setting', 'key', 'wa_api_url')->row('isi');
		$lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga'")->row();
		$wa_api_session_id = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

		$url = ($wa_api_url_db ?: (getenv('WA_API_URL') ?: '')) . '/sessions/' . $wa_api_session_id . '/status';
		$res = $this->_api_request($url);

		$this->output
			->set_status_header($res['code'])
			->set_content_type('application/json')
			->set_output($res['body']);
	}

	public function wa_disconnect()
	{
		$wa_api_url_db = $this->model->getBy('setting', 'key', 'wa_api_url')->row('isi');
		$lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga'")->row();
		$wa_api_session_id = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

		$url = ($wa_api_url_db ?: (getenv('WA_API_URL') ?: '')) . '/disconnect';
		$res = $this->_api_request($url, 'POST', ['sessionId' => $wa_api_session_id]);

		$this->output
			->set_status_header($res['code'])
			->set_content_type('application/json')
			->set_output($res['body']);
	}

	public function wa_groups()
	{
		$wa_api_url_db = $this->model->getBy('setting', 'key', 'wa_api_url')->row('isi');
		$lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga'")->row();
		$wa_api_session_id = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

		$url = ($wa_api_url_db ?: (getenv('WA_API_URL') ?: '')) . '/groups?sessionId=' . $wa_api_session_id;
		$res = $this->_api_request($url);

		$this->output
			->set_status_header($res['code'])
			->set_content_type('application/json')
			->set_output($res['body']);
	}



	public function jml_rombel()
	{
		$jml = $this->input->post('jml_rombel', TRUE);
		$waktu_info_jadwal = $this->input->post('waktu_info_jadwal', TRUE);
		$waktu_pembiasaan = $this->input->post('waktu_pembiasaan', TRUE);
		$waktu_kehadiran = $this->input->post('waktu_kehadiran', TRUE);

		$settings = [
			'jml_jp' => $jml,
			'waktu_info_jadwal' => $waktu_info_jadwal,
			'waktu_pembiasaan' => $waktu_pembiasaan,
			'waktu_kehadiran' => $waktu_kehadiran
		];

		$success = true;
		foreach ($settings as $key => $val) {
			$cek = $this->model->getBy2('setting', 'key', $key, 'id_lembaga', $this->id_lembaga)->row();
			if ($cek) {
				$sql = $this->model->edit2('setting', 'key', $key, 'id_lembaga', $this->id_lembaga, ['isi' => $val]);
			} else {
				$sql = $this->model->tambah('setting', ['key' => $key, 'isi' => $val, 'id_lembaga' => $this->id_lembaga]);
			}
			if (!$sql) {
				$success = false;
			}
		}

		if ($success) {
			$this->session->set_flashdata('ok', 'Update pengaturan berhasil');
		} else {
			$this->session->set_flashdata('error', 'Update pengaturan gagal');
		}
		redirect('setting');
	}

	public function set_lembaga($id_lembaga)
	{
		$this->session->set_userdata('id_lembaga', $id_lembaga);

		$this->db->where('id_user', $this->iduser);
		$this->db->update('user', ['id_lembaga' => $id_lembaga]);
		// balik ke halaman sebelumnya
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function loadJam()
	{
		$jml_jp = $this->model->getBy('setting', 'key', 'jml_jp')->row('isi');

		$hasil = '';

		for ($i = 1; $i <= $jml_jp; $i++) {
			$hasil .= "
				<tr class='hover:bg-gray-50 dark:hover:bg-gray-700/40'>
					<td class='px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200'>
						Jam ke-{$i}
					</td>

					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Saturday'>
					</td>
					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Sunday'>
					</td>
					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Monday'>
					</td>
					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Tuesday'>
					</td>
					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Wednesday'>
					</td>
					<td class='px-2 py-1 text-center'>
						<input type='time' class='time-input jam-input' data-jam='{$i}' data-hari='Thursday'>
					</td>
				</tr>
			";
		}

		echo $hasil;
	}

	public function simpanJam()
	{
		$jam = $this->input->post('jam', TRUE);
		$hari = $this->input->post('hari', TRUE);
		$waktu = $this->input->post('waktu', TRUE);

		$cek = $this->model->getBy2('jp', 'jam', $jam, 'hari', $hari)->row();
		if ($cek) {
			$this->model->edit2('jp', 'hari', $hari, 'jam', $jam, ['waktu' => $waktu]);
			echo json_encode(['status' => true]);
		} else {
			$this->model->tambah('jp', ['hari' => $hari, 'jam' => $jam, 'waktu' => $waktu]);
			echo json_encode(['status' => true]);
		}
	}
}
