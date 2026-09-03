<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->mustLogin();
        $this->config->load('api', TRUE);
    }

    /**
     * cURL Client to consume external Pelanggaran API
     */
    private function _requestApi($endpoint, $method = 'GET', $payload = null)
    {
        $base_url = rtrim($this->config->item('pelanggaran_api_url', 'api') ?: 'http://localhost:8000/api', '/');
        $token = $this->config->item('pelanggaran_api_token', 'api') ?: 'sipesan_dwk_bearer_secret_token_key_2026';

        // Check if database setting table overrides API settings
        if ($this->db->table_exists('setting')) {
            $db_url = $this->db->get_where('setting', ['key' => 'pelanggaran_api_url'])->row('isi');
            if (!empty($db_url)) {
                $base_url = rtrim($db_url, '/');
            }
            $db_token = $this->db->get_where('setting', ['key' => 'pelanggaran_api_token'])->row('isi');
            if (!empty($db_token)) {
                $token = $db_token;
            }
        }

        $url = $base_url . '/' . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($payload) ? $payload : json_encode($payload));
            }
        } elseif (strtoupper($method) !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($payload) ? $payload : json_encode($payload));
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $decoded = null;
        if ($response !== false) {
            $decoded = json_decode($response, true);
        }

        return [
            'success'   => ($http_code >= 200 && $http_code < 300),
            'http_code' => $http_code,
            'data'      => $decoded,
            'raw'       => $response,
            'error'     => $error
        ];
    }

    /**
     * Index page - Form Laporan Pelanggaran
     */
    public function index()
    {
        $data['judul'] = 'Laporan Pelanggaran Siswa';
        $data['menu']  = 'pelanggaran';
        $data['sub']   = 'pelanggaran_siswa';

        // Preload points list
        $points_res = $this->_requestApi('/points');
        $points_list = [];

        if ($points_res['success'] && !empty($points_res['data'])) {
            $raw_pts = [];
            if (isset($points_res['data']['data']) && is_array($points_res['data']['data'])) {
                $raw_pts = $points_res['data']['data'];
            } elseif (is_array($points_res['data'])) {
                $raw_pts = $points_res['data'];
            }

            foreach ($raw_pts as $item) {
                $id = $item['id'] ?? ($item['point_id'] ?? ($item['id_point'] ?? 0));
                $nama = $item['nama'] ?? ($item['name'] ?? ($item['pelanggaran'] ?? ($item['keterangan'] ?? '')));
                $point = (int)($item['jumlah_poin'] ?? ($item['point'] ?? ($item['jumlah'] ?? ($item['poin'] ?? ($item['bobot'] ?? ($item['skor'] ?? 0))))));
                $kategori = $item['kategori'] ?? ($item['category'] ?? ($item['jenis'] ?? ($item['tingkat'] ?? 'Pelanggaran')));
                $kode = $item['kode'] ?? ($item['kode_pelanggaran'] ?? '');

                if (!empty($id) && !empty($nama)) {
                    $points_list[] = [
                        'id'          => (int)$id,
                        'kode'        => $kode,
                        'nama'        => $nama,
                        'point'       => $point,
                        'jumlah_poin' => $point,
                        'kategori'    => $kategori
                    ];
                }
            }
        }

        $data['points'] = $points_list;
        $data['api_connected'] = $points_res['success'];
        $data['api_error'] = $points_res['error'] ?: ($points_res['http_code'] == 0 ? 'Tidak dapat terhubung ke server API. Pastikan server aktif.' : '');

        // Render based on user role
        if ($this->session->userdata('level') === 'guru') {
            $this->load->view('guru/pelanggaran', $data);
        } else {
            $this->load->view('admin/pelanggaran', $data);
        }
    }

    /**
     * AJAX endpoint: Search Santri from External API
     * GET /pelanggaran/search_santri?search=budi
     */
    public function search_santri()
    {
        $query = $this->input->get('search', TRUE) ?: ($this->input->get('q', TRUE) ?: '');
        $query = trim($query);

        if (empty($query)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'data'   => []
                ]));
            return;
        }

        $res = $this->_requestApi('/santri?search=' . urlencode($query));

        if (!$res['success']) {
            $error_msg = !empty($res['error']) ? $res['error'] : 'Gagal menghubungi API Santri (Status: ' . $res['http_code'] . ')';
            $this->output
                ->set_content_type('application/json')
                ->set_status_header($res['http_code'] ?: 500)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => $error_msg,
                    'data'    => []
                ]));
            return;
        }

        $raw_data = [];
        if (isset($res['data']['data']) && is_array($res['data']['data'])) {
            $raw_data = $res['data']['data'];
        } elseif (is_array($res['data'])) {
            $raw_data = $res['data'];
        }

        // Normalize santri data structure
        $santri_list = [];
        foreach ($raw_data as $item) {
            $id = $item['santri_id'] ?? ($item['id'] ?? ($item['id_santri'] ?? ''));
            $nama = $item['nama'] ?? ($item['name'] ?? ($item['nama_santri'] ?? ''));
            $nis = $item['nis'] ?? ($item['nisn'] ?? ($item['nomor_induk'] ?? '-'));
            $kelas = $item['kelas'] ?? ($item['nama_kelas'] ?? ($item['rombel'] ?? ($item['k_formal'] ?? '')));
            $kamar = $item['kamar'] ?? ($item['asrama'] ?? ($item['nama_kamar'] ?? ($item['komplek'] ?? '')));
            $foto = $item['foto'] ?? ($item['photo'] ?? ($item['avatar'] ?? ''));

            if (!empty($id) && !empty($nama)) {
                $santri_list[] = [
                    'id'    => $id,
                    'nama'  => $nama,
                    'nis'   => $nis,
                    'kelas' => $kelas,
                    'kamar' => $kamar,
                    'foto'  => $foto,
                    'text'  => $nama . ' (' . $nis . ($kelas ? ' - ' . $kelas : '') . ($kamar ? ' / ' . $kamar : '') . ')'
                ];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $santri_list
            ]));
    }

    /**
     * AJAX endpoint: Get Points/Pelanggaran categories from External API
     * GET /pelanggaran/get_points
     */
    public function get_points()
    {
        $res = $this->_requestApi('/points');

        if (!$res['success']) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header($res['http_code'] ?: 500)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Gagal mengambil data poin pelanggaran dari API.',
                    'data'    => []
                ]));
            return;
        }

        $raw_data = [];
        if (isset($res['data']['data']) && is_array($res['data']['data'])) {
            $raw_data = $res['data']['data'];
        } elseif (is_array($res['data'])) {
            $raw_data = $res['data'];
        }

        $points_list = [];
        foreach ($raw_data as $item) {
            $id = $item['id'] ?? ($item['point_id'] ?? ($item['id_point'] ?? 0));
            $nama = $item['nama'] ?? ($item['name'] ?? ($item['pelanggaran'] ?? ($item['keterangan'] ?? '')));
            $point = (int)($item['jumlah_poin'] ?? ($item['point'] ?? ($item['jumlah'] ?? ($item['poin'] ?? ($item['bobot'] ?? ($item['skor'] ?? 0))))));
            $kategori = $item['kategori'] ?? ($item['category'] ?? ($item['jenis'] ?? ($item['tingkat'] ?? 'Pelanggaran')));
            $kode = $item['kode'] ?? ($item['kode_pelanggaran'] ?? '');

            if (!empty($id) && !empty($nama)) {
                $points_list[] = [
                    'id'          => (int)$id,
                    'kode'        => $kode,
                    'nama'        => $nama,
                    'point'       => $point,
                    'jumlah_poin' => $point,
                    'kategori'    => $kategori
                ];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'data'   => $points_list
            ]));
    }

    /**
     * POST endpoint: Submit Pelanggaran Report to External API
     * POST /pelanggaran/submit
     */
    public function submit()
    {
        // Support both JSON body and standard form POST
        $raw = file_get_contents('php://input');
        $json_data = json_decode($raw, true);

        $santri_id = $json_data['santri_id'] ?? $this->input->post('santri_id', TRUE);
        $point_id  = $json_data['point_id'] ?? $this->input->post('point_id', TRUE);
        $tanggal   = $json_data['tanggal'] ?? $this->input->post('tanggal', TRUE);
        $lokasi    = $json_data['lokasi'] ?? $this->input->post('lokasi', TRUE);
        $kronologi = $json_data['kronologi'] ?? $this->input->post('kronologi', TRUE);
        $pelapor   = $json_data['pelapor'] ?? $this->input->post('pelapor', TRUE);
        $saksi     = $json_data['saksi'] ?? $this->input->post('saksi');

        // Sanitize and Validate
        if (empty($santri_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Santri / Siswa wajib dipilih.'
                ]));
            return;
        }

        if (empty($point_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Jenis poin pelanggaran wajib dipilih.'
                ]));
            return;
        }

        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        if (empty($lokasi)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Lokasi kejadian wajib diisi.'
                ]));
            return;
        }

        if (empty($kronologi)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Kronologi kejadian wajib diisi.'
                ]));
            return;
        }

        if (empty($pelapor)) {
            $pelapor = $this->session->userdata('nama_user') ?: 'Guru / Pembina';
        }

        // Format saksi array
        $saksi_arr = [];
        if (is_array($saksi)) {
            foreach ($saksi as $s) {
                $trimmed = trim((string)$s);
                if (!empty($trimmed)) {
                    $saksi_arr[] = $trimmed;
                }
            }
        } elseif (is_string($saksi) && !empty(trim($saksi))) {
            // Check if JSON encoded array string
            $decoded_saksi = json_decode($saksi, true);
            if (is_array($decoded_saksi)) {
                $saksi_arr = array_values(array_filter(array_map('trim', $decoded_saksi)));
            } else {
                // Comma separated
                $saksi_arr = array_values(array_filter(array_map('trim', explode(',', $saksi))));
            }
        }

        // Build Payload
        $payload = [
            'santri_id' => (string)$santri_id,
            'point_id'  => (int)$point_id,
            'tanggal'   => (string)$tanggal,
            'lokasi'    => (string)$lokasi,
            'kronologi' => (string)$kronologi,
            'pelapor'   => (string)$pelapor,
            'saksi'     => $saksi_arr
        ];

        // Send POST request to External API
        $res = $this->_requestApi('/pelanggaran', 'POST', $payload);

        if ($res['success']) {
            $message = 'Laporan pelanggaran siswa berhasil dikirim dan dicatat ke sistem pembinaan.';
            if (!empty($res['data']['message'])) {
                $message = $res['data']['message'];
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => $message,
                    'data'    => $res['data'] ?? []
                ]));
        } else {
            $error_msg = 'Gagal menyimpan laporan pelanggaran.';
            if (!empty($res['data']['message'])) {
                $error_msg = $res['data']['message'];
            } elseif (!empty($res['data']['error'])) {
                $error_msg = is_string($res['data']['error']) ? $res['data']['error'] : json_encode($res['data']['error']);
            } elseif (!empty($res['error'])) {
                $error_msg = 'Koneksi ke API Pelanggaran gagal: ' . $res['error'];
            } elseif ($res['http_code'] == 0) {
                $error_msg = 'Tidak dapat terhubung ke server API (http://localhost:8000). Pastikan server backend API sedang aktif.';
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header($res['http_code'] ?: 500)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => $error_msg,
                    'details' => $res['data'] ?? []
                ]));
        }
    }
}
