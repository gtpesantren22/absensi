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

		$this->mustLogin();
		$this->AdminOrSuper();
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
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


		/* ================= BASE QUERY ================= */
		$this->db->from('kelas');

		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('jenis', $search)
				->group_end();
		}

		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');

		/* ================= TOTAL ================= */
		$this->db->where('kelas.id_lembaga', $this->id_lembaga);
		$this->db->where('kelas.id_tahun', $id_tahun_aktif);
		$total = $this->db->count_all_results('', false);

		/* ================= DATA ================= */
		$this->db->select('
			kelas.*,
			COUNT(rombel.id_siswa) AS jumlah_anggota
		');

		$this->db->join('rombel', 'rombel.id_kelas = kelas.id_kelas', 'left');
		$this->db->where('kelas.id_lembaga', $this->id_lembaga);
		$this->db->where('kelas.id_tahun', $id_tahun_aktif);

		$this->db->group_by('kelas.id_kelas');
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
		$jenis        = $this->input->post('jenis', true);

		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');

		$data = [
			'nama'         => $nama,
			'jenis'        => $jenis,
			'id_lembaga'   => $this->id_lembaga,
			'id_tahun'     => $id_tahun_aktif
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

		$this->db->trans_start();
		$this->db->where('id_kelas', $id)->delete('rombel');
		$this->db->where('id_kelas', $id)->delete('kelas');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Data kelas gagal dihapus.');
			redirect('kelas');
		} else {
			$this->session->set_flashdata('ok', 'Data kelas berhasil dihapus beserta anggotanya.');
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
		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');

		foreach ($sheetData as $i => $row) {
			if ($i == 0) continue; // skip header
			if (empty($row[1])) continue;

			$dataInsert[] = [
				'nama' => $row[0],
				'jenis'      => trim($row[1]),
				'id_lembaga' => $this->id_lembaga,
				'id_tahun'   => $id_tahun_aktif
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
		// $result = $this->Model_guru->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');
		$id_kelas = $this->input->get('id_kelas') ?? '';

		$cekKelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();

		$offset = ($page - 1) * $perPage;

		/* ================= BASE QUERY ================= */
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa');

		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nisn', $search)
				->or_like('siswa.nama', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}

		if ($cekKelas->jenis == 'Utama') {
			$this->db->where('registrasi_siswa.id_lembaga', $this->id_lembaga);
		}

		/* ================= TOTAL ================= */
		$this->db->select('COUNT(*) AS total');
		$total = $this->db->get()->row()->total;

		/* ================= DATA ================= */
		$this->db->select('siswa.*');
		$this->db->from('registrasi_siswa');
		$this->db->join('siswa', 'registrasi_siswa.id_siswa = siswa.id_siswa');

		if (!empty($search)) {
			$this->db->group_start()
				->like('siswa.nisn', $search)
				->or_like('siswa.nama', $search)
				->or_like('siswa.jkl', $search)
				->or_like('siswa.alamat', $search)
				->group_end();
		}

		if ($cekKelas->jenis == 'Utama') {
			$this->db->where('registrasi_siswa.id_lembaga', $this->id_lembaga);
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

	public function dataRombel($id_kelas)
	{
		$data = $this->db
			->select('rombel.id_rombel, siswa.nama, siswa.nisn')
			->from('rombel')
			->join('siswa', 'siswa.id_siswa = rombel.id_siswa')
			->where('rombel.id_kelas', $id_kelas)
			->order_by('siswa.nama', 'ASC')
			->get()
			->result_array();

		return $this->output
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

		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
		$data = [
			'id_kelas' => $id_kelas,
			'id_siswa' => $id_siswa,
			'id_lembaga' => $this->id_lembaga,
			'id_tahun' => $id_tahun_aktif
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
		$sql = $this->db->where('id_kelas', $id_kelas)->delete('rombel');

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
		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');

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
					'id_lembaga' => $this->id_lembaga,
					'id_tahun' => $id_tahun_aktif
				];
			}
		}

		$this->model->insertBatch('rombel', $dataInsert);

		$this->session->set_flashdata('ok', 'Data anggota kelas berhasil diupload.');
		redirect('kelas/anggota/' . $id_kelas);
	}

	public function reset()
	{
		$this->mustLogin();
		$this->AdminOrSuper();

		$password = $this->input->post('password');

		if (empty($password)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'Password wajib diisi!']));
			return;
		}

		// Verify password
		$user = $this->db->get_where('user', ['id_user' => $this->iduser])->row();
		if (!$user || !password_verify($password, $user->password)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'Password salah!']));
			return;
		}

		// Delete all classes and their rombel members for this institution and year
		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
		$this->db->trans_start();

		$this->db->query("
			DELETE FROM rombel 
			WHERE id_kelas IN (
				SELECT id_kelas FROM kelas WHERE id_lembaga = ? AND id_tahun = ?
			)
		", [$this->id_lembaga, $id_tahun_aktif]);

		$this->db->where('id_lembaga', $this->id_lembaga)
			->where('id_tahun', $id_tahun_aktif)
			->delete('kelas');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'Gagal mereset data kelas!']));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => true, 'msg' => 'Semua data kelas dan anggotanya berhasil di-reset!']));
		}
	}

	public function salin_struktur_kelas()
	{
		$id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
		
		// Find another year before the active year
		$prev_year = $this->db->query("SELECT id_tahun FROM tahun_ajaran WHERE id_tahun < ? ORDER BY id_tahun DESC LIMIT 1", [$id_tahun_aktif])->row();
		if (!$prev_year) {
			$this->session->set_flashdata('error', 'Tahun ajaran sebelum ini tidak ditemukan!');
			redirect('kelas');
		}

		// Fetch all classes of previous year
		$classes = $this->db->get_where('kelas', ['id_lembaga' => $this->id_lembaga, 'id_tahun' => $prev_year->id_tahun])->result();
		if (empty($classes)) {
			$this->session->set_flashdata('error', 'Tidak ada data kelas tahun lalu untuk disalin!');
			redirect('kelas');
		}

		$this->db->trans_start();
		foreach ($classes as $c) {
			// Check if class with same name already exists in active year
			$exists = $this->db->get_where('kelas', [
				'nama' => $c->nama,
				'id_lembaga' => $this->id_lembaga,
				'id_tahun' => $id_tahun_aktif
			])->row();
			if (!$exists) {
				$this->db->insert('kelas', [
					'nama' => $c->nama,
					'jenis' => $c->jenis,
					'id_lembaga' => $this->id_lembaga,
					'id_tahun' => $id_tahun_aktif
				]);
			}
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal menyalin kelas!');
		} else {
			$this->session->set_flashdata('ok', 'Berhasil menyalin struktur kelas dari tahun lalu.');
		}
		redirect('kelas');
	}

	public function kenaikan()
	{
		$data['judul'] = 'Kenaikan Kelas';
		$data['menu'] = 'master';
		$data['sub'] = 'kenaikan_kelas';

		$data['tahun_ajaran'] = $this->db->order_by('nama_tahun', 'DESC')->get('tahun_ajaran')->result();
		
		$this->load->view('admin/kenaikan_kelas', $data);
	}

	public function get_kelas_by_tahun($id_tahun)
	{
		$kelas = $this->db->get_where('kelas', [
			'id_lembaga' => $this->id_lembaga,
			'id_tahun' => $id_tahun
		])->result_array();
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($kelas));
	}

	public function get_siswa_by_kelas($id_kelas)
	{
		$siswa = $this->db
			->select('siswa.id_siswa, siswa.nama, siswa.nisn, rombel.id_rombel')
			->from('rombel')
			->join('siswa', 'siswa.id_siswa = rombel.id_siswa')
			->where('rombel.id_kelas', $id_kelas)
			->order_by('siswa.nama', 'ASC')
			->get()
			->result_array();
			
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($siswa));
	}

	public function proses_kenaikan()
	{
		$id_tahun_tujuan = $this->input->post('id_tahun_tujuan', true);
		$id_kelas_tujuan = $this->input->post('id_kelas_tujuan', true);
		$id_siswa_list   = $this->input->post('id_siswa', true); // array

		if (empty($id_tahun_tujuan) || empty($id_kelas_tujuan) || empty($id_siswa_list)) {
			$this->session->set_flashdata('error', 'Mohon pilih kelas tujuan dan siswa yang akan dinaikkan kelas!');
			redirect('kelas/kenaikan');
		}

		$this->db->trans_start();
		$insert_data = [];
		foreach ($id_siswa_list as $id_siswa) {
			// Check if student is already in a class in the target year to prevent double addition
			$exists = $this->db->get_where('rombel', [
				'id_siswa' => $id_siswa,
				'id_tahun' => $id_tahun_tujuan,
				'id_lembaga' => $this->id_lembaga
			])->row();

			if (!$exists) {
				$insert_data[] = [
					'id_kelas' => $id_kelas_tujuan,
					'id_siswa' => $id_siswa,
					'id_lembaga' => $this->id_lembaga,
					'id_tahun' => $id_tahun_tujuan
				];
			}
		}

		if (!empty($insert_data)) {
			$this->db->insert_batch('rombel', $insert_data);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal memproses kenaikan kelas!');
		} else {
			$this->session->set_flashdata('ok', 'Kenaikan kelas berhasil diproses untuk ' . count($insert_data) . ' siswa.');
		}
		redirect('kelas/kenaikan');
	}
}
