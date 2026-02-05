<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sinkron extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Modeldata', 'model');

		$this->config->load('api', true);
		$this->token = $this->config->item('ppdwk_token', 'api');

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
	public function lembaga()
	{
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
		// var_dump($detail);
		// exit();

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
				}
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


		$this->db->trans_start();

		// ======================
		// 1. SIMPAN / UPDATE lembaga
		// ======================

		$datalembaga = [
			'nama'     => $lembaga['nama'],
			'npsn'     => $lembaga['npsn'],
			'jenjang'     => $lembaga['jenjang'],
			'alamat'     => $lembaga['alamat'],
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

		if ($httpCode !== 200) {
			log_message('error', "DETAIL PTK FAIL [$url]: " . $result);
			return null;
		}

		$json = json_decode($result, true);
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
		}

		if ($sql) {
			echo json_encode(['status' => true]);
		} else {
			echo json_encode(['status' => false]);
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
		$this->db
			->where('id_guru', $idGuru)
			->delete('registrasi');
		// var_dump($detail);
		// var_dump($detail);
		// exit();

		if ($detail && isset($detail['registrasi_ptk'])) {

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
				}
			}
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'msg' => 'Guru ' . $guru['nama'] . ' + registrasi tersinkron'
		]);
	}
}
