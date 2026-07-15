<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sinkron extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');

		$this->config->load('api', true);
		
		// Load token dynamically from setting table, getenv, or config
		$token_db = $this->db->get_where('setting', ['key' => 'ppdwk_token'])->row('isi');
		$this->token = $token_db ?: (getenv('PPDWK_TOKEN') ?: $this->config->item('ppdwk_token', 'api'));

		$this->mustLogin();
		$this->onlyAdminSuper();
		$this->iduser = $this->session->userdata('id_user');
		$this->id_lembaga = $this->db->query("SELECT id_lembaga FROM user WHERE id_user = '$this->iduser'")->row('id_lembaga');
	}

	public function guru()
	{
		$data['judul'] = 'Data Guru';
		$data['menu'] = 'sinkron';
		$data['sub'] = 'sinc_guru';

		// $data['data'] = $this->model->getAll('guru')->result();

		$this->load->view('sinkron/guru', $data);
	}
	public function siswa()
	{
		$data['judul'] = 'Data Siswa';
		$data['menu'] = 'sinkron';
		$data['sub'] = 'sinc_siswa';

		// $data['data'] = $this->model->getAll('guru')->result();

		$this->load->view('sinkron/siswa', $data);
	}
	private function _ensure_lembaga_columns()
	{
		if (!$this->db->field_exists('jenis_lembaga', 'lembaga')) {
			$this->db->query("ALTER TABLE `lembaga` ADD COLUMN `jenis_lembaga` TEXT NULL;");
		}
	}

	public function lembaga()
	{
		$this->_ensure_lembaga_columns();
		$data['judul'] = 'Data Lembaga';
		$data['menu'] = 'sinkron';
		$data['sub'] = 'sinc_lembaga';

		// $data['data'] = $this->model->getAll('guru')->result();

		$this->load->view('sinkron/lembaga', $data);
	}

	public function data_siswa()
	{
		// $result = $this->Model_guru->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= FILTER ================= */
		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('alamat', $search)
				->or_like('jkl', $search)
				->or_like('nisn', $search)
				->group_end();
		}

		/* ================= TOTAL ================= */
		$total = $this->db->count_all_results('siswa', false);

		/* ================= DATA ================= */
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

	public function data_guru()
	{
		// $result = $this->Model_guru->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= FILTER ================= */
		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('kode_guru', $search)
				->or_like('no_hp', $search)
				->or_like('jkl', $search)
				->group_end();
		}

		/* ================= TOTAL ================= */
		$total = $this->db->count_all_results('guru', false);

		/* ================= DATA ================= */
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

	public function data_lembaga()
	{
		// $result = $this->Model_guru->getData($params);
		$search   = $this->input->get('search') ?? '';
		$page     = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy   = $this->input->get('sortBy') ?? 'nama';
		$sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		/* ================= FILTER ================= */
		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('npsn', $search)
				->or_like('jenjang', $search)
				->or_like('jenis_lembaga', $search)
				->group_end();
		}

		/* ================= TOTAL ================= */
		$total = $this->db->count_all_results('lembaga', false);

		/* ================= DATA ================= */
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

	public function update_session_id()
	{
		$id_lembaga = $this->input->post('id_lembaga', TRUE);
		$session_id = $this->input->post('session_id', TRUE);

		if (empty($id_lembaga)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'ID Lembaga tidak boleh kosong']));
			return;
		}

		$update = $this->db->where('id_lembaga', $id_lembaga)->update('lembaga', ['session_id' => $session_id]);

		if ($update) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => true, 'msg' => 'Session ID berhasil diperbarui']));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => false, 'msg' => 'Gagal memperbarui Session ID']));
		}
	}

	public function fetch_page()
	{
		$page = $this->input->post('page') ?? 1;
		$perPage = 10;

		$url = "https://data.ppdwk.com/api/datatables?"
			. "data=referensi-guru"
			. "&page={$page}"
			. "&per_page={$perPage}"
			. "&q=&sortby=nama&sortbydesc=ASC";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->token,
				'Accept: application/json'
			],
			CURLOPT_TIMEOUT => 20
		]);

		$result = curl_exec($ch);
		curl_close($ch);

		echo $result;
	}

	public function fetch_page_siswa()
	{
		$page = $this->input->post('page') ?? 1;
		$perPage = 10;

		$url = "https://data.ppdwk.com/api/datatables?"
			. "data=referensi-peserta-didik"
			. "&page={$page}"
			. "&per_page={$perPage}"
			. "&q=&sortby=nama&sortbydesc=ASC";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->token,
				'Accept: application/json'
			],
			CURLOPT_TIMEOUT => 20
		]);

		$result = curl_exec($ch);
		curl_close($ch);

		echo $result;
	}

	public function fetch_page_lembaga()
	{
		$page = $this->input->post('page') ?? 1;
		$perPage = 10;

		$url = "https://data.ppdwk.com/api/datatables?"
			. "data=referensi-lembaga"
			. "&page={$page}"
			. "&per_page={$perPage}"
			. "&q=&sortby=nama&sortbydesc=ASC";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->token,
				'Accept: application/json'
			],
			CURLOPT_TIMEOUT => 20
		]);

		$result = curl_exec($ch);
		curl_close($ch);

		echo $result;
	}

	public function sync_one()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$guru = $payload['guru'] ?? null;

		if (!$guru || !isset($guru['id_guru'])) {
			echo json_encode(['status' => false, 'msg' => 'Data guru kosong']);
			return;
		}


		$this->db->trans_start();

		// ======================
		// 1. SIMPAN / UPDATE GURU
		// ======================

		$dataGuru = [
			'nama'     => $guru['nama'],
			'no_hp'     => $guru['no_hp'],
			'jkl'     => $guru['jkl'],
		];

		$cekGuru = $this->db
			->get_where('guru', ['id_guru' => $guru['id_guru']])
			->row();

		if ($cekGuru) {
			$this->db->where('id_guru', $cekGuru->id_guru)->update('guru', $dataGuru);
			$idGuru = $cekGuru->id_guru;
		} else {
			$dataGuru['id_guru'] = $guru['id_guru'];
			$dataGuru['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('guru', $dataGuru);
			$idGuru = $guru['id_guru'];

			$id = (int)$this->db->insert_id();
			$kode = kodeFromNumber($id);
			$this->db->where('id_guru', $idGuru)->update('guru', [
				'kode_guru' => $kode
			]);
		}

		// ======================
		// 2. AMBIL DETAIL GURU
		// ======================
		$detail = $this->getDetail("https://data.ppdwk.com/api/ptk/show/" . $guru['id_guru']);
		
		if ($detail === 'NOT_FOUND') {
			$this->db->trans_start();
			$this->db->where('id_guru', $idGuru)->delete('registrasi');
			$this->db->where('id_guru', $idGuru)->delete('user');
			$this->db->where('id_guru', $idGuru)->delete('guru');
			$this->db->trans_complete();

			echo json_encode([
				'status' => 'deleted',
				'msg' => 'Guru ' . $guru['nama'] . ' tidak ditemukan di pusat. Data lokal telah dihapus.'
			]);
			return;
		}

		if ($detail && isset($detail['registrasi_ptk'])) {

			foreach ($detail['registrasi_ptk'] as $reg) {

				$idLembaga = $reg['lembaga_id'] ?? null;
				$induk = $reg['ptk_induk'] ?? null;
				if (!$idLembaga) continue;

				// ======================
				// 3. SIMPAN REGISTRASI
				// ======================
				$exists = $this->db->get_where('registrasi', [
					'id_guru'    => $idGuru,
					'id_lembaga' => $idLembaga
				])->row();

				if (!$exists) {
					$this->db->insert('registrasi', [
						'id_guru'    => $idGuru,
						'id_lembaga' => $idLembaga,
						'satminkal' => $induk,
						'created_at' => date('Y-m-d H:i:s'),
					]);
				} else {
					$this->db->where([
						'id_guru'    => $idGuru,
						'id_lembaga' => $idLembaga
					])->update('registrasi', [
						'satminkal' => $induk
					]);
				}
			}

			// Update user's id_lembaga based on satminkal = 1 (or '1')
			$satminkal_reg = $this->db->query("SELECT id_lembaga FROM registrasi WHERE id_guru = ? AND (satminkal = 1 OR satminkal = '1')", [$idGuru])->row();
			if ($satminkal_reg) {
				$this->db->where('id_guru', $idGuru)->update('user', [
					'id_lembaga' => $satminkal_reg->id_lembaga
				]);
			}
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'Guru ' . $guru['nama'] . ' + registrasi tersinkron'
		]);
	}
	public function sync_one_siswa()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$siswa = $payload['siswa'] ?? null;

		if (!$siswa || !isset($siswa['id_siswa'])) {
			echo json_encode(['status' => false, 'msg' => 'Data siswa kosong']);
			return;
		}


		$this->db->trans_start();

		// ======================
		// 1. SIMPAN / UPDATE siswa
		// ======================

		$datasiswa = [
			'nama'     => $siswa['nama'],
			'nisn'     => $siswa['nisn'],
			'jkl'     => $siswa['jkl'],
		];

		$ceksiswa = $this->db
			->get_where('siswa', ['id_siswa' => $siswa['id_siswa']])
			->row();

		if ($ceksiswa) {
			$this->db->where('id_siswa', $ceksiswa->id_siswa)->update('siswa', $datasiswa);
			$idsiswa = $ceksiswa->id_siswa;
		} else {
			$datasiswa['id_siswa'] = $siswa['id_siswa'];
			$this->db->insert('siswa', $datasiswa);
			$idsiswa = $siswa['id_siswa'];
		}

		// ======================
		// 2. AMBIL DETAIL siswa
		// ======================
		$detail = $this->getDetail("https://data.ppdwk.com/api/pd/show/" . $siswa['id_siswa']);

		// var_dump($detail);
		// exit();

		if ($detail && isset($detail['registrasi_pd'])) {

			if ($detail['wilayah']) {
				$this->db->where('id_siswa', $idsiswa)->update('siswa', [
					'alamat' => $detail['wilayah']['nama'] . '-' . $detail['wilayah']['parrent_recursive']['nama'] . '-' . $detail['wilayah']['parrent_recursive']['parrent_recursive']['nama']
				]);
			}

			foreach ($detail['registrasi_pd'] as $reg) {

				$idLembaga = $reg['lembaga_id'] ?? null;
				if (!$idLembaga) continue;

				$keluarpd = $reg['jenis_keluar'] ?? null;
				if ($keluarpd != null) continue;

				// ======================
				// 3. SIMPAN REGISTRASI
				// ======================
				$exists = $this->db->get_where('registrasi_siswa', [
					'id_siswa'    => $idsiswa,
					'id_lembaga' => $idLembaga
				])->row();

				if (!$exists) {
					$this->db->insert('registrasi_siswa', [
						'id_siswa'    => $idsiswa,
						'id_lembaga' => $idLembaga,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
			}
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'Siswa ' . $siswa['nama'] . ' + registrasi tersinkron'
		]);
	}

	public function sync_one_lembaga()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$lembaga = $payload['lembaga'] ?? null;

		if (!$lembaga || !isset($lembaga['id_lembaga'])) {
			echo json_encode(['status' => false, 'msg' => 'Data lembaga kosong']);
			return;
		}

		$this->_ensure_lembaga_columns();

		$this->db->trans_start();

		// ======================
		// 1. SIMPAN / UPDATE lembaga
		// ======================

		$jenis_lembaga = $lembaga['jenis_lembaga'] ?? null;
		$jenis_lembaga_name = '';
		if (is_array($jenis_lembaga)) {
			$jenis_lembaga_name = $jenis_lembaga['nama'] ?? '';
		} elseif (is_string($jenis_lembaga)) {
			$decoded = json_decode($jenis_lembaga, true);
			if (is_array($decoded)) {
				$jenis_lembaga_name = $decoded['nama'] ?? '';
			} else {
				$jenis_lembaga_name = $jenis_lembaga;
			}
		}

		$datalembaga = [
			'nama'          => $lembaga['nama'],
			'npsn'          => $lembaga['npsn'],
			'jenjang'       => $lembaga['jenjang'],
			'alamat'        => $lembaga['alamat'],
			'jenis_lembaga' => $jenis_lembaga_name,
		];

		$ceklembaga = $this->db
			->get_where('lembaga', ['id_lembaga' => $lembaga['id_lembaga']])
			->row();

		if ($ceklembaga) {
			$this->db->where('id_lembaga', $ceklembaga->id_lembaga)->update('lembaga', $datalembaga);
		} else {
			$datalembaga['id_lembaga'] = $lembaga['id_lembaga'];
			$this->db->insert('lembaga', $datalembaga);
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'lembaga ' . $lembaga['nama'] . ' + registrasi tersinkron'
		]);
	}

	private function getDetail($url)
	{
		if (!$url) return null;

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->token,
				'Accept: application/json'
			],
			CURLOPT_TIMEOUT => 30
		]);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode === 404) {
			return 'NOT_FOUND';
		}

		if ($httpCode !== 200) {
			log_message('error', "DETAIL PTK FAIL [$url]: " . $result);
			return null;
		}

		$json = json_decode($result, true);
		if ($json && isset($json['status']) && $json['status'] === false) {
			return 'NOT_FOUND';
		}
		return $json ?? null;
	}

	public function akun()
	{
		$id = $this->input->post('id', true);

		$guru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$id' ")->row();
		$reg = $this->db->query("SELECT * FROM registrasi WHERE id_guru = '$id' AND satminkal ")->row();
		$passs = generatePassword6();
		$cek = $this->db->query("SELECT * FROM user WHERE id_guru = '$id' ")->row();
		if (!$cek) {
			$userdata = [
				'id_user' => $this->uuid->v4(),
				'nama' => $guru->nama,
				'jabatan' => 'Guru',
				'username' => generateUsernameUnique($guru->nama),
				'password' => password_hash($passs, PASSWORD_BCRYPT),
				'pass_v' => $passs,
				'level' => 'guru',
				'id_guru' => $id,
				'id_lembaga' => $reg->id_lembaga
			];
			$sql = $this->db->insert('user', $userdata);

			if ($sql) {
				echo json_encode(['status' => true]);
			} else {
				echo json_encode(['status' => false]);
			}
		} else {
			echo json_encode(['status' => false, 'msg' => 'User sudah ada']);
		}
	}

	public function sync_siswa()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$siswa = $payload['siswa'] ?? null;

		if (!$siswa || !isset($siswa['id_siswa'])) {
			echo json_encode(['status' => false, 'msg' => 'Data siswa kosong']);
			return;
		}


		$this->db->trans_start();



		// ======================
		// 2. AMBIL DETAIL siswa
		// ======================
		$idsiswa = $siswa['id_siswa'];
		$detail = $this->getDetail("https://data.ppdwk.com/api/pd/show/" . $siswa['id_siswa']);
		$this->db
			->where('id_siswa', $idsiswa)
			->delete('registrasi_siswa');
		// var_dump($detail);
		// exit();

		if ($detail && isset($detail['registrasi_pd'])) {

			$this->db->where('id_siswa', $idsiswa)->update('siswa', [
				'nama'     => $detail['nama'],
				'nisn'     => $detail['nisn'],
				'jkl'     => $detail['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'
			]);
			if ($detail['wilayah']) {
				$this->db->where('id_siswa', $idsiswa)->update('siswa', [
					'alamat' => $detail['wilayah']['nama'] . '-' . $detail['wilayah']['parrent_recursive']['nama'] . '-' . $detail['wilayah']['parrent_recursive']['parrent_recursive']['nama']
				]);
			}

			foreach ($detail['registrasi_pd'] as $reg) {

				$idLembaga = $reg['lembaga_id'] ?? null;
				if (!$idLembaga) continue;

				$keluarpd = $reg['jenis_keluar'] ?? null;
				if ($keluarpd != null) continue;

				// ======================
				// 3. SIMPAN REGISTRASI
				// ======================
				$exists = $this->db->get_where('registrasi_siswa', [
					'id_siswa'    => $idsiswa,
					'id_lembaga' => $idLembaga
				])->row();

				if (!$exists) {
					$this->db->insert('registrasi_siswa', [
						'id_siswa'    => $idsiswa,
						'id_lembaga' => $idLembaga,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
			}
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'Siswa ' . $detail['nama'] . ' + registrasi tersinkron'
		]);
	}

	public function sync_guru()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$guru = $payload['guru'] ?? null;

		if (!$guru || !isset($guru['id_guru'])) {
			echo json_encode(['status' => false, 'msg' => 'Data guru kosong']);
			return;
		}


		$this->db->trans_start();

		$idGuru = $guru['id_guru'];
		$detail = $this->getDetail("https://data.ppdwk.com/api/ptk/show/" . $guru['id_guru']);

		if ($detail === 'NOT_FOUND') {
			$this->db->trans_start();
			$this->db->where('id_guru', $idGuru)->delete('registrasi');
			$this->db->where('id_guru', $idGuru)->delete('user');
			$this->db->where('id_guru', $idGuru)->delete('guru');
			$this->db->trans_complete();

			echo json_encode([
				'status' => 'deleted',
				'msg' => 'Guru tidak ditemukan di pusat. Data lokal telah dihapus.'
			]);
			return;
		}

		if ($detail && isset($detail['registrasi_ptk'])) {

			$this->db->where('id_guru', $idGuru)->delete('registrasi');

			$dataGuru = [
				'nama'     => $detail['nama'],
				'no_hp'     => $detail['telpon'],
				'jkl'     => $detail['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan',
			];

			$cekGuru = $this->db
				->get_where('guru', ['id_guru' => $guru['id_guru']])
				->row();

			if ($cekGuru) {
				$this->db->where('id_guru', $cekGuru->id_guru)->update('guru', $dataGuru);
			} else {
				$dataGuru['id_guru'] = $guru['id_guru'];
				$dataGuru['created_at'] = date('Y-m-d H:i:s');
				$this->db->insert('guru', $dataGuru);

				$id = (int)$this->db->insert_id();
				$kode = kodeFromNumber($id);
				$this->db->where('id_guru', $idGuru)->update('guru', [
					'kode_guru' => $kode
				]);
			}

			foreach ($detail['registrasi_ptk'] as $reg) {

				$idLembaga = $reg['lembaga_id'] ?? null;
				$induk = $reg['ptk_induk'] ?? null;
				if (!$idLembaga) continue;

				// ======================
				// 3. SIMPAN REGISTRASI
				// ======================
				$exists = $this->db->get_where('registrasi', [
					'id_guru'    => $idGuru,
					'id_lembaga' => $idLembaga
				])->row();

				if (!$exists) {
					$this->db->insert('registrasi', [
						'id_guru'    => $idGuru,
						'id_lembaga' => $idLembaga,
						'satminkal' => $induk,
						'created_at' => date('Y-m-d H:i:s'),
					]);
				} else {
					$this->db->where([
						'id_guru'    => $idGuru,
						'id_lembaga' => $idLembaga
					])->update('registrasi', [
						'satminkal' => $induk
					]);
				}
			}

			// Update user's id_lembaga based on satminkal = 1 (or '1')
			$satminkal_reg = $this->db->query("SELECT id_lembaga FROM registrasi WHERE id_guru = ? AND (satminkal = 1 OR satminkal = '1')", [$idGuru])->row();
			if ($satminkal_reg) {
				$this->db->where('id_guru', $idGuru)->update('user', [
					'id_lembaga' => $satminkal_reg->id_lembaga
				]);
			}
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'Guru ' . $detail['nama'] . ' + registrasi tersinkron'
		]);
	}

	public function generate_warna_all()
	{
		$this->mustLogin();
		$this->onlyAdminSuper();

		// Fetch all teachers in database
		$gurus = $this->db->select('id_guru, nama')->get('guru')->result_array();

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
		$this->onlyAdminSuper();

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

	private function _ensure_master_mapel_table()
	{
		if (!$this->db->table_exists('master_mapel')) {
			$this->db->query("CREATE TABLE `master_mapel` (
				`id_master_mapel` INT PRIMARY KEY,
				`kode_mapel` VARCHAR(50) NOT NULL,
				`nama` VARCHAR(100) NOT NULL,
				`jenis_lembaga` TEXT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		} else {
			// Drop unique key if it exists to prevent duplicate code errors
			$query = $this->db->query("SHOW KEYS FROM `master_mapel` WHERE Key_name='unique_kode'");
			if ($query->num_rows() > 0) {
				$this->db->query("ALTER TABLE `master_mapel` DROP INDEX `unique_kode` ");
			}
		}
		if (!$this->db->field_exists('id_master_mapel', 'mapel')) {
			$this->db->query("ALTER TABLE `mapel` ADD COLUMN `id_master_mapel` INT NULL;");
		}
	}

	public function mapel()
	{
		$this->_ensure_master_mapel_table();
		$data['judul'] = 'Data Master Mapel';
		$data['menu'] = 'sinkron';
		$data['sub'] = 'sinc_mapel';

		$this->load->view('sinkron/mapel', $data);
	}

	public function data_master_mapel()
	{
		$this->_ensure_master_mapel_table();
		$search      = $this->input->get('search') ?? '';
		$peruntukan  = $this->input->get('peruntukan') ?? '';
		$page        = max(1, (int) ($this->input->get('page') ?? 1));
		$perPage     = max(1, (int) ($this->input->get('perPage') ?? 10));
		$sortBy      = $this->input->get('sortBy') ?? 'nama';
		$sortDir     = strtoupper($this->input->get('sortDir') ?? 'ASC');

		$offset = ($page - 1) * $perPage;

		$this->db->from('master_mapel');
		if (!empty($search)) {
			$this->db->group_start()
				->like('nama', $search)
				->or_like('kode_mapel', $search)
				->group_end();
		}

		if (!empty($peruntukan)) {
			$this->db->like('jenis_lembaga', '"nama":"' . $peruntukan . '"');
		}

		$total = $this->db->count_all_results('', false);

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

	public function fetch_page_mapel()
	{
		$page = $this->input->post('page') ?? 1;
		$perPage = 10;

		$url = "https://data.ppdwk.com/api/datatables?"
			. "data=referensi-mata-pelajaran"
			. "&page={$page}"
			. "&per_page={$perPage}"
			. "&q=&sortby=mata_pelajaran_id&sortbydesc=ASC";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->token,
				'Accept: application/json'
			],
			CURLOPT_TIMEOUT => 20
		]);

		$result = curl_exec($ch);
		curl_close($ch);

		echo $result;
	}

	public function sync_one_mapel()
	{
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			echo json_encode(['status' => false, 'msg' => 'Payload tidak valid']);
			return;
		}

		$mapel = $payload['mapel'] ?? null;

		if (!$mapel || !isset($mapel['id_master_mapel'])) {
			echo json_encode(['status' => false, 'msg' => 'Data mapel kosong']);
			return;
		}

		$id = $mapel['id_master_mapel'];
		$nama = $mapel['nama'];
		$jenis_lembaga = $mapel['jenis_lembaga'];

		$cek = $this->db->get_where('master_mapel', ['id_master_mapel' => $id])->row();

		// Generate clean abbreviation/initials code using helper function
		$kode = generate_kode_mapel($nama, $id);

		$data = [
			'nama' => $nama,
			'jenis_lembaga' => is_array($jenis_lembaga) ? json_encode($jenis_lembaga) : $jenis_lembaga
		];

		if ($cek) {
			$this->db->where('id_master_mapel', $id)->update('master_mapel', $data);
			$this->db->where('id_master_mapel', $id)->update('mapel', ['nama' => $nama]);
			echo json_encode(['status' => true, 'msg' => 'Update ' . $nama . ' sukses']);
		} else {
			$data['id_master_mapel'] = $id;
			$data['kode_mapel'] = $kode;
			$this->db->insert('master_mapel', $data);
			echo json_encode(['status' => true, 'msg' => 'Insert ' . $nama . ' sukses']);
		}
	}
}
