<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Setup headers for API output
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, X-API-KEY, Content-Type');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }

    private function _authenticate()
    {
        $token = null;

        // Try Authorization header
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        if ($auth_header) {
            if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
                $token = $matches[1];
            } else {
                $token = trim($auth_header);
            }
        }

        // Try X-API-KEY header if not found
        if (!$token) {
            $token = $this->input->get_request_header('X-API-KEY', TRUE);
        }

        // Try GET parameter if not found
        if (!$token) {
            $token = $this->input->get('api_key', TRUE);
        }

        // Load config token fallback
        $this->config->load('api', TRUE);
        $configured_token = $this->config->item('api_token', 'api') ?: 'absensi_api_token_secret_xyz';

        // Check if database overrides the token
        $db_token = $this->db->get_where('setting', ['key' => 'api_token'])->row('isi');
        if ($db_token) {
            $configured_token = $db_token;
        }

        if (empty($token) || $token !== $configured_token) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized: Invalid or missing API key / bearer token'
                ]));
            return FALSE;
        }

        return TRUE;
    }

    public function jam_mengajar()
    {
        if (!$this->_authenticate()) {
            return;
        }

        // 1. Total jam mengajar dari setiap guru secara keseluruhan
        $total_jam_mengajar = $this->db->query("
            SELECT g.id_guru, g.nama, g.kode_guru,
                   COALESCE(SUM(j.jam_sampai - j.jam_dari + 1), 0) as total_jp
            FROM guru g
            LEFT JOIN jadwal j ON g.id_guru = j.id_guru
            GROUP BY g.id_guru, g.nama, g.kode_guru
            ORDER BY total_jp DESC, g.nama ASC
        ")->result_array();

        // Cast total_jp to integer
        foreach ($total_jam_mengajar as &$t) {
            $t['total_jp'] = (int)$t['total_jp'];
        }

        // 2. Jumlah jam mengajar/jp setiap guru dari setiap lembaga yang terregistrasi
        $jam_mengajar_per_lembaga = $this->db->query("
            SELECT r.id_lembaga, l.nama as nama_lembaga, g.id_guru, g.nama as nama_guru, g.kode_guru,
                   COALESCE(SUM(j.jam_sampai - j.jam_dari + 1), 0) as jp
            FROM registrasi r
            JOIN guru g ON r.id_guru = g.id_guru
            JOIN lembaga l ON r.id_lembaga = l.id_lembaga
            LEFT JOIN jadwal j ON g.id_guru = j.id_guru AND j.id_lembaga = r.id_lembaga
            GROUP BY r.id_lembaga, l.nama, g.id_guru, g.nama, g.kode_guru
            ORDER BY l.nama ASC, jp DESC, g.nama ASC
        ")->result_array();

        // Cast jp to integer
        foreach ($jam_mengajar_per_lembaga as &$jl) {
            $jl['jp'] = (int)$jl['jp'];
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data' => [
                    'total_jam_mengajar' => $total_jam_mengajar,
                    'jam_mengajar_per_lembaga' => $jam_mengajar_per_lembaga
                ]
            ]));
    }

    public function guru($id_guru = null)
    {
        if (!$this->_authenticate()) {
            return;
        }

        if (!$id_guru) {
            $id_guru = $this->input->get('id_guru', TRUE);
        }

        if (empty($id_guru)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Bad Request: Missing id_guru parameter'
                ]));
            return;
        }

        // Get teacher info
        $guru = $this->db->get_where('guru', ['id_guru' => $id_guru])->row_array();
        if (!$guru) {
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Not Found: Guru not found'
                ]));
            return;
        }

        // Query 1: Total jam mengajar dari guru ini
        $total_jam_mengajar = $this->db->query("
            SELECT COALESCE(SUM(jam_sampai - jam_dari + 1), 0) as total_jp
            FROM jadwal
            WHERE id_guru = ?
        ", [$id_guru])->row('total_jp');

        // Query 2: Detail jam mengajar per lembaga
        $jam_mengajar_per_lembaga = $this->db->query("
            SELECT r.id_lembaga, l.nama as nama_lembaga,
                   COALESCE(SUM(j.jam_sampai - j.jam_dari + 1), 0) as jp
            FROM registrasi r
            JOIN lembaga l ON r.id_lembaga = l.id_lembaga
            LEFT JOIN jadwal j ON j.id_guru = r.id_guru AND j.id_lembaga = r.id_lembaga
            WHERE r.id_guru = ?
            GROUP BY r.id_lembaga, l.nama
            ORDER BY l.nama ASC
        ", [$id_guru])->result_array();

        // Cast values
        $total_jp = (int)$total_jam_mengajar;
        foreach ($jam_mengajar_per_lembaga as &$jl) {
            $jl['jp'] = (int)$jl['jp'];
        }

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data' => [
					'id_guru' => $guru['id_guru'],
					'nama' => $guru['nama'],
					'kode_guru' => $guru['kode_guru'],
					'warna' => $guru['warna'],
					'total_jam_mengajar' => $total_jp,
					'jam_mengajar_per_lembaga' => $jam_mengajar_per_lembaga
				]
			]));
	}

	public function rombel()
	{
		if (!$this->_authenticate()) {
			return;
		}

		$results = $this->db->query("
			SELECT l.id_lembaga, l.nama as nama_lembaga, k.id_kelas, k.nama as nama_kelas, k.jenis,
				   COUNT(r.id_siswa) as jumlah_anggota
			FROM kelas k
			JOIN lembaga l ON k.id_lembaga = l.id_lembaga
			LEFT JOIN rombel r ON r.id_kelas = k.id_kelas
			GROUP BY k.id_kelas, l.id_lembaga, l.nama, k.nama, k.jenis
			ORDER BY l.nama ASC, k.nama ASC
		")->result_array();

		$data = [];
		foreach ($results as $row) {
			$id_lembaga = $row['id_lembaga'];
			if (!isset($data[$id_lembaga])) {
				$data[$id_lembaga] = [
					'id_lembaga' => $id_lembaga,
					'nama_lembaga' => $row['nama_lembaga'],
					'rombel' => []
				];
			}
			$data[$id_lembaga]['rombel'][] = [
				'id_kelas' => $row['id_kelas'],
				'nama_kelas' => $row['nama_kelas'],
				'jenis' => $row['jenis'],
				'jumlah_anggota' => (int)$row['jumlah_anggota']
			];
		}

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data' => array_values($data)
			]));
	}

	public function jadwal()
	{
		if (!$this->_authenticate()) {
			return;
		}

		$id_lembaga = $this->input->get('id_lembaga', TRUE);
		$hari = $this->input->get('hari', TRUE);
		$id_kelas = $this->input->get('id_kelas', TRUE);
		$id_guru = $this->input->get('id_guru', TRUE);

		$this->db->select('j.id_jadwal, j.hari, j.jam_dari, j.jam_sampai,
						   k.id_kelas, k.nama as nama_kelas,
						   m.id_mapel, m.nama as nama_mapel, m.kode_mapel,
						   g.id_guru, g.nama as nama_guru, g.kode_guru, g.warna as warna_guru,
						   l.id_lembaga, l.nama as nama_lembaga');
		$this->db->from('jadwal j');
		$this->db->join('kelas k', 'j.id_kelas = k.id_kelas', 'left');
		$this->db->join('mapel m', 'j.id_mapel = m.id_mapel', 'left');
		$this->db->join('guru g', 'j.id_guru = g.id_guru', 'left');
		$this->db->join('lembaga l', 'j.id_lembaga = l.id_lembaga', 'left');

		if (!empty($id_lembaga)) {
			$this->db->where('j.id_lembaga', $id_lembaga);
		}
		if (!empty($hari)) {
			$this->db->where('j.hari', $hari);
		}
		if (!empty($id_kelas)) {
			$this->db->where('j.id_kelas', $id_kelas);
		}
		if (!empty($id_guru)) {
			$this->db->where('j.id_guru', $id_guru);
		}

		$this->db->order_by('l.nama', 'ASC');
		$this->db->order_by('j.hari', 'ASC');
		$this->db->order_by('j.jam_dari', 'ASC');
		$this->db->order_by('k.nama', 'ASC');

		$jadwal = $this->db->get()->result_array();

		// Cast integer values
		foreach ($jadwal as &$item) {
			$item['jam_dari'] = (int)$item['jam_dari'];
			$item['jam_sampai'] = (int)$item['jam_sampai'];
		}

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data' => $jadwal
			]));
	}
}
