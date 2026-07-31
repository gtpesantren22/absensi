<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends MY_Controller
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
		$data['judul'] = 'Data Siswa';
		$data['menu'] = 'master';
		$data['sub'] = 'siswa';

		$this->load->view('admin/siswa', $data);
	}

	public function datatable()
	{
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= TOTAL ================= */
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa');

		$this->db->where('registrasi_siswa.id_lembaga', $this->id_lembaga);

		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nama', $search)
				->or_like('siswa.nis', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}
		$this->db->select('COUNT(*) as total');
		$total = $this->db->get()->row()->total;

		/* ================= DATA ================= */
		$this->db->select('siswa.*');
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa');
		$this->db->where('registrasi_siswa.id_lembaga', $this->id_lembaga);
		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nama', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}
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

	public function add()
	{
		$nama         = $this->input->post('nama', true);
		$alamat        = $this->input->post('alamat', true);
		$jenis_kelamin = $this->input->post('jenis_kelamin', true);
		$nisn 	   = $this->input->post('nisn', true);
		$nis 	   = $this->input->post('nis', true);

		$data = [
			'id_siswa'    => $this->uuid->v4(),
			'nama'         => $nama,
			'alamat'        => $alamat,
			'jkl' => $jenis_kelamin,
			'nisn'		=> $nisn,
			'nis'		=> $nis,
		];

		$sql = $this->model->tambah('siswa', $data);
		if ($sql) {
			$this->session->set_flashdata('ok', 'Data siswa berhasil ditambahkan.');
		} else {
			$this->session->set_flashdata('error', 'Data siswa gagal ditambahkan.');
		}
		redirect('siswa');
	}

	public function hapus()
	{
		$id = $this->input->post('id', true);
		$sql = $this->db
			->where('id_siswa', $id)
			->where('id_lembaga', $this->id_lembaga)
			->delete('registrasi_siswa');
		if (!$sql) {
			$this->session->set_flashdata('error', 'Data siswa gagal dihapus.');
			redirect('siswa');
		} else {
			$this->session->set_flashdata('ok', 'Data siswa berhasil dihapus.');
			redirect('siswa');
		}
	}

	public function getById($id)
	{
		$data = $this->model->getBy('siswa', 'id_siswa', $id)->row_array();

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function update($id)
	{
		$id_siswa    = $id;
		$nama         = $this->input->post('nama', true);
		$alamat        = $this->input->post('alamat', true);
		$jenis_kelamin = $this->input->post('jenis_kelamin', true);
		$nisn 	   = $this->input->post('nisn', true);
		$nis 	   = $this->input->post('nis', true);

		$data = [
			'nama'         => $nama,
			'alamat'        => $alamat,
			'jkl' => $jenis_kelamin,
			'nisn'		=> $nisn,
			'nis'		=> $nis,
		];

		$sql = $this->model->edit('siswa', 'id_siswa', $id_siswa, $data);

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data siswa gagal diupdate.');
			redirect('siswa');
		} else {
			$this->session->set_flashdata('ok', 'Data siswa berhasil diupdate.');
			redirect('siswa');
		}
	}

	// public function downloadTemplate()
	// {
	// 	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->setTitle('Template Guru');

	// 	// Header kolom
	// 	$sheet->setCellValue('A1', 'NISN');
	// 	$sheet->setCellValue('B1', 'Nama Lengkap');
	// 	$sheet->setCellValue('C1', 'Alamat');
	// 	$sheet->setCellValue('D1', 'Jenis Kelamin (L/P)');

	// 	// Set lebar kolom
	// 	foreach (range('A', 'D') as $col) {
	// 		$sheet->getColumnDimension($col)->setAutoSize(true);
	// 	}

	// 	// Download sebagai file Excel
	// 	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// 	header('Content-Disposition: attachment;filename="template_siswa.xlsx"');
	// 	header('Cache-Control: max-age=0');

	// 	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	// 	$writer->save('php://output');
	// 	exit;
	// }

	// public function upload_excel()
	// {
	// 	if (!isset($_FILES['file']['name'])) {
	// 		show_error('File tidak ditemukan');
	// 	}

	// 	$file = $_FILES['file']['tmp_name'];
	// 	$ext  = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

	// 	if (!in_array($ext, ['xls', 'xlsx'])) {
	// 		show_error('Format file tidak didukung');
	// 	}

	// 	$spreadsheet = IOFactory::load($file);
	// 	$sheetData   = $spreadsheet->getActiveSheet()->toArray();

	// 	$dataInsert = [];

	// 	foreach ($sheetData as $i => $row) {
	// 		if ($i == 0) continue; // skip header
	// 		if (empty($row[1])) continue;

	// 		$dataInsert[] = [
	// 			'id_siswa'   => $this->uuid->v4(),
	// 			'nisn'      => trim($row[0]),
	// 			'nama'      => trim($row[1]),
	// 			'alamat'    => trim($row[2]),
	// 			'jkl'       => $row[3] == 'L' ? 'Laki-laki' : 'Perempuan',
	// 		];
	// 	}

	// 	$this->model->insertBatch('siswa', $dataInsert);

	// 	$this->session->set_flashdata('ok', 'Data siswa berhasil diupload.');
	// 	redirect('siswa');
	// }
}
