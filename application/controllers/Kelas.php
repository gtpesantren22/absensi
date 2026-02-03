<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Kelas extends MY_Controller
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
	}

	public function index()
	{
		$data['judul'] = 'Data Kelas';
		$data['menu'] = 'master';
		$data['sub'] = 'kelas';

		$this->load->view('admin/kelas', $data);
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


		/* ================= TOTAL ================= */
		if (!empty($search)) {
			$this->db_active->group_start()
				->like('nama', $search)
				->or_like('jenis', $search)
				->group_end();
		}
		$total = $this->db_active->count_all_results('kelas');

		/* ================= DATA ================= */
		if (!empty($search)) {
			$this->db_active->group_start()
				->like('nama', $search)
				->or_like('jenis', $search)
				->group_end();
		}
		$this->db_active->select('
			kelas.*,
			COUNT(rombel.id_siswa) AS jumlah_anggota
		');
		$this->db_active->from('kelas');
		$this->db_active->join('rombel', 'rombel.id_kelas = kelas.id_kelas', 'left');
		$this->db_active->group_by('kelas.id_kelas');
		$this->db_active->order_by($sortBy, $sortDir);
		$this->db_active->limit($perPage, $offset);

		$data = $this->db_active->get()->result_array();


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
		$nama         = $this->input->post('nama', true);
		$jenis        = $this->input->post('jenis', true);

		$data = [
			'nama'         => $nama,
			'jenis'        => $jenis,
		];

		$sql = $this->model->tambah('kelas', $data);

		if ($sql) {
			$this->session->set_flashdata('ok', 'Data kelas berhasil ditambahkan.');
		} else {
			$this->session->set_flashdata('error', 'Data kelas gagal ditambahkan.');
		}
		redirect('kelas');
	}

	public function hapus()
	{
		$id = $this->input->post('id', true);
		$sql = $this->model->hapus('kelas', 'id_kelas', $id);
		if (!$sql) {
			$this->session->set_flashdata('error', 'Data kelas gagal dihapus.');
			redirect('kelas');
		} else {
			$this->session->set_flashdata('ok', 'Data kelas berhasil dihapus.');
			redirect('kelas');
		}
	}

	public function getById($id)
	{
		$data = $this->model->getBy('kelas', 'id_kelas', $id)->row_array();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function update($id)
	{
		$id_kelas    = $id;
		$nama         = $this->input->post('nama', true);
		$jenis    = $this->input->post('jenis', true);

		$data = [
			'nama'         => $nama,
			'jenis'        => $jenis,
		];

		$sql = $this->model->edit('kelas', 'id_kelas', $id_kelas, $data);

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data kelas gagal diupdate.');
			redirect('kelas');
		} else {
			$this->session->set_flashdata('ok', 'Data kelas berhasil diupdate.');
			redirect('kelas');
		}
	}

	public function downloadTemplate()
	{
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Kelas');

		// Header kolom
		$sheet->setCellValue('A1', 'Nama Kelas');
		$sheet->setCellValue('B1', 'Jenis');
		// Set lebar kolom
		foreach (range('A', 'B') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Download sebagai file Excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="template_kelas.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function downloadTemplateAnggotaKelas($id_kelas)
	{
		$kelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Anggota Kelas');

		// Header kolom
		$sheet->setCellValue('A1', 'Anggota Kelas : ' . $kelas->nama);
		$sheet->setCellValue('A2', 'Nama Siswa');
		$sheet->setCellValue('B2', 'NISN');
		// Set lebar kolom
		/* 🔥 PAKSA KOLOM B JADI TEXT */
		$sheet->getStyle('B')->getNumberFormat()
			->setFormatCode(NumberFormat::FORMAT_TEXT);

		foreach (range('A', 'B') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Download sebagai file Excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="template_anggota_kelas_' . $kelas->nama . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function upload_excel()
	{
		if (!isset($_FILES['file']['name'])) {
			show_error('File tidak ditemukan');
		}

		$file = $_FILES['file']['tmp_name'];
		$ext  = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

		if (!in_array($ext, ['xls', 'xlsx'])) {
			show_error('Format file tidak didukung');
		}

		$spreadsheet = IOFactory::load($file);
		$sheetData   = $spreadsheet->getActiveSheet()->toArray();

		$dataInsert = [];

		foreach ($sheetData as $i => $row) {
			if ($i == 0) continue; // skip header
			if (empty($row[1])) continue;

			$dataInsert[] = [
				'nama' => $row[0],
				'jenis'      => trim($row[1]),
			];
		}

		$this->model->insertBatch('kelas', $dataInsert);

		$this->session->set_flashdata('ok', 'Data kelas berhasil diupload.');
		redirect('kelas');
	}

	public function anggota($id)
	{
		$data['judul'] = 'Anggota Kelas';
		$data['menu'] = 'master';
		$data['sub'] = 'kelas';

		$data['data_kelas'] = $this->model->getBy('kelas', 'id_kelas', $id)->row();

		$this->load->view('admin/anggota_kelas', $data);
	}

	public function dataSiswa()
	{
		$usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser'")->row();
		// $result = $this->Model_guru->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');
		$id_kelas = $this->input->get('id_kelas') ?? '';

		$cekKelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();

		$offset = ($page - 1) * $perPage;

		/* ================= TOTAL ================= */
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa', 'left');
		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nisn', $search)
				->or_like('siswa.nama', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}
		// hitung siswa unik
		if ($cekKelas->jenis == 'Utama') {
			$this->db->where('registrasi_siswa.id_lembaga', $usrdtl->id_lembaga);
		}
		$this->db->select('COUNT(DISTINCT registrasi_siswa.id_siswa) AS total');
		$result = $this->db->get()->row();

		$total = (int) $result->total;

		/* ================= DATA ================= */
		$this->db->select('siswa.*');
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa', 'left');
		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nisn', $search)
				->or_like('siswa.nama', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}
		// hitung siswa unik
		// if ($cekKelas->jenis == 'Utama') {
		// 	$this->db_active->where(
		// 		"id_siswa NOT IN (
		// 			SELECT id_siswa 
		// 			FROM rombel 
		// 		)",
		// 		NULL,
		// 		FALSE
		// 	);
		// }

		if ($cekKelas->jenis == 'Utama') {
			$this->db->where('registrasi_siswa.id_lembaga', $usrdtl->id_lembaga);
		}
		$this->db->group_by('registrasi_siswa.id_siswa');
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

	public function dataRombel($id_kelas)
	{
		$rombels = $this->db_active
			->select('id_rombel, id_siswa')
			->where('id_kelas', $id_kelas)
			->get('rombel')
			->result_array();

		if (empty($rombels)) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'data'  => [],
					'total' => 0
				]));
		}

		$ids = array_column($rombels, 'id_siswa');
		$siswas = $this->db
			->select('id_siswa, nama, nisn')
			->where_in('id_siswa', $ids)
			->order_by('nama', 'ASC')
			->get('siswa')
			->result_array();

		$siswaMap = array_column($siswas, null, 'id_siswa');
		$data = [];

		foreach ($rombels as $r) {
			if (isset($siswaMap[$r['id_siswa']])) {
				$data[] = [
					'id_rombel' => $r['id_rombel'],
					'nama'      => $siswaMap[$r['id_siswa']]['nama'],
					'nisn'      => $siswaMap[$r['id_siswa']]['nisn'],
				];
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'data'  => $data,
				'total' => count($data)
			]));
	}

	public function tambahAnggota()
	{
		$id_kelas = $this->input->post('id_kelas', true);
		$id_siswa = $this->input->post('id_siswa', true);

		// Cek apakah siswa sudah ada di kelas tersebut
		$cek = $this->model->getBy2('rombel', 'id_kelas', $id_kelas, 'id_siswa', $id_siswa);
		if ($cek->num_rows() > 0) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Siswa sudah ada di kelas ini.']));
			return;
		}

		$data = [
			'id_kelas' => $id_kelas,
			'id_siswa' => $id_siswa,
		];

		$sql = $this->model->tambah('rombel', $data);

		if ($sql) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'ok', 'message' => 'Siswa berhasil dipindahkan ke kelas.']));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Gagal memindahkan siswa ke kelas.']));
		}
	}

	public function hapusAnggota()
	{
		$id_rombel = $this->input->post('id_rombel', true);

		$sql = $this->model->hapus('rombel', 'id_rombel', $id_rombel);

		if ($sql) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'ok', 'message' => 'Siswa berhasil dihapus dari kelas.']));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus siswa dari kelas.']));
		}
	}

	public function kosongiAnggota()
	{
		$id_kelas = $this->input->post('id', true);
		$sql = $this->db_active->where('id_kelas', $id_kelas)->delete('rombel');

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data kelas gagal diupdate.');
			redirect('kelas');
		} else {
			$this->session->set_flashdata('ok', 'Data kelas berhasil diupdate.');
			redirect('kelas');
		}
	}

	public function upload_anggota()
	{
		if (!isset($_FILES['file']['name'])) {
			show_error('File tidak ditemukan');
		}

		$id_kelas = $this->input->post('id_kelas', true);
		$file = $_FILES['file']['tmp_name'];
		$ext  = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

		if (!in_array($ext, ['xls', 'xlsx'])) {
			show_error('Format file tidak didukung');
		}

		$spreadsheet = IOFactory::load($file);
		$sheetData   = $spreadsheet->getActiveSheet()->toArray();

		$dataInsert = [];

		foreach ($sheetData as $i => $row) {
			if ($i < 2) continue; // skip header
			if (empty($row[1])) continue;

			// Cari id_siswa berdasarkan nisn
			$nisn = trim($row[1]);
			$siswa = $this->db->query("SELECT * FROM siswa WHERE nisn = '$nisn' ")->row();
			if ($siswa) {
				$dataInsert[] = [
					'id_kelas' => $id_kelas,
					'id_siswa' => $siswa->id_siswa,
				];
			}
		}

		$this->model->insertBatch('rombel', $dataInsert);

		$this->session->set_flashdata('ok', 'Data anggota kelas berhasil diupload.');
		redirect('kelas/anggota/' . $id_kelas);
	}
}
