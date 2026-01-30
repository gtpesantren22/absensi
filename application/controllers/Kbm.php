<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Kbm extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');
		$this->load->library('Dynamic_db'); // load dulu
		$this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

		$this->mustLogin();

		$this->id_user = $this->session->userdata('id_user');
		$this->iduser = $this->session->userdata('id_user');
		$usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
		$this->id_lembaga = $usrdtl->id_lembaga;
	}

	public function control()
	{
		$this->onlyPiket();

		$data['judul'] = 'Data KBM';
		$data['menu'] = 'absensisiswa';
		$data['sub'] = 'kbm';

		$days = date('l');
		$data['kelas'] = $this->db->query("SELECT id_kelas FROM jadwal WHERE hari = '$days' AND id_lembaga = '$this->id_lembaga' GROUP BY id_kelas ORDER BY id_kelas ASC ");
		$data['harini'] = $days;
		$data['dateDays'] = date('Y-m-d');

		$this->load->view('absensi/kbm', $data);
	}

	public function absensi()
	{
		$data['judul'] = 'Data KBM';
		$data['menu'] = 'kbm';
		$data['sub'] = 'kbm';

		$days = date('l');
		$userData = $this->db->query("SELECT * FROM user WHERE id_user = '$this->id_user' ")->row();
		$data['guru'] = $this->db->query("SELECT * FROM guru WHERE id_guru = '$userData->id_guru' ")->row();

		$kls = [];
		$jdwal = $this->db->query("SELECT * FROM jadwal WHERE hari = '$days' AND id_guru = '$userData->id_guru' ORDER BY jam_dari ASC ")->result();
		foreach ($jdwal as $jdwl) {
			$dtl = $this->db->query("SELECT * FROM jadwal_dtl WHERE id_jadwal = '$jdwl->id_jadwal' ")->row();
			$kls[] = [
				'id_jadwal' => $jdwl->id_jadwal,
				'kelas' => $dtl->id_kelas,
				'jam_dari' => $jdwl->jam_dari,
				'jam_sampai' => $jdwl->jam_sampai,
			];
		}
		$data['kelas'] = $kls;


		$this->load->view('absensi/kbm_siswa', $data);
	}

	public function cariKelas()
	{
		$idJadwal = $this->input->post('id_jadwal', true);
		$jadwal = $this->db->query("SELECT * FROM jadwal WHERE id_jadwal = '$idJadwal' ")->row();
		$mapel = $this->model->getBy('mapel', 'id_mapel', $jadwal->id_mapel)->row();

		$dyas = date('l');

		$listdata = $this->model->query("SELECT * FROM rombel WHERE id_kelas = $jadwal->id_kelas ");
		$mapel = $this->model->getBy('mapel', 'id_mapel', $jadwal->id_mapel)->row();

		echo '
		<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-800">

			<!-- Hidden data -->
			<input type="hidden" name="guru" value="' . $jadwal->id_guru . '">
			<input type="hidden" name="mapel" value="' . $jadwal->id_mapel . '">
			<input type="hidden" name="kelas" value="' . $jadwal->id_kelas . '">
			<input type="hidden" name="dari" value="' . $jadwal->jam_dari . '">
			<input type="hidden" name="sampai" value="' . $jadwal->jam_sampai . '">

			<table class="min-w-full border-collapse text-sm text-slate-700 dark:text-slate-200">
				<tbody>

					<!-- Mapel -->
					<tr>
						<td colspan="2" class="bg-sky-600 dark:bg-sky-700 px-4 py-3 font-semibold text-white">
							Mapel : ' . $mapel->nama . '
						</td>
					</tr>

					<!-- Jam -->
					<tr>
						<td colspan="2" class="bg-sky-600 dark:bg-sky-700 px-4 py-2 font-semibold text-white">
							Jam ke : ' . $jadwal->jam_dari . ' - ' . $jadwal->jam_sampai . '
						</td>
					</tr>';
		$no = 1;
		foreach ($listdata->result() as $row) :
			$siswadt = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$row->id_siswa' ")->row();
			echo '
					<tr class="border-b border-gray-200 dark:border-slate-700 
							hover:bg-gray-50 dark:hover:bg-slate-700/50">

						<!-- Nama -->
						<td class="px-4 py-2 font-medium text-gray-700 dark:text-slate-200">
							' . $siswadt->nama . '
						</td>

						<!-- Absensi -->
						<td class="px-4 py-1">
							<input type="hidden" name="data[' . $no . '][id_siswa]" value="' . $row->id_siswa . '">

							<div class="flex flex-wrap gap-2">

								<!-- Hadir -->
								<label class="cursor-pointer">
									<input type="radio" name="data[' . $no . '][ket]" value="hadir" checked class="peer hidden">
									<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-emerald-500 peer-checked:text-white">
										H
									</span>
								</label>

								<!-- Sakit -->
								<label class="cursor-pointer">
									<input type="radio" name="data[' . $no . '][ket]" value="sakit" class="peer hidden">
									<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-yellow-400 peer-checked:text-white">
										S
									</span>
								</label>

								<!-- Izin -->
								<label class="cursor-pointer">
									<input type="radio" name="data[' . $no . '][ket]" value="izin" class="peer hidden">
									<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-blue-500 peer-checked:text-white">
										I
									</span>
								</label>

								<!-- Alpha -->
								<label class="cursor-pointer">
									<input type="radio" name="data[' . $no . '][ket]" value="alpha" class="peer hidden">
									<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-red-500 peer-checked:text-white">
										A
									</span>
								</label>

							</div>
						</td>

					</tr>';

			$no++;
		endforeach;
		echo '
					</tbody>
				</table>
			</div>';
	}

	public function save_multiple_data()
	{
		$guru = $this->input->post('guru', true);
		$mapel = $this->input->post('mapel', true);
		$dari = $this->input->post('dari', true);
		$sampai = $this->input->post('sampai', true);
		$data = $this->input->post('data', true);
		$kelas = $this->input->post('kelas', true);
		$isi = $this->input->post('isi', true);
		$kode = $this->uuid->v4();
		$tanggal = date('Y-m-d');

		$jmlAbs = ($sampai - $dari) + 1;

		$cek = $this->model->getBy5('harian', 'id_guru', $guru, 'id_mapel', $mapel, 'id_kelas', $kelas, 'tanggal', $tanggal, 'dari', $dari)->row();

		$nmGuru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$guru' ")->row();
		$nmMapel = $this->model->getBy('mapel', 'id_mapel', $mapel)->row();
		$nmkelas = $this->model->getBy('kelas', 'id_kelas', $kelas)->row();

		if ($cek) {
			$this->session->set_flashdata('error', 'Absensi sudah ada. Jika ada kelasahan silahkan dihapus atau diupdate kembali');
			redirect('kbm/absensi');
		} else {
			if (!empty($data)) {
				foreach ($data as $item) {
					$idsw = $item['id_siswa'];
					$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$idsw' ")->row();

					if ($item['ket'] == 'alpha') {
						$sakit = 0;
						$izin = 0;
						$alpha = $jmlAbs;
					} elseif ($item['ket'] == 'sakit') {
						$sakit = $jmlAbs;
						$izin = 0;
						$alpha = 0;
					} elseif ($item['ket'] == 'izin') {
						$sakit = 0;
						$izin = $jmlAbs;
						$alpha = 0;
					} else {
						$sakit = 0;
						$izin = 0;
						$alpha = 0;
					}

					$dtsm = [
						'kode' => $kode,
						'tanggal' => $tanggal,
						'id_kelas' => $kelas,
						'id_mapel' => $mapel,
						'id_guru' => $guru,
						'dari' => $dari,
						'sampai' => $sampai,
						'id_siswa' => $item['id_siswa'],
						'ket' => $item['ket'],
						'sakit' => $sakit,
						'izin' => $izin,
						'alpha' => $alpha,

						'kelas' => $nmkelas->nama,
						'mapel' => $nmMapel->nama,
						'guru' => $nmGuru->nama,
						'nama_siswa' => $nmsiswa->nama
					];
					$sql = $this->model->tambah('harian', $dtsm);
				}

				if ($sql) {

					$this->model->tambah('jurnal_guru', [
						'kode_absen' => $kode,
						'isi' => $isi ? $isi : '-'
					]);
					$hadirHsl = $this->model->getBy2('harian', 'ket', 'hadir', 'kode', $kode);
					$sakitHsl = $this->db_active->query("SELECT * FROM harian WHERE ket= 'sakit' AND kode = '$kode'");
					$izinHsl = $this->db_active->query("SELECT * FROM harian WHERE ket= 'izin' AND kode = '$kode'");
					$alphaHsl = $this->db_active->query("SELECT * FROM harian WHERE ket= 'alpha' AND kode = '$kode'");

					$psn = '*LAPORAN KEHADIRAN SISWA*
*' . tanggal_indo(date('d-m-Y'), true) . '*

Guru : ' . $nmGuru->nama . '
Mapel : ' . $nmMapel->nama . '
Kelas : ' . $nmkelas->nama . '
Jam ke : ' . $dari . ' - ' . $sampai . '

*Sakit*
';
					foreach ($sakitHsl->result() as $skt) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$skt->id_siswa' ")->row();
						$psn .= '- ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}
					$psn .= "\n" . '*Izin*' . "\n";
					foreach ($izinHsl->result() as $izn) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$izn->id_siswa' ")->row();
						$psn .= '- ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}
					$psn .= "\n" . '*Alpha*' . "\n";
					foreach ($alphaHsl->result() as $alp) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$alp->id_siswa' ")->row();
						$psn .= '- ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}

					$psn .= "\n" . '*Hadir :*' . "\n" . $hadirHsl->num_rows() . ' siswa';
					$psn .= "\n" . "\n" . '_Demikian Laporan ini kami sampaikan terimakasih_';

					// echo $psn;
					// kirim_person('085236924510', $psn);
					kirim_group('120363418007064631@g.us', $psn);

					// Real
					// kirim_group('6285258800849-1471341787@g.us', $psn);

					$this->session->set_flashdata('ok', 'Input Absen Berhasil');
					redirect('kbm/absensi');
				} else {
					$this->session->set_flashdata('error', 'Input Absen Gagal');
					redirect('kbm/absensi');
				}
			}
		}
	}
}
