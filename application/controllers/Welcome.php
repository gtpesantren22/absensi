<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		header('Content-Type: text/plain');
		echo shell_exec('git checkout -- application/views/guru/*.php 2>&1');
		echo "\nGit restore complete.\n";
	}

	public function no_akes()
	{
		$this->load->view('no-akses');
	}

	public function maintenance()
	{
		$maintenance_mode = false;
		if ($this->db->table_exists('setting')) {
			$row_maint = $this->db->get_where('setting', ['key' => 'maintenance_mode'])->row();
			if ($row_maint && $row_maint->isi === '1') {
				$maintenance_mode = true;
			}
		}
		if (!$maintenance_mode) {
			redirect(base_url());
			exit;
		}

		// Load settings for the view
		$app_name = 'Absensi Sekolah';
		$app_logo = '';
		if ($this->db->table_exists('setting')) {
			$row_name = $this->db->get_where('setting', ['key' => 'app_name'])->row();
			if ($row_name) {
				$app_name = $row_name->isi;
			}
			$row_logo = $this->db->get_where('setting', ['key' => 'app_logo'])->row();
			if ($row_logo) {
				$app_logo = $row_logo->isi;
			}
		}

		$data['app_name'] = $app_name;
		$data['app_logo'] = $app_logo;

		$this->load->view('maintenance', $data);
	}
}
