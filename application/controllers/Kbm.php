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

		$this->mustLogin();

		$this->id_user = $this->session->userdata('id_user');
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->session->userdata('id_lembaga');
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
		$data['id_lembaga'] = $this->id_lembaga;

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

	public function hasil()
	{
		$data['judul'] = 'Hasil Absensi KBM';
		$data['menu'] = 'hasil';
		$data['sub'] = 'kbm';

		$data['days'] = date('l');
		$idguru = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
		$data['idguru'] = $idguru->id_guru;
		$data['guru'] = $this->db->query("SELECT * FROM guru WHERE id_guru = '$idguru->id_guru' ")->row();

		$data['lmb'] =  $this->db
			->select('
					j.id_lembaga,
					l.nama AS nama_lembaga,
					MIN(j.jam_dari) AS jam_pertama
				')
			->from('jadwal j')
			->join('lembaga l', 'l.id_lembaga = j.id_lembaga')
			->where('j.hari', $data['days'])
			->where('j.id_guru', $idguru->id_guru)
			->group_by('j.id_lembaga, l.nama')
			->order_by('jam_pertama', 'ASC')
			->get()
			->result();
		$data['user'] = $idguru;

		$this->load->view('absensi/hasil_kbm_siswa', $data);
	}


	public function cariKelas()
	{
		$idJadwal = $this->input->post('id_jadwal', true);
		$jadwal = $this->db->query("SELECT * FROM jadwal WHERE id_jadwal = '$idJadwal' ")->row();

		$mapel = $this->db->query("SELECT * FROM mapel WHERE id_mapel = '$jadwal->id_mapel' ")->row();

		$dyas = date('Y-m-d');

		$listdata = $this->db->query("SELECT * FROM rombel WHERE id_kelas = $jadwal->id_kelas ");

		$cek = $this->db->query("SELECT * FROM harian WHERE id_guru = '$jadwal->id_guru' AND id_mapel = '$jadwal->id_mapel' AND id_kelas = '$jadwal->id_kelas' AND tanggal = '$dyas' AND dari = '$jadwal->jam_dari' AND id_lembaga = '$jadwal->id_lembaga' ")->row();


		if (!$cek) {
			echo '	
			<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-800">

			<input type="hidden" name="guru" value="' . $jadwal->id_guru . '">
			<input type="hidden" name="mapel" value="' . $jadwal->id_mapel . '">
			<input type="hidden" name="kelas" value="' . $jadwal->id_kelas . '">
			<input type="hidden" name="dari" value="' . $jadwal->jam_dari . '">
			<input type="hidden" name="sampai" value="' . $jadwal->jam_sampai . '">
			<input type="hidden" name="id_lembaga" value="' . $jadwal->id_lembaga . '">

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
								
								<!-- Telat -->
								<label class="cursor-pointer">
									<input type="radio" name="data[' . $no . '][ket]" value="telat" class="peer hidden">
									<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border
												border-gray-300 dark:border-slate-500
												text-xs font-bold text-gray-700 dark:text-slate-200
												peer-checked:bg-purple-500 peer-checked:text-white">
										T
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
		} else {
			echo '
			<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-800 p-6 text-center">

				<p class="text-lg font-semibold text-gray-800 dark:text-slate-100">
					Absensi Sudah Selesai
				</p>

				<button type="button"
					onclick="window.location.href=\'' . base_url('kbm/edit/' . $cek->kode) . '\'"
					class="mt-5 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
					Edit Hasil Absensi
				</button>

			</div>';
		}
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
		$id_lembaga = $this->input->post('id_lembaga', true);
		$kode = $this->uuid->v4();
		$tanggal = date('Y-m-d');

		$jmlAbs = ($sampai - $dari) + 1;

		$cek = $this->db->query("SELECT * FROM harian WHERE id_guru = '$guru' AND id_mapel = '$mapel' AND id_kelas = '$kelas' AND tanggal = '$tanggal' AND dari = '$dari' AND id_lembaga = '$id_lembaga' ")->row();

		$nmGuru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$guru' ")->row();
		$nmMapel = $this->db->query("SELECT * FROM mapel WHERE id_mapel = '$mapel'")->row();
		$nmkelas = $this->db->query("SELECT * FROM kelas WHERE id_kelas = '$kelas'")->row();

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
						$telat = 0;
					} elseif ($item['ket'] == 'sakit') {
						$sakit = $jmlAbs;
						$izin = 0;
						$alpha = 0;
						$telat = 0;
					} elseif ($item['ket'] == 'izin') {
						$sakit = 0;
						$izin = $jmlAbs;
						$alpha = 0;
						$telat = 0;
					} elseif ($item['ket'] == 'telat') {
						$sakit = 0;
						$izin = 0;
						$alpha = 0;
						$telat = $jmlAbs;
					} else {
						$sakit = 0;
						$izin = 0;
						$alpha = 0;
						$telat = 0;
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
						'telat' => $telat,
						'kelas' => $nmkelas->nama,
						'mapel' => $nmMapel->nama,
						'guru' => $nmGuru->nama,
						'nama_siswa' => $nmsiswa->nama,
						'id_lembaga' => $id_lembaga,
					];
					$sql = $this->db->insert('harian', $dtsm);
				}

				if ($sql) {
					$this->db->insert('jurnal_guru', [
						'kode_absen' => $kode,
						'isi' => $isi ? $isi : '-'
					]);
					$hadirHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'hadir' AND kode = '$kode' AND id_lembaga = '$id_lembaga'");
					$sakitHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'sakit' AND kode = '$kode' AND id_lembaga = '$id_lembaga'");
					$izinHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'izin' AND kode = '$kode' AND id_lembaga = '$id_lembaga'");
					$alphaHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'alpha' AND kode = '$kode' AND id_lembaga = '$id_lembaga'");
					$telatHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'telat' AND kode = '$kode' AND id_lembaga = '$id_lembaga'");

					$psn = '*LAPORAN KEHADIRAN SISWA*
*' . tanggal_indo(date('d-m-Y'), true) . '*

Guru : ' . $nmGuru->nama . '
Mapel : ' . $nmMapel->nama . '
Kelas : ' . $nmkelas->nama . '
Jam ke : ' . $dari . ' - ' . $sampai . '

*Sakit*
';
					$no_sakit = 1;
					foreach ($sakitHsl->result() as $skt) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$skt->id_siswa' ")->row();
						$psn .= $no_sakit++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}
					$psn .= "\n" . '*Izin*' . "\n";
					$no_izin = 1;
					foreach ($izinHsl->result() as $izn) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$izn->id_siswa' ")->row();
						$psn .= $no_izin++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}
					$psn .= "\n" . '*Alpha*' . "\n";
					$no_alpha = 1;
					foreach ($alphaHsl->result() as $alp) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$alp->id_siswa' ")->row();
						$psn .= $no_alpha++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}
					$psn .= "\n" . '*Telat*' . "\n";
					$no_telat = 1;
					foreach ($telatHsl->result() as $tl) {
						$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$tl->id_siswa' ")->row();
						$psn .= $no_telat++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
					}

					$psn .= "\n" . '*Hadir :* '  . $hadirHsl->num_rows() . ' siswa';

					$psn .= "\n" . "\n" . '*Materi yang disampaikan :*' . "\n" . $isi;

					$psn .= "\n" . "\n" . '_Demikian Laporan ini kami sampaikan terimakasih_';

					// echo $psn;
					// kirim_person('085236924510', $psn);

					// Get WA API settings
					$wa_api_url_db = $this->db->get_where('setting', ['key' => 'wa_api_url'])->row('isi');
					$wa_api_key_db = $this->db->get_where('setting', ['key' => 'wa_api_key'])->row('isi');

					$wa_api_url = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');
					$wa_api_key = $wa_api_key_db ?: (getenv('WA_API_KEY') ?: '');

					// Get session ID from lembaga
					$lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $id_lembaga])->row();
					$sessionId = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

					// Get selected groups for this institution
					$selected_groups_db = $this->db->get_where('setting', [
						'key' => 'wa_selected_groups',
						'id_lembaga' => $id_lembaga
					])->row('isi');

					$selected_groups = json_decode($selected_groups_db, true) ?: [];

					// Send to each selected group
					foreach ($selected_groups as $group) {
						$groupId = $group['id'];

						$ch = curl_init();
						curl_setopt_array($ch, [
							CURLOPT_URL => $wa_api_url . '/send-group',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => http_build_query([
								'groupId' => $groupId,
								'message' => $psn,
								'apiKey' => $wa_api_key,
								'sessionId' => $sessionId
							]),
							CURLOPT_TIMEOUT => 20
						]);
						curl_exec($ch);
						curl_close($ch);
					}

					$this->session->set_flashdata('ok', 'Input Absen Berhasil');
					redirect('kbm/hasil');
				} else {
					$this->session->set_flashdata('error', 'Input Absen Gagal');
					redirect('kbm/absensi');
				}
			}
		}
	}

	public function edit($kode)
	{
		$data['judul'] = 'Data KBM';
		$data['menu'] = 'hasil';
		$data['sub'] = 'kbm';

		$userData = $this->db->query("SELECT * FROM user WHERE id_user = '$this->id_user' ")->row();
		$data['guru'] = $this->db->query("SELECT * FROM guru WHERE id_guru = '$userData->id_guru' ")->row();


		$data['listdata'] = $this->db->query("SELECT * FROM harian WHERE kode = '$kode' ");
		$data['materi'] = $this->db->query("SELECT * FROM jurnal_guru WHERE kode_absen = '$kode' ")->row();

		$this->load->view('absensi/edit_kbm_siswa', $data);
	}

	public function edit_multiple_data()
	{
		$dari = $this->input->post('dari', true);
		$sampai = $this->input->post('sampai', true);
		$kode = $this->input->post('kode', true);

		$guru = $this->input->post('guru', true);
		$mapel = $this->input->post('mapel', true);
		$kelas = $this->input->post('kelas', true);

		$data = $this->input->post('data', true);
		$isi = $this->input->post('isi', true);

		$jmlAbs = ($sampai - $dari) + 1;

		if (!empty($data)) {
			foreach ($data as $item) {
				$id = $item['id'];

				if ($item['ket'] == 'alpha') {
					$sakit = 0;
					$izin = 0;
					$alpha = $jmlAbs;
					$telat = 0;
				} elseif ($item['ket'] == 'sakit') {
					$sakit = $jmlAbs;
					$izin = 0;
					$alpha = 0;
					$telat = 0;
				} elseif ($item['ket'] == 'izin') {
					$sakit = 0;
					$izin = $jmlAbs;
					$alpha = 0;
					$telat = 0;
				} elseif ($item['ket'] == 'telat') {
					$sakit = 0;
					$izin = 0;
					$alpha = 0;
					$telat = $jmlAbs;
				} else {
					$sakit = 0;
					$izin = 0;
					$alpha = 0;
					$telat = 0;
				}

				$dtsm = [
					'ket' => $item['ket'],
					'sakit' => $sakit,
					'izin' => $izin,
					'alpha' => $alpha,
					'telat' => $telat,
				];
				$sql = $this->db->update('harian', $dtsm, ['id_harian' => $id]);
			}

			if ($sql) {
				$this->db->update('jurnal_guru', [
					'isi' => $isi ? $isi : '-'
				], ['kode_absen' => $kode]);

				$hadirHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'hadir' AND kode = '$kode'");
				$sakitHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'sakit' AND kode = '$kode'");
				$izinHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'izin' AND kode = '$kode'");
				$alphaHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'alpha' AND kode = '$kode'");
				$telatHsl = $this->db->query("SELECT * FROM harian WHERE ket= 'telat' AND kode = '$kode'");

				$psn = '*LAPORAN KEHADIRAN SISWA*
*' . tanggal_indo(date('d-m-Y'), true) . '*

Guru : ' . $guru . '
Mapel : ' . $mapel . '
Kelas : ' . $kelas . '
Jam ke : ' . $dari . ' - ' . $sampai . '

*Sakit*
';
				$no_sakit = 1;
				foreach ($sakitHsl->result() as $skt) {
					$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$skt->id_siswa' ")->row();
					$psn .= $no_sakit++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
				}
				$psn .= "\n" . '*Izin*' . "\n";
				$no_izin = 1;
				foreach ($izinHsl->result() as $izn) {
					$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$izn->id_siswa' ")->row();
					$psn .= $no_izin++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
				}
				$psn .= "\n" . '*Alpha*' . "\n";
				$no_alpha = 1;
				foreach ($alphaHsl->result() as $alp) {
					$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$alp->id_siswa' ")->row();
					$psn .= $no_alpha++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
				}
				$psn .= "\n" . '*Telat*' . "\n";
				$no_telat = 1;
				foreach ($telatHsl->result() as $tl) {
					$nmsiswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = '$tl->id_siswa' ")->row();
					$psn .= $no_telat++ . '. ' . ucwords(strtolower($nmsiswa->nama)) . "\n";
				}

				$psn .= "\n" . '*Hadir :* '  . $hadirHsl->num_rows() . ' siswa';

				$psn .= "\n" . "\n" . '*Materi yang disampaikan :*' . "\n" . $isi;

				$psn .= "\n" . "\n" . '_Demikian Laporan ini kami sampaikan terimakasih_';

				// Get WA API settings
				$wa_api_url_db = $this->db->get_where('setting', ['key' => 'wa_api_url'])->row('isi');
				$wa_api_key_db = $this->db->get_where('setting', ['key' => 'wa_api_key'])->row('isi');

				$wa_api_url = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');
				$wa_api_key = $wa_api_key_db ?: (getenv('WA_API_KEY') ?: '');

				// Get session ID from lembaga
				$lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $this->id_lembaga])->row();
				$sessionId = ($lembaga && !empty($lembaga->session_id)) ? $lembaga->session_id : "default";

				// Get selected groups for this institution
				$selected_groups_db = $this->db->get_where('setting', [
					'key' => 'wa_selected_groups',
					'id_lembaga' => $this->id_lembaga
				])->row('isi');

				$selected_groups = json_decode($selected_groups_db, true) ?: [];

				// Send to each selected group
				foreach ($selected_groups as $group) {
					$groupId = $group['id'];

					$ch = curl_init();
					curl_setopt_array($ch, [
						CURLOPT_URL => $wa_api_url . '/send-group',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => http_build_query([
							'groupId' => $groupId,
							'message' => $psn,
							'apiKey' => $wa_api_key,
							'sessionId' => $sessionId
						]),
						CURLOPT_TIMEOUT => 20
					]);
					curl_exec($ch);
					curl_close($ch);
				}

				$this->session->set_flashdata('ok', 'Input Absen Berhasil');

				redirect('kbm/hasil');
			} else {
				$this->session->set_flashdata('error', 'Input Absen Gagal');
				redirect('kbm/absensi');
			}
		}
	}

	public function hapus_hasil($kode_absensi)
	{
		$this->db->query("DELETE FROM harian WHERE kode = '$kode_absensi'");
		$this->db->query("DELETE FROM jurnal_guru WHERE kode_absen = '$kode_absensi'");
		$this->session->set_flashdata('ok', 'Hapus Absen Berhasil');
		redirect('kbm/hasil');
	}
}
