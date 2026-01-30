<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Mapel extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->library('Dynamic_db'); // load dulu
		$this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

		$this->mustLogin();
		$this->AdminOrSuper();
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
		// $result = $this->Model_mapel->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= FILTER ================= */
		if (!empty($search)) {
			$this->db_active->group_start()
				->like('nama', $search)
				->or_like('kode_mapel', $search)
				->group_end();
		}

		/* ================= TOTAL ================= */
		$total = $this->db_active->count_all_results('mapel', false);

		/* ================= DATA ================= */
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
		$id = $this->input->post('id', true);
		$sql = $this->model->hapus('mapel', 'id_mapel', $id);
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

	public function downloadTemplate()
	{
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Mapel');

		// Header kolom
		$sheet->setCellValue('A1', 'Kode Mapel');
		$sheet->setCellValue('B1', 'Nama Mapel');

		// Set lebar kolom
		foreach (range('A', 'E') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Download sebagai file Excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="template_mapel.xlsx"');
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
				'kode_mapel' => $row[0],
				'nama'      => trim($row[1]),
			];
		}

		$this->model->insertBatch('mapel', $dataInsert);

		$this->session->set_flashdata('ok', 'Data mapel berhasil diupload.');
		redirect('mapel');
	}
}
