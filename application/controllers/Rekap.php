<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Rekap extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->model('Rekap_model');

		$this->mustLogin();
		$this->AdminOrSuper();
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
	}

	// Pembiasaan
	public function pembiasaan_guru()
	{
		$data['menu'] = 'rekap';
		$data['sub'] = 'pembiasaan_guru';

		$this->load->view('rekap/pembiasaan_guru', $data);
	}

	public function ajaxRekapApelGuru()
	{
		$dari   = $this->input->post('dari', true);
		$sampai = $this->input->post('sampai', true);

		// Validasi sederhana
		if (!$dari || !$sampai) {
			echo json_encode([
				'status' => false,
				'message' => 'Tanggal tidak lengkap'
			]);
			return;
		}

		// Ambil data dari model
		$data = $this->Rekap_model->getRekapApelGuru($dari, $sampai);

		echo json_encode([
			'status' => true,
			'data' => $data
		]);
	}

	public function export_excel()
	{
		$tgl_dari   = $this->input->post('tgl_dari');
		$tgl_sampai = $this->input->post('tgl_sampai');

		$rekap = $this->Rekap_model->getRekapApelGuru($tgl_dari, $tgl_sampai);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		/* ================= JUDUL ================= */
		$sheet->mergeCells('A1:I1');
		$sheet->setCellValue('A1', 'REKAP ABSENSI GURU');

		$sheet->mergeCells('A2:I2');
		$sheet->setCellValue(
			'A2',
			'Periode : ' . tanggal_indo($tgl_dari) . ' s/d ' . tanggal_indo($tgl_sampai)
		);

		$sheet->getStyle('A1:A2')->applyFromArray([
			'font' => ['bold' => true, 'size' => 13],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
		]);

		/* ================= HEADER ================= */
		$sheet->mergeCells('A4:A5')->setCellValue('A4', 'No');
		$sheet->mergeCells('B4:B5')->setCellValue('B4', 'Nama Guru');
		$sheet->mergeCells('C4:C5')->setCellValue('C4', 'Wajib Hadir');
		$sheet->mergeCells('D4:D5')->setCellValue('D4', 'Hadir');
		$sheet->mergeCells('E4:G4')->setCellValue('E4', 'Tidak Hadir');
		$sheet->mergeCells('H4:H5')->setCellValue('H4', 'Prosentase');

		$sheet->setCellValue('E5', 'Izin');
		$sheet->setCellValue('F5', 'Alpha');
		$sheet->setCellValue('G5', 'Izin + Alpha');

		$sheet->getStyle('A4:H5')->applyFromArray([
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'E5E7EB'],
			],
			'borders' => [
				'allBorders' => ['borderStyle' => Border::BORDER_THIN],
			],
		]);

		/* ================= DATA ================= */
		$row = 6;
		$no  = 1;

		foreach ($rekap as $r) {
			$tidakHadir = $r->izin + $r->alpha;
			$persen = $r->wajib > 0 ? round(($r->hadir / $r->wajib) * 100, 1) : 0;

			$sheet->setCellValue("A$row", $no++);
			$sheet->setCellValue("B$row", $r->nama_guru);
			$sheet->setCellValue("C$row", $r->wajib);
			$sheet->setCellValue("D$row", $r->hadir);
			$sheet->setCellValue("E$row", $r->izin);
			$sheet->setCellValue("F$row", $r->alpha);
			$sheet->setCellValue("G$row", $tidakHadir);
			$sheet->setCellValue("H$row", $persen . '%');

			// Border
			$sheet->getStyle("A$row:H$row")->getBorders()
				->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			// Center angka
			$sheet->getStyle("A$row:C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("D$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

			// Warna
			$sheet->getStyle("D$row")->getFont()->getColor()->setRGB('059669'); // Hadir
			$sheet->getStyle("E$row")->getFont()->getColor()->setRGB('2563EB'); // Izin
			$sheet->getStyle("F$row")->getFont()->getColor()->setRGB('DC2626'); // Alpha
			$sheet->getStyle("G$row")->getFont()->getColor()->setRGB('D97706'); // Total

			if ($persen >= 90) {
				$sheet->getStyle("H$row")->getFont()->getColor()->setRGB('059669');
			} elseif ($persen >= 75) {
				$sheet->getStyle("H$row")->getFont()->getColor()->setRGB('D97706');
			} else {
				$sheet->getStyle("H$row")->getFont()->getColor()->setRGB('DC2626');
			}

			$row++;
		}

		/* ================= AUTO WIDTH ================= */
		foreach (range('A', 'H') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		/* ================= OUTPUT ================= */
		$filename = 'Rekap_Absensi_Guru.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');

		(new Xlsx($spreadsheet))->save('php://output');
		exit;
	}

	public function kehadiran_guru()
	{
		$data['menu'] = 'rekap';
		$data['sub'] = 'kehadiran_guru';

		$this->load->view('rekap/kehadiran_guru', $data);
	}

	public function ajaxRekapHadirGuru()
	{
		$dari   = $this->input->post('dari', true);
		$sampai = $this->input->post('sampai', true);

		// Validasi sederhana
		if (!$dari || !$sampai) {
			echo json_encode([
				'status' => false,
				'message' => 'Tanggal tidak lengkap'
			]);
			return;
		}

		// Ambil data dari model
		$data = $this->Rekap_model->getRekapHadirGuru($dari, $sampai);

		echo json_encode([
			'status' => true,
			'data' => $data
		]);
	}

	public function export_excel_hadir()
	{
		$tgl_dari   = $this->input->post('tgl_dari');
		$tgl_sampai = $this->input->post('tgl_sampai');

		$rekap = $this->Rekap_model->getRekapHadirGuru($tgl_dari, $tgl_sampai);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		/* ================= JUDUL ================= */
		$sheet->mergeCells('A1:I1');
		$sheet->setCellValue('A1', 'REKAP ABSENSI KEHADIRAN GURU KE SEKOLAH');

		$sheet->mergeCells('A2:I2');
		$sheet->setCellValue(
			'A2',
			'Periode : ' . tanggal_indo($tgl_dari) . ' s/d ' . tanggal_indo($tgl_sampai)
		);

		$sheet->getStyle('A1:A2')->applyFromArray([
			'font' => ['bold' => true, 'size' => 13],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
		]);

		/* ================= HEADER ================= */
		$sheet->mergeCells('A4:A5')->setCellValue('A4', 'No');
		$sheet->mergeCells('B4:B5')->setCellValue('B4', 'Nama Guru');
		$sheet->mergeCells('C4:C5')->setCellValue('C4', 'Wajib Hadir');
		$sheet->mergeCells('D4:D5')->setCellValue('D4', 'Hadir');
		$sheet->mergeCells('E4:H4')->setCellValue('E4', 'Tidak Hadir');
		$sheet->mergeCells('I4:I5')->setCellValue('I4', 'Prosentase');

		$sheet->setCellValue('E5', 'Izin');
		$sheet->setCellValue('F5', 'Alpha');
		$sheet->setCellValue('G5', 'Cuti');
		$sheet->setCellValue('H5', 'Total');

		$sheet->getStyle('A4:I5')->applyFromArray([
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'E5E7EB'],
			],
			'borders' => [
				'allBorders' => ['borderStyle' => Border::BORDER_THIN],
			],
		]);

		/* ================= DATA ================= */
		$row = 6;
		$no  = 1;

		foreach ($rekap as $r) {
			$tidakHadir = $r->izin + $r->alpha;
			$persen = $r->wajib > 0 ? round(($r->hadir / $r->wajib) * 100, 1) : 0;

			$sheet->setCellValue("A$row", $no++);
			$sheet->setCellValue("B$row", $r->nama_guru);
			$sheet->setCellValue("C$row", $r->wajib);
			$sheet->setCellValue("D$row", $r->hadir);
			$sheet->setCellValue("E$row", $r->izin);
			$sheet->setCellValue("F$row", $r->alpha);
			$sheet->setCellValue("G$row", $r->cuti);
			$sheet->setCellValue("H$row", $r->jml_tidak_hadir);
			$sheet->setCellValue("I$row", $persen . '%');

			// Border
			$sheet->getStyle("A$row:I$row")->getBorders()
				->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			// Center angka
			$sheet->getStyle("A$row:C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("D$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

			// Warna
			$sheet->getStyle("D$row")->getFont()->getColor()->setRGB('059669'); // Hadir
			$sheet->getStyle("E$row")->getFont()->getColor()->setRGB('2563EB'); // Izin
			$sheet->getStyle("F$row")->getFont()->getColor()->setRGB('DC2626'); // Alpha
			$sheet->getStyle("G$row")->getFont()->getColor()->setRGB('E20AFF'); // Cuti
			$sheet->getStyle("H$row")->getFont()->getColor()->setRGB('D97706'); // Total

			if ($persen >= 90) {
				$sheet->getStyle("I$row")->getFont()->getColor()->setRGB('059669');
			} elseif ($persen >= 75) {
				$sheet->getStyle("I$row")->getFont()->getColor()->setRGB('D97706');
			} else {
				$sheet->getStyle("I$row")->getFont()->getColor()->setRGB('DC2626');
			}

			$row++;
		}

		/* ================= AUTO WIDTH ================= */
		foreach (range('A', 'H') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		/* ================= OUTPUT ================= */
		$filename = 'Rekap_Absensi_Kehadiran_Guru.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');

		(new Xlsx($spreadsheet))->save('php://output');
		exit;
	}

	public function jam_mengajar()
	{
		$data['menu'] = 'rekap';
		$data['sub'] = 'jam_mengajar';

		$this->load->view('rekap/jam_mengajar', $data);
	}
	public function ajaxRekapMengajarGuru()
	{
		$dari   = $this->input->post('dari', true);
		$sampai = $this->input->post('sampai', true);

		// Validasi sederhana
		if (!$dari || !$sampai) {
			echo json_encode([
				'status' => false,
				'message' => 'Tanggal tidak lengkap'
			]);
			return;
		}

		// Ambil data dari model
		$data = $this->Rekap_model->getRekapMengajarGuru($dari, $sampai);

		echo json_encode([
			'status' => true,
			'data' => $data
		]);
	}

	public function export_excel_mengajar()
	{
		$tgl_dari   = $this->input->post('tgl_dari');
		$tgl_sampai = $this->input->post('tgl_sampai');

		$rekap = $this->Rekap_model->getRekapMengajarGuru($tgl_dari, $tgl_sampai);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		/* ================= JUDUL ================= */
		$sheet->mergeCells('A1:K1');
		$sheet->setCellValue('A1', 'REKAP ABSENSI KEHADIRAN MENGAJAR GURU');

		$sheet->mergeCells('A2:K2');
		$sheet->setCellValue(
			'A2',
			'Periode : ' . tanggal_indo($tgl_dari) . ' s/d ' . tanggal_indo($tgl_sampai)
		);

		$sheet->getStyle('A1:A2')->applyFromArray([
			'font' => ['bold' => true, 'size' => 13],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
		]);

		/* ================= HEADER ================= */
		$sheet->mergeCells('A4:A5')->setCellValue('A4', 'No');
		$sheet->mergeCells('B4:B5')->setCellValue('B4', 'Nama Guru');
		$sheet->mergeCells('C4:C5')->setCellValue('C4', 'Wajib Hadir');
		$sheet->mergeCells('D4:D5')->setCellValue('D4', 'Hadir');
		$sheet->mergeCells('E4:H4')->setCellValue('E4', 'Tidak Hadir');
		$sheet->mergeCells('K4:K5')->setCellValue('K4', 'Prosentase');

		$sheet->setCellValue('E5', 'Izin');
		$sheet->setCellValue('F5', 'Telat');
		$sheet->setCellValue('G5', 'Sakit');
		$sheet->setCellValue('H5', 'Alpha');
		$sheet->setCellValue('I5', 'Cuti');
		$sheet->setCellValue('J5', 'Total');

		$sheet->getStyle('A4:K5')->applyFromArray([
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'E5E7EB'],
			],
			'borders' => [
				'allBorders' => ['borderStyle' => Border::BORDER_THIN],
			],
		]);

		/* ================= DATA ================= */
		$row = 6;
		$no  = 1;

		foreach ($rekap as $r) {
			$persen = $r->wajib > 0 ? round(($r->hadir / $r->wajib) * 100, 1) : 0;

			$sheet->setCellValue("A$row", $no++);
			$sheet->setCellValue("B$row", $r->nama_guru);
			$sheet->setCellValue("C$row", $r->wajib);
			$sheet->setCellValue("D$row", $r->hadir);
			$sheet->setCellValue("E$row", $r->izin);
			$sheet->setCellValue("F$row", $r->telat);
			$sheet->setCellValue("G$row", $r->sakit);
			$sheet->setCellValue("H$row", $r->alpha);
			$sheet->setCellValue("I$row", $r->cuti);
			$sheet->setCellValue("J$row", $r->jml_tidak_hadir);
			$sheet->setCellValue("K$row", $persen . '%');

			// Border
			$sheet->getStyle("A$row:K$row")->getBorders()
				->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			// Center angka
			$sheet->getStyle("A$row:C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("D$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

			// Warna
			$sheet->getStyle("D$row")->getFont()->getColor()->setRGB('059669'); // Hadir
			$sheet->getStyle("E$row")->getFont()->getColor()->setRGB('2563EB'); // Izin
			$sheet->getStyle("F$row")->getFont()->getColor()->setRGB('BD5204'); // Sakit
			$sheet->getStyle("G$row")->getFont()->getColor()->setRGB('010663'); // Telat
			$sheet->getStyle("H$row")->getFont()->getColor()->setRGB('DC2626'); // Alpha
			$sheet->getStyle("I$row")->getFont()->getColor()->setRGB('E20AFF'); // Cuti
			$sheet->getStyle("J$row")->getFont()->getColor()->setRGB('D97706'); // Total

			if ($persen >= 90) {
				$sheet->getStyle("K$row")->getFont()->getColor()->setRGB('059669');
			} elseif ($persen >= 75) {
				$sheet->getStyle("K$row")->getFont()->getColor()->setRGB('D97706');
			} else {
				$sheet->getStyle("K$row")->getFont()->getColor()->setRGB('DC2626');
			}

			$row++;
		}

		/* ================= AUTO WIDTH ================= */
		foreach (range('A', 'K') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		/* ================= OUTPUT ================= */
		$filename = 'Rekap_Absensi_Emngajar_Guru.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');

		(new Xlsx($spreadsheet))->save('php://output');
		exit;
	}

	public function kbm_siswa()
	{
		$data['menu'] = 'rekap';
		$data['sub'] = 'kbm_siswa';

		$this->load->view('rekap/kbm_siswa', $data);
	}

	public function export_kbm_siswa()
	{
		// $dtlAbsen = $this->model->getBy('absensi', 'id_absen', $id)->row();
		$dari = $this->input->post('tgl_dari', TRUE);
		$sampai = $this->input->post('tgl_sampai', TRUE);

		$spreadsheet = new Spreadsheet();

		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_header = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
		];
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];
		$style_row_center = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Set text jadi di tengah secara vertical (middle)
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		$dataKelas = $this->db->query("SELECT * FROM kelas WHERE id_lembaga = '$this->id_lembaga' ORDER BY nama ASC")->result();
		foreach ($dataKelas as $dtsk) {

			$sheet = $spreadsheet->createSheet();

			$nmKelas = $dtsk->nama;

			$sheet->mergeCells('A1:G1')->setCellValue('A1', "REKAP ABSENSI SISWA")->getStyle('A1')->applyFromArray($style_header); // Set kolom A1 dengan tulisan "DATA SISWA"

			$sheet->mergeCells('A2:G2')->setCellValue('A2', "SMK DARUL LUGHAH WAL KAROMAH")->getStyle('A2')->applyFromArray($style_header); // Set kolom A1 dengan tulisan "DATA SISWA"

			$sheet->mergeCells('A3:G3')->setCellValue('A3', ""); // Set kolom A1 dengan tulisan "DATA SISWA"

			$sheet->mergeCells('A4:G4')->setCellValue('A4', "Minggu : ")->getStyle('A4')->getFont()->setBold(true);;

			$sheet->mergeCells('A5:G5')->setCellValue('A5', "Bulan : " . cariBulan([$dari, $sampai]))->getStyle('A5')->getFont()->setBold(true);;

			$sheet->mergeCells('A6:G6')->setCellValue('A6', "Rentang : " . tanggal_indo($dari) . ' s/d ' . tanggal_indo($sampai))->getStyle('A6')->getFont()->setBold(true);;

			$sheet->mergeCells('A7:G7')->setCellValue('A7', ""); // Set kolom A1 dengan tulisan "DATA SISWA"

			$spreadsheet->getActiveSheet()->getStyle('A8:G8')->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('F7EF00');

			// Buat header tabel nya pada baris ke 3
			$sheet->setCellValue('A8', "NO");
			$sheet->setCellValue('B8', "NAMA");
			$sheet->setCellValue('C8', "KELAS");
			$sheet->setCellValue('D8', "SAKIT");
			$sheet->setCellValue('E8', "IZIN");
			$sheet->setCellValue('F8', "ALPHA");
			$sheet->setCellValue('G8', "KET");


			// Apply style header yang telah kita buat tadi ke masing-masing kolom header

			$sheet->getStyle('A8')->applyFromArray($style_col);
			$sheet->getStyle('B8')->applyFromArray($style_col);
			$sheet->getStyle('C8')->applyFromArray($style_col);
			$sheet->getStyle('D8')->applyFromArray($style_col);
			$sheet->getStyle('E8')->applyFromArray($style_col);
			$sheet->getStyle('F8')->applyFromArray($style_col);
			$sheet->getStyle('G8')->applyFromArray($style_col);


			$absn = $this->db->query(
				"
					SELECT 
						h.id_siswa,
						SUM(h.sakit) AS sakitAll,
						SUM(h.izin)  AS izinAll,
						SUM(h.alpha) AS alphaAll
					FROM harian h
					WHERE 
						h.id_kelas = ?
						AND h.id_lembaga = ?
						AND h.tanggal >= ?
						AND h.tanggal <= ?
					GROUP BY h.id_siswa
				",
				[
					$dtsk->id_kelas,
					$this->id_lembaga,
					$dari,
					$sampai
				]
			)->result();

			$no = 1; // Untuk penomoran tabel, di awal set dengan 1
			$numrow = 9; // Set baris pertama untuk isi tabel adalah baris ke 4
			foreach ($absn as $data) { // Lakukan looping pada variabel siswa
				$siswaDtl = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$data->id_siswa'")->row();
				$sheet->setCellValue('A' . $numrow, $no);
				$sheet->setCellValue('B' . $numrow, $siswaDtl->nama);
				$sheet->setCellValue('C' . $numrow, $nmKelas);
				$sheet->setCellValue('D' . $numrow, $data->sakitAll);
				$sheet->setCellValue('E' . $numrow, $data->izinAll);
				$sheet->setCellValue('F' . $numrow, $data->alphaAll);
				$sheet->setCellValue('G' . $numrow, '');


				// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
				$sheet->getStyle('A' . $numrow)->applyFromArray($style_row_center);
				$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
				$sheet->getStyle('C' . $numrow)->applyFromArray($style_row_center);
				$sheet->getStyle('D' . $numrow)->applyFromArray($style_row_center);
				$sheet->getStyle('E' . $numrow)->applyFromArray($style_row_center);
				$sheet->getStyle('F' . $numrow)->applyFromArray($style_row_center);
				$sheet->getStyle('G' . $numrow)->applyFromArray($style_row);

				$sheet->getStyle('D')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB(colorCell($data->sakitAll));
				$sheet->getStyle('E')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB(colorCell($data->izinAll));
				$sheet->getStyle('F')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB(colorCell($data->alphaAll));


				$no++; // Tambah 1 setiap kali looping
				$numrow++; // Tambah 1 setiap kali looping
			}

			// $sheet = $spreadsheet->getActiveSheet();
			foreach ($sheet->getColumnIterator() as $column) {
				$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
			}

			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$sheet->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$sheet->setTitle($nmKelas);
		}

		$fileName = 'Rekap absensi tanggal ' . tanggal_indo($dari) . ' s/d ' . tanggal_indo($sampai);

		// Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename=' . $fileName . '.xlsx'); // Set nama file excel nya
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}
