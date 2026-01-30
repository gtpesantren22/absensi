<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->library('Dynamic_db'); // load dulu
		$this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()
		$this->mustLogin();
		$this->AdminOrSuper();

		$this->level = $this->session->userdata('level');
		$this->iduser = $this->session->userdata('id_user');
	}

	public function index()
	{
		$usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();

		$data['menu'] = 'setting';
		$data['sub'] = 'setting';

		$data['jml_rombel'] = $this->model->getBy('setting', 'key', 'jml_jp')->row();

		$this->load->view('admin/setting', $data);
	}

	public function jml_rombel()
	{
		$jml = $this->input->post('jml_rombel', TRUE);
		$sql = $this->model->edit('setting', 'key', 'jml_jp', ['isi' => $jml]);
		if ($sql) {
			$this->session->set_flashdata('ok', 'Update JP berhasil');
			redirect('setting');
		} else {
			$this->session->set_flashdata('error', 'Update JP gagal');
			redirect('setting');
		}
	}

	public function set_lembaga($id_lembaga)
	{
		$infolmb = $this->db->query("SELECT list_db.db_name FROM lembaga JOIN list_db ON lembaga.id_db=list_db.id WHERE id_lembaga = '$id_lembaga' ")->row();

		$this->session->set_userdata('db_selected', $infolmb->db_name);

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
