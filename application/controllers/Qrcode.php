<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qrcode extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->iduser = $this->session->userdata('id_user');
        $this->load->model('Modeldata', 'model');
    }

    public function index()
    {
        $this->load->view('qr_view');
    }

    public function getToken($length = 10)
    {
        $cek = $this->db
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('qrcode')
            ->row();

        if (!$cek || $cek->used == 1) {
            $token = substr(bin2hex(random_bytes(10)), 0, $length);
            $save = $this->db->insert('qrcode', ['token' => $token]);
            if ($save) {
                echo json_encode(['token' => $token]);
            } else {
                echo json_encode(['token' => '']);
            }
        } else {
            echo json_encode(['token' => $cek->token]);
        }
    }

    public function checkStatus()
    {
        $cek = $this->db
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('qrcode')
            ->row();

        if ($cek->used == 1) {
            echo json_encode(['used' => true]);
        } else {
            echo json_encode(['used' => false]);
        }
    }

    public function scan($jenis)
    {
        $this->mustLogin();

        $data['title'] = "Absensi Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "kehadiranguru";

        $data['jenis'] = $jenis;
        $dtlUser = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
        $cek = $this->model->getBy2('kehadiran_guru', 'id_guru', $dtlUser->id_guru, 'tanggal', date('Y-m-d'))->row();

        if ($jenis == 'masuk' && $cek) {
            $this->session->set_flashdata('error', 'Absensi masuk sudah ada');
            redirect('home');
            exit;
        } elseif ($jenis == 'pulang' && $cek->pulang != null) {
            $this->session->set_flashdata('error', 'Absensi masuk pulang ada');
            redirect('home');
            exit;
        }

        $this->load->view('scan', $data);
    }

    public function sendScan($jenis)
    {
        $this->mustLogin();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $token = $data['token'] ?? null;

        $cekToken = $this->db->query("SELECT * FROM qrcode WHERE token = '$token' ")->row();
        $dtlUser = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();

        if (!$cekToken) {
            echo json_encode(['valid' => false, 'message' => 'QR Code tidak valid']);
            exit;
        }

        if ($cekToken && $cekToken->used == 1) {
            echo json_encode(['valid' => false, 'message' => 'QR Code expired']);
            exit;
        }

        if ($jenis == 'masuk') {
            $add = $this->model->tambah('kehadiran_guru', [
                'id_guru' => $dtlUser->id_guru,
                'tanggal' => date('Y-m-d'),
                'ket' => 'hadir',
                'waktu' => date('H:i:s')
            ]);
        } else {
            $add = $this->model->edit('kehadiran_guru', 'id_guru', $dtlUser->id_guru, [
                'pulang' => date('H:i:s')
            ]);
        }

        if ($add) {
            $this->db->query("UPDATE qrcode SET used = 1 WHERE token = '$token' ");
            echo json_encode(['valid' => true, 'message' => 'Absensi berhasil']);
            exit;
        } else {
            echo json_encode(['valid' => false, 'message' => 'Absensi gagal. Coba lagi']);
            exit;
        }
    }

    public function verifyLocation()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['lat'], $input['lon'])) {
            $this->json(false, 'Data lokasi tidak lengkap');
            return;
        }

        $userLat = floatval($input['lat']);
        $userLon = floatval($input['lon']);

        // === 3 TITIK LOKASI SAH ===
        $locations = [
            ['lat' => -7.762560182146305, 'lon' => 113.421642647389], // Sekolah A  
            ['lat' => -7.762921929327378, 'lon' => 113.42061504208957], // Sekolah B , 
            ['lat' => -6.199000, 'lon' => 106.818200], // Sekolah C
        ];

        $radius = 10; // meter

        foreach ($locations as $loc) {
            if ($this->distance($userLat, $userLon, $loc['lat'], $loc['lon']) <= $radius) {
                $this->json(true, 'Lokasi valid');
                return;
            }
        }

        $this->json(false, 'Anda berada di luar area absensi');
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    private function json($allow, $msg)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'allow' => $allow,
            'message' => $msg
        ]);
        exit;
    }
}
