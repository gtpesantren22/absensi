<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends MY_Controller
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
		$data['judul'] = 'Data Guru';
		$data['menu'] = 'master';
		$data['sub'] = 'guru';

		$this->load->view('admin/guru', $data);
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
		$this->db->from('registrasi');
		$this->db->join('guru', 'registrasi.id_guru = guru.id_guru');
		$this->db->where('registrasi.id_lembaga', $this->id_lembaga);

		if (!empty($search)) {
			$this->db->group_start()
				->like('guru.kode_guru', $search)
				->or_like('guru.nama', $search)
				->or_like('guru.jkl', $search)
				->or_like('guru.no_hp', $search)
				->group_end();
		}

		$this->db->select('COUNT(*) as total');

		$total = $this->db->get()->row()->total;

		/* ================= DATA ================= */
		$this->db->select('guru.*');
		$this->db->from('registrasi');
		$this->db->join('guru', 'registrasi.id_guru = guru.id_guru');

		$this->db->where('registrasi.id_lembaga', $this->id_lembaga);

		if (!empty($search)) {
			$this->db->group_start()
				->like('guru.kode_guru', $search)
				->or_like('guru.nama', $search)
				->or_like('guru.jkl', $search)
				->or_like('guru.no_hp', $search)
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
		$kode_guru    = $this->input->post('kode_guru', true);
		$nama         = $this->input->post('nama', true);
		$no_hp        = $this->input->post('no_hp', true);
		$jenis_kelamin = $this->input->post('jenis_kelamin', true);
		$warna		= $this->input->post('warna', true);

		$data = [
			'id_guru'    => $this->uuid->v4(),
			'kode_guru'    => $kode_guru,
			'nama'         => $nama,
			'no_hp'        => $no_hp,
			'jkl' => $jenis_kelamin,
			'warna'		=> $warna,
		];

		$sql = $this->model->tambah('guru', $data);

		if ($sql) {
			$this->session->set_flashdata('ok', 'Data guru berhasil ditambahkan.');
		} else {
			$this->session->set_flashdata('error', 'Data guru gagal ditambahkan.');
		}
		redirect('guru');
	}

	public function hapus()
	{
		$id = $this->input->post('id', true);
		$sql = $this->db
			->where('id_guru', $id)
			->where('id_lembaga', $this->id_lembaga)
			->delete('registrasi');

		if (!$sql) {
			$this->session->set_flashdata('error', 'Data guru gagal dihapus.');
			redirect('guru');
		} else {
			$this->session->set_flashdata('ok', 'Data guru berhasil dihapus.');
			redirect('guru');
		}
	}

	// public function getById($id)
	// {
	// 	$data = $this->model->getBy('guru', 'id_guru', $id)->row_array();

	// 	$this->output
	// 		->set_content_type('application/json')
	// 		->set_output(json_encode($data));
	// }

	// public function update($id)
	// {
	// 	$id_guru    = $id;
	// 	$kode_guru    = $this->input->post('kode_guru', true);
	// 	$nama         = $this->input->post('nama', true);
	// 	$no_hp        = $this->input->post('no_hp', true);
	// 	$jenis_kelamin = $this->input->post('jenis_kelamin', true);
	// 	$warna		= $this->input->post('warna', true);

	// 	$data = [
	// 		'kode_guru'    => $kode_guru,
	// 		'nama'         => $nama,
	// 		'no_hp'        => $no_hp,
	// 		'jkl' => $jenis_kelamin,
	// 		'warna'		=> $warna,
	// 	];

	// 	$sql = $this->model->edit('guru', 'id_guru', $id_guru, $data);

	// 	if (!$sql) {
	// 		$this->session->set_flashdata('error', 'Data guru gagal diupdate.');
	// 		redirect('guru');
	// 	} else {
	// 		$this->session->set_flashdata('ok', 'Data guru berhasil diupdate.');
	// 		redirect('guru');
	// 	}
	// }

	// public function downloadTemplate()
	// {
	// 	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->setTitle('Template Guru');

	// 	// Header kolom
	// 	$sheet->setCellValue('A1', 'Kode Guru');
	// 	$sheet->setCellValue('B1', 'Nama');
	// 	$sheet->setCellValue('C1', 'Jenis Kelamin (L/P)');
	// 	$sheet->setCellValue('D1', 'No HP');
	// 	$sheet->setCellValue('E1', 'Warna (optional)');

	// 	// Set lebar kolom
	// 	foreach (range('A', 'E') as $col) {
	// 		$sheet->getColumnDimension($col)->setAutoSize(true);
	// 	}

	// 	// Download sebagai file Excel
	// 	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// 	header('Content-Disposition: attachment;filename="template_guru.xlsx"');
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
	// 			'id_guru'   => $this->uuid->v4(),
	// 			'kode_guru' => $row[0],
	// 			'nama'      => trim($row[1]),
	// 			'jkl'       => $row[2] == 'L' ? 'Laki-laki' : 'Perempuan',
	// 			'no_hp'     => trim($row[3]),
	// 			'warna'     => trim($row[4]) ?: 'blue',
	// 		];
	// 	}

	// 	$this->model->insertBatch('guru', $dataInsert);

	// 	$this->session->set_flashdata('ok', 'Data guru berhasil diupload.');
	// 	redirect('guru');
	// }

	public function generate_warna_all()
	{
		$this->mustLogin();
		$this->AdminOrSuper();

		// Fetch all teachers registered in this lembaga
		$this->db->select('guru.id_guru, guru.nama');
		$this->db->from('registrasi');
		$this->db->join('guru', 'registrasi.id_guru = guru.id_guru');
		$this->db->where('registrasi.id_lembaga', $this->id_lembaga);
		$gurus = $this->db->get()->result_array();

		$count = count($gurus);
		if ($count === 0) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'Tidak ada data guru']));
			return;
		}

		$colors = [];
		$golden_ratio_conjugate = 0.618033988749895;
		$h = mt_rand(0, 1000) / 1000;

		for ($i = 0; $i < $count; $i++) {
			$h += $golden_ratio_conjugate;
			$h = fmod($h, 1.0);
			$colors[] = $this->hslToHex($h, 0.65, 0.5);
		}

		shuffle($colors);

		$this->db->trans_start();
		foreach ($gurus as $index => $g) {
			$this->db->where('id_guru', $g['id_guru'])->update('guru', ['warna' => $colors[$index]]);
		}
		$this->db->trans_complete();

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(['status' => true, 'msg' => 'Berhasil men-generate warna unik untuk semua guru']));
	}

	public function generate_warna_single()
	{
		$this->mustLogin();
		$this->AdminOrSuper();

		$id = $this->input->post('id');

		if (empty($id)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'ID guru tidak ditemukan']));
			return;
		}

		// Generate random hex color
		$h = mt_rand(0, 1000) / 1000;
		$color = $this->hslToHex($h, 0.65, 0.5);

		$this->db->where('id_guru', $id)->update('guru', ['warna' => $color]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(['status' => true, 'warna' => $color, 'msg' => 'Warna berhasil di-generate']));
	}

	private function hslToHex($h, $s, $l)
	{
		$r = $g = $b = $l;
		$v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
		if ($v > 0) {
			$m = $l + $l - $v;
			$sv = ($v - $m) / $v;
			$h *= 6.0;
			$sextant = floor($h);
			$fract = $h - $sextant;
			$vsf = $v * $sv * $fract;
			$mid1 = $m + $vsf;
			$mid2 = $v - $vsf;

			switch ($sextant) {
				case 0: $r = $v; $g = $mid1; $b = $m; break;
				case 1: $r = $mid2; $g = $v; $b = $m; break;
				case 2: $r = $m; $g = $v; $b = $mid1; break;
				case 3: $r = $m; $g = $mid2; $b = $v; break;
				case 4: $r = $mid1; $g = $m; $b = $v; break;
				case 5: $r = $v; $g = $m; $b = $mid2; break;
			}
		}

		return sprintf("#%02x%02x%02x", round($r * 255), round($g * 255), round($b * 255));
	}
}
