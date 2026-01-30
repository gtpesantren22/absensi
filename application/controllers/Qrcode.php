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
            echo json_encode(['valid' => true, 'message' => 'Absensi berhasil']);
            exit;
        } else {
            echo json_encode(['valid' => false, 'message' => 'Absensi gagal. Coba lagi']);
            exit;
        }
    }
}
