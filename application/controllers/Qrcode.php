<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qrcode extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
        $this->load->model('Modeldata', 'model');

        // Automatically create terminal tables if they do not exist
        if (!$this->db->table_exists('terminal_device')) {
            $this->db->query("CREATE TABLE `terminal_device` (
                `id_device` INT AUTO_INCREMENT PRIMARY KEY,
                `device_token` VARCHAR(64) NOT NULL UNIQUE,
                `device_name` VARCHAR(100) DEFAULT NULL,
                `id_lembaga` VARCHAR(36) DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if (!$this->db->table_exists('terminal_pairing')) {
            $this->db->query("CREATE TABLE `terminal_pairing` (
                `id_pairing` INT AUTO_INCREMENT PRIMARY KEY,
                `pairing_id` VARCHAR(64) NOT NULL UNIQUE,
                `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
                `device_token` VARCHAR(64) DEFAULT NULL,
                `created_at` DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if (!$this->db->table_exists('pembiasaan_siswa')) {
            $this->db->query("CREATE TABLE `pembiasaan_siswa` (
                `id_pembiasaan_siswa` INT AUTO_INCREMENT PRIMARY KEY,
                `id_siswa` VARCHAR(36) NOT NULL,
                `tanggal` DATE NOT NULL,
                `jam_masuk` TIME DEFAULT NULL,
                `jam_pulang` TIME DEFAULT NULL,
                `ket` VARCHAR(20) NOT NULL DEFAULT 'hadir',
                `id_lembaga` VARCHAR(36) NOT NULL,
                `id_semester` INT(10) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_siswa_tgl` (`id_siswa`, `tanggal`),
                INDEX `idx_lembaga_semester` (`id_lembaga`, `id_semester`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if (!$this->db->field_exists('nis', 'siswa')) {
            $this->db->query("ALTER TABLE `siswa` ADD COLUMN `nis` VARCHAR(50) NULL DEFAULT NULL AFTER `id_siswa`");
            $index_exists = $this->db->query("SHOW INDEX FROM `siswa` WHERE Key_name = 'idx_nis'")->num_rows();
            if (!$index_exists) {
                $this->db->query("ALTER TABLE `siswa` ADD UNIQUE INDEX `idx_nis` (`nis`)");
            }
        }
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
            ->where('used', 0)
            ->get('qrcode')
            ->num_rows();
        if ($cek > 0) {
            echo json_encode(['ready' => true]);
        } else {
            echo json_encode(['ready' => false]);
        }
    }

    public function getActiveToken()
    {
        $cek = $this->db
            ->where('used', 0)
            ->get('qrcode')
            ->row();
        if ($cek) {
            echo json_encode(['token' => $cek->token]);
        } else {
            echo json_encode(['token' => '']);
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

		if ($this->session->userdata('level') === 'guru') {
			$this->load->view('guru/scan', $data);
		} else {
			$this->load->view('scan', $data);
		}
	}

    public function sendScan($jenis)
    {
        $this->mustLogin();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $token = $data['token'] ?? null;
        $lat = $data['lat'] ?? null;
        $lon = $data['lon'] ?? null;
        $accuracy = $data['accuracy'] ?? null;

        // Perform GPS location & Anti-Fake-GPS validation (Disabled)
        /*
        $checkLoc = $this->checkLocationValid($lat, $lon, $accuracy);
        if (!$checkLoc['allow']) {
            echo json_encode(['valid' => false, 'message' => $checkLoc['message']]);
            exit;
        }
        */

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

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $cekExist = $this->db->get_where('kehadiran_guru', [
            'id_guru' => $dtlUser->id_guru,
            'tanggal' => date('Y-m-d'),
            'id_semester' => $id_semester_aktif
        ])->row();

        if ($jenis == 'masuk') {
            if ($cekExist) {
                $updateData = [
                    'ket' => 'hadir',
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ];
                if (empty($cekExist->waktu) || $cekExist->waktu === '00:00:00') {
                    $updateData['waktu'] = date('H:i:s');
                }
                $add = $this->model->edit2('kehadiran_guru', 'id_guru', $dtlUser->id_guru, 'tanggal', date('Y-m-d'), $updateData);
            } else {
                $add = $this->model->tambah('kehadiran_guru', [
                    'id_guru' => $dtlUser->id_guru,
                    'tanggal' => date('Y-m-d'),
                    'ket' => 'hadir',
                    'waktu' => date('H:i:s'),
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ]);
            }
            $this->db->query("UPDATE qrcode SET used = 1 WHERE token = '$token' ");
        } else {
            if ($cekExist) {
                $add = $this->model->edit2('kehadiran_guru', 'id_guru', $dtlUser->id_guru, 'tanggal', date('Y-m-d'), [
                    'pulang' => date('H:i:s'),
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ]);
            } else {
                $add = $this->model->tambah('kehadiran_guru', [
                    'id_guru' => $dtlUser->id_guru,
                    'tanggal' => date('Y-m-d'),
                    'ket' => 'hadir',
                    'pulang' => date('H:i:s'),
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ]);
            }
            $this->db->query("UPDATE qrcode SET used = 1 WHERE token = '$token' ");
        }

        if ($add) {
            echo json_encode(['valid' => true, 'message' => 'Absensi berhasil']);
            exit;
        } else {
            echo json_encode(['valid' => false, 'message' => 'Absensi gagal. Coba lagi']);
            exit;
        }
    }

    private function checkLocationValid($userLat, $userLon, $accuracy = null)
    {
        if (empty($userLat) || empty($userLon)) {
            return ['allow' => false, 'message' => 'Harap aktifkan Izin Lokasi (GPS) di HP Anda'];
        }

        // Anti-Fake GPS check
        if ($accuracy !== null && $accuracy !== '') {
            $accFloat = floatval($accuracy);
            if ($accFloat < 2.0) {
                return ['allow' => false, 'message' => 'Absensi Ditolak: Terdeteksi penggunaan Fake GPS / Mock Location'];
            }
            if ($accFloat > 50.0) {
                return ['allow' => false, 'message' => 'Absensi Ditolak: Sinyal GPS di HP kurang presisi (> 50m). Harap gunakan GPS Akurasi Tinggi'];
            }
        }

        $locations = [
            ['lat' => -7.762560182146305, 'lon' => 113.421642647389], // Kantor Pesantren
            ['lat' => -7.762921929327378, 'lon' => 113.42061504208957], //Pos Blakang
            ['lat' => -7.756998490707694, 'lon' => 113.4230718505036], //RA
            ['lat' => -7.762236379980296, 'lon' => 113.42135752295482], //Kantor Putri
            ['lat' => -7.762615239821377, 'lon' => 113.42080028623307], //SMK
            ['lat' => -7.769032046442462, 'lon' => 113.46365920898806], //Test  - perlu ganti
            ['lat' => -7.7566220, 'lon' => 113.4226588], //MI
            ['lat' => -7.76244486747453, 'lon' => 113.42093116617477], //Kantor MA
            ['lat' => -7.762676745693598, 'lon' => 113.4217539337592], //Kantor SMP
            ['lat' => -7.762640867696531, 'lon' => 113.42149644172208], //Kantor MTs
        ];

        $radius = 30; // meter

        foreach ($locations as $loc) {
            if ($this->distance(floatval($userLat), floatval($userLon), $loc['lat'], $loc['lon']) <= $radius) {
                return ['allow' => true, 'message' => 'Lokasi valid'];
            } 
        }

        return ['allow' => false, 'message' => 'Absensi Ditolak: HP Anda terdeteksi berada di luar area lokasi sekolah'];
    }

    public function verifyLocation()
    {
        // verification disabled as requested
        $this->json(true, 'Lokasi valid');
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

    public function sendScanCard()
    {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $qr_code = trim($data['qr_code'] ?? '');
        $jenis   = trim($data['jenis'] ?? 'auto'); // 'auto', 'masuk', 'pulang'
        $terminal_token = trim($data['terminal_token'] ?? '');

        if (empty($qr_code)) {
            echo json_encode(['valid' => false, 'message' => 'Data QR Code kosong']);
            exit;
        }

        // Dynamically get fields of the 'guru' table to avoid query crash if 'nik' or 'nip' columns do not exist in the database
        $fields = $this->db->list_fields('guru');
        $where_clauses = ["id_guru = ?"];
        $params = [$qr_code];

        if (in_array('kode_guru', $fields)) {
            $where_clauses[] = "kode_guru = ?";
            $params[] = $qr_code;
        }
        if (in_array('nik', $fields)) {
            $where_clauses[] = "nik = ?";
            $params[] = $qr_code;
        }
        if (in_array('nip', $fields)) {
            $where_clauses[] = "nip = ?";
            $params[] = $qr_code;
        }

        $query_str = "SELECT * FROM guru WHERE " . implode(" OR ", $where_clauses) . " LIMIT 1";
        $guru = $this->db->query($query_str, $params)->row();

        if (!$guru) {
            // Check if it matches a student
            $siswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = ? OR nis = ? OR nisn = ? LIMIT 1", [$qr_code, $qr_code, $qr_code])->row();
            
            if ($siswa) {
                // Yes, it is a student! Let's process student attendance
                $regSiswa = $this->db->get_where('registrasi_siswa', ['id_siswa' => $siswa->id_siswa])->row();
                if (!$regSiswa) {
                    echo json_encode(['valid' => false, 'message' => 'Siswa ' . $siswa->nama . ' tidak terdaftar di lembaga mana pun.']);
                    exit;
                }
                
                $id_lembaga_siswa = $regSiswa->id_lembaga;
                
                // If scanned from terminal device, match the institution
                if (!empty($terminal_token)) {
                    $device = $this->db->get_where('terminal_device', ['device_token' => $terminal_token, 'is_active' => 1])->row();
                    if ($device && $device->id_lembaga && $device->id_lembaga !== $id_lembaga_siswa) {
                        echo json_encode(['valid' => false, 'message' => 'Siswa ' . $siswa->nama . ' bukan dari lembaga terminal ini.']);
                        exit;
                    }
                }
                
                $id_semester_aktif = $this->session->userdata('id_semester_aktif');
                if (empty($id_semester_aktif)) {
                    $active_sem = $this->db->get_where('semester', ['is_active' => 1])->row();
                    $id_semester_aktif = $active_sem ? $active_sem->id_semester : null;
                }

                // Check today's attendance record
                $cekExist = $this->db->get_where('pembiasaan_siswa', [
                    'id_siswa' => $siswa->id_siswa,
                    'tanggal'  => date('Y-m-d')
                ])->row();

                // Determine scan type if 'auto'
                if ($jenis === 'auto') {
                    if (!$cekExist || empty($cekExist->jam_masuk) || $cekExist->jam_masuk === '00:00:00') {
                        $actJenis = 'masuk';
                    } else {
                        $actJenis = 'pulang';
                    }
                } else {
                    $actJenis = $jenis;
                }

                // Guard Rail: Enforce a minimum interval of 2 minutes between masuk and pulang for students
                if ($actJenis === 'pulang' && $jenis === 'auto') {
                    if ($cekExist && !empty($cekExist->jam_masuk) && $cekExist->jam_masuk !== '00:00:00') {
                        $masukTime = strtotime($cekExist->jam_masuk);
                        $currentTime = time();
                        $diffSeconds = $currentTime - $masukTime;
                        if ($diffSeconds < 120) { // 2 minutes cooldown
                            echo json_encode([
                                'valid' => false,
                                'message' => 'Absensi Ditolak: Baru saja melakukan Absensi Masuk. Silakan lakukan Absensi Pulang nanti.'
                            ]);
                            exit;
                        }
                    }
                }

                $currentTime = date('H:i:s');
                $success = false;
                
                if ($actJenis === 'masuk') {
                    if ($cekExist && !empty($cekExist->jam_masuk) && $cekExist->jam_masuk !== '00:00:00') {
                        echo json_encode([
                            'valid' => false,
                            'message' => 'Siswa ' . $siswa->nama . ' sudah melakukan Absensi Masuk hari ini pukul ' . date('H:i', strtotime($cekExist->jam_masuk))
                        ]);
                        exit;
                    }

                    if ($cekExist) {
                        $updateData = [
                            'jam_masuk'  => $currentTime,
                            'ket'        => 'hadir',
                            'id_lembaga' => $id_lembaga_siswa
                        ];
                        if (!empty($id_semester_aktif)) {
                            $updateData['id_semester'] = $id_semester_aktif;
                        }
                        $success = $this->db->update('pembiasaan_siswa', $updateData, ['id_pembiasaan_siswa' => $cekExist->id_pembiasaan_siswa]);
                    } else {
                        $insertData = [
                            'id_siswa'   => $siswa->id_siswa,
                            'tanggal'    => date('Y-m-d'),
                            'jam_masuk'  => $currentTime,
                            'ket'        => 'hadir',
                            'id_lembaga' => $id_lembaga_siswa
                        ];
                        if (!empty($id_semester_aktif)) {
                            $insertData['id_semester'] = $id_semester_aktif;
                        }
                        $success = $this->db->insert('pembiasaan_siswa', $insertData);
                    }
                    $scanMessage = 'Absen MASUK Berhasil: ' . $siswa->nama;

                } else { // pulang
                    if ($cekExist && !empty($cekExist->jam_pulang) && $cekExist->jam_pulang !== '00:00:00') {
                        echo json_encode([
                            'valid' => false,
                            'message' => 'Siswa ' . $siswa->nama . ' sudah melakukan Absensi Pulang hari ini pukul ' . date('H:i', strtotime($cekExist->jam_pulang))
                        ]);
                        exit;
                    }

                    if ($cekExist) {
                        $updateData = [
                            'jam_pulang' => $currentTime,
                            'id_lembaga' => $id_lembaga_siswa
                        ];
                        if (!empty($id_semester_aktif)) {
                            $updateData['id_semester'] = $id_semester_aktif;
                        }
                        $success = $this->db->update('pembiasaan_siswa', $updateData, ['id_pembiasaan_siswa' => $cekExist->id_pembiasaan_siswa]);
                    } else {
                        $insertData = [
                            'id_siswa'   => $siswa->id_siswa,
                            'tanggal'    => date('Y-m-d'),
                            'jam_pulang' => $currentTime,
                            'ket'        => 'hadir',
                            'id_lembaga' => $id_lembaga_siswa
                        ];
                        if (!empty($id_semester_aktif)) {
                            $insertData['id_semester'] = $id_semester_aktif;
                        }
                        $success = $this->db->insert('pembiasaan_siswa', $insertData);
                    }
                    $scanMessage = 'Absen PULANG Berhasil: ' . $siswa->nama;
                }

                if ($success) {
                    echo json_encode([
                        'valid'   => true,
                        'message' => $scanMessage,
                        'guru'    => $siswa->nama, // mapped to front-end res.guru
                        'waktu'   => date('H:i:s'),
                        'type'    => strtoupper($actJenis)
                    ]);
                } else {
                    echo json_encode(['valid' => false, 'message' => 'Gagal menyimpan absensi siswa ke server.']);
                }
                exit;
            }

            echo json_encode(['valid' => false, 'message' => 'Kartu tidak terdaftar (ID/NIS: ' . htmlspecialchars($qr_code) . ')']);
            exit;
        }

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        if (empty($id_semester_aktif)) {
            $active_sem = $this->db->get_where('semester', ['is_active' => 1])->row();
            $id_semester_aktif = $active_sem ? $active_sem->id_semester : null;
        }

        // Check if scanned from paired terminal device, to assign to that terminal's institution
        $id_lembaga_target = 0;
        if (!empty($terminal_token)) {
            $device = $this->db->get_where('terminal_device', ['device_token' => $terminal_token, 'is_active' => 1])->row();
            if ($device && $device->id_lembaga) {
                $id_lembaga_target = $device->id_lembaga;
            }
        }

        if (empty($id_lembaga_target)) {
            $regGuru = $this->db->query("SELECT id_lembaga FROM registrasi WHERE id_guru = '$guru->id_guru' LIMIT 1")->row();
            $id_lembaga_target = $regGuru ? $regGuru->id_lembaga : ($this->id_lembaga ?: 0);
        }

        // Check today's attendance record
        $cekExist = $this->db->get_where('kehadiran_guru', [
            'id_guru' => $guru->id_guru,
            'tanggal' => date('Y-m-d')
        ])->row();

        // Determine scan type if 'auto'
        if ($jenis === 'auto') {
            if (!$cekExist || empty($cekExist->waktu) || $cekExist->waktu === '00:00:00') {
                $actJenis = 'masuk';
            } else {
                $actJenis = 'pulang';
            }
        } else {
            $actJenis = $jenis;
        }

        // Guard Rail: Enforce a minimum interval of 10 minutes (600 seconds) between masuk and pulang in 'auto' mode
        if ($actJenis === 'pulang' && $jenis === 'auto') {
            if ($cekExist && !empty($cekExist->waktu) && $cekExist->waktu !== '00:00:00') {
                $masukTime = strtotime($cekExist->waktu);
                $currentTime = time();
                $diffSeconds = $currentTime - $masukTime;
                if ($diffSeconds < 600) { // 10 minutes
                    $remainingSeconds = 600 - $diffSeconds;
                    $remainingMinutes = ceil($remainingSeconds / 60);
                    echo json_encode([
                        'valid' => false,
                        'message' => 'Absensi Ditolak: Anda baru saja melakukan Absensi Masuk. Silakan lakukan Absensi Pulang nanti (tunggu ' . $remainingMinutes . ' menit lagi).'
                    ]);
                    exit;
                }
            }
        }

        if ($actJenis === 'masuk') {
            if ($cekExist && !empty($cekExist->waktu) && $cekExist->waktu !== '00:00:00') {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Guru ' . $guru->nama . ' sudah melakukan Absensi Masuk hari ini pukul ' . date('H:i', strtotime($cekExist->waktu))
                ]);
                exit;
            }

            if ($cekExist) {
                $updateData = [
                    'ket' => 'hadir',
                    'waktu' => date('H:i:s'),
                    'id_lembaga' => $id_lembaga_target
                ];
                if (!empty($id_semester_aktif)) {
                    $updateData['id_semester'] = $id_semester_aktif;
                }
                $add = $this->model->edit2('kehadiran_guru', 'id_guru', $guru->id_guru, 'tanggal', date('Y-m-d'), $updateData);
            } else {
                $insertData = [
                    'id_guru' => $guru->id_guru,
                    'tanggal' => date('Y-m-d'),
                    'ket' => 'hadir',
                    'waktu' => date('H:i:s'),
                    'id_lembaga' => $id_lembaga_target
                ];
                if (!empty($id_semester_aktif)) {
                    $insertData['id_semester'] = $id_semester_aktif;
                }
                $add = $this->model->tambah('kehadiran_guru', $insertData);
            }

            $scanMessage = 'Absensi MASUK Berhasil: ' . $guru->nama;

        } else { // 'pulang'
            if ($cekExist && !empty($cekExist->pulang) && $cekExist->pulang !== '00:00:00') {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Guru ' . $guru->nama . ' sudah melakukan Absensi Pulang hari ini pukul ' . date('H:i', strtotime($cekExist->pulang))
                ]);
                exit;
            }

            if ($cekExist) {
                $updateData = [
                    'pulang' => date('H:i:s'),
                    'id_lembaga' => $id_lembaga_target
                ];
                if (!empty($id_semester_aktif)) {
                    $updateData['id_semester'] = $id_semester_aktif;
                }
                $add = $this->model->edit2('kehadiran_guru', 'id_guru', $guru->id_guru, 'tanggal', date('Y-m-d'), $updateData);
            } else {
                $insertData = [
                    'id_guru' => $guru->id_guru,
                    'tanggal' => date('Y-m-d'),
                    'ket' => 'hadir',
                    'pulang' => date('H:i:s'),
                    'id_lembaga' => $id_lembaga_target
                ];
                if (!empty($id_semester_aktif)) {
                    $insertData['id_semester'] = $id_semester_aktif;
                }
                $add = $this->model->tambah('kehadiran_guru', $insertData);
            }

            $scanMessage = 'Absensi PULANG Berhasil: ' . $guru->nama;
        }

        if ($add) {
            echo json_encode([
                'valid' => true,
                'message' => $scanMessage,
                'guru' => $guru->nama,
                'waktu' => date('H:i:s'),
                'type' => strtoupper($actJenis)
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'message' => 'Gagal menyimpan absensi untuk ' . $guru->nama
            ]);
        }
        exit;
    }

    public function qrmulti()
    {
        $app_name = 'Absensi Sekolah';
        $app_logo = '';
        if ($this->db->table_exists('setting')) {
            $row_name = $this->db->get_where('setting', ['key' => 'app_name'])->row();
            if ($row_name) {
                $app_name = $row_name->isi;
            }
            $row_logo = $this->db->get_where('setting', ['key' => 'app_logo'])->row();
            if ($row_logo) {
                $app_logo = $row_logo->isi;
            }
        }

        $data['app_name'] = $app_name;
        $data['app_logo'] = $app_logo;
        $this->load->view('qr_multi', $data);
    }

    public function checkTerminalToken()
    {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $token = $data['token'] ?? '';

        if (empty($token)) {
            echo json_encode(['valid' => false]);
            exit;
        }

        $device = $this->db->get_where('terminal_device', ['device_token' => $token, 'is_active' => 1])->row();
        if ($device) {
            // Find corresponding school name
            $lembaga_nama = 'Semua Lembaga';
            if ($device->id_lembaga) {
                $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $device->id_lembaga])->row();
                if ($lembaga) {
                    $lembaga_nama = $lembaga->nama;
                }
            }
            echo json_encode([
                'valid' => true, 
                'device_name' => $device->device_name,
                'lembaga_nama' => $lembaga_nama,
                'id_lembaga' => $device->id_lembaga
            ]);
        } else {
            echo json_encode(['valid' => false]);
        }
        exit;
    }

    public function generatePairingSession()
    {
        header('Content-Type: application/json');
        $pairing_id = bin2hex(random_bytes(20));
        
        $this->db->insert('terminal_pairing', [
            'pairing_id' => $pairing_id,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'pairing_id' => $pairing_id, 
            'pairing_url' => base_url('qrcode/authorizeTerminal/' . $pairing_id)
        ]);
        exit;
    }

    public function checkPairingStatus($pairing_id)
    {
        header('Content-Type: application/json');
        $pairing_id = trim($pairing_id);

        $pairing = $this->db->get_where('terminal_pairing', ['pairing_id' => $pairing_id])->row();
        if ($pairing) {
            if ($pairing->status === 'paired') {
                echo json_encode(['status' => 'paired', 'device_token' => $pairing->device_token]);
            } else {
                echo json_encode(['status' => 'pending']);
            }
        } else {
            echo json_encode(['status' => 'not_found']);
        }
        exit;
    }

    public function authorizeTerminal($pairing_id)
    {
        // Enforce Super Admin login
        if (!$this->session->userdata('login')) {
            $this->session->set_userdata('redirect_url', 'qrcode/authorizeTerminal/' . $pairing_id);
            redirect('auth');
            exit;
        }

        if ($this->session->userdata('level') !== 'super_admin') {
            $data['current_user'] = $this->session->userdata('username') ?: 'Unknown User';
            $data['current_level'] = $this->session->userdata('level') ?: 'Guru';
            $this->load->view('authorize_error', $data);
            return;
        }

        $pairing_id = trim($pairing_id);
        $pairing = $this->db->get_where('terminal_pairing', ['pairing_id' => $pairing_id])->row();

        if (!$pairing) {
            show_error('Sesi pairing kadaluarsa atau tidak ditemukan.', 404);
            exit;
        }

        if ($pairing->status === 'paired') {
            $data['device_name'] = $this->db->get_where('terminal_device', ['device_token' => $pairing->device_token])->row('device_name');
            // Fetch school name
            $device = $this->db->get_where('terminal_device', ['device_token' => $pairing->device_token])->row();
            $data['lembaga_nama'] = 'Semua Lembaga';
            if ($device && $device->id_lembaga) {
                $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $device->id_lembaga])->row();
                if ($lembaga) $data['lembaga_nama'] = $lembaga->nama;
            }
            $this->load->view('authorize_success', $data);
            return;
        }

        // Fetch institutions list to let Super Admin assign this terminal to an institution
        $data['lembagas'] = $this->db->order_by('nama', 'ASC')->get('lembaga')->result();
        $data['pairing_id'] = $pairing_id;

        if ($this->input->post('submit')) {
            $device_name = trim($this->input->post('device_name', TRUE) ?: 'Terminal Absensi');
            $id_lembaga  = trim($this->input->post('id_lembaga', TRUE) ?: null);

            if (empty($id_lembaga)) {
                $id_lembaga = null;
            }

            $device_token = bin2hex(random_bytes(32));

            // 1. Insert into terminal_device
            $this->db->insert('terminal_device', [
                'device_token' => $device_token,
                'device_name'  => $device_name,
                'id_lembaga'   => $id_lembaga,
                'created_at'   => date('Y-m-d H:i:s'),
                'is_active'    => 1
            ]);

            // 2. Update terminal_pairing status to paired
            $this->db->update('terminal_pairing', [
                'status' => 'paired',
                'device_token' => $device_token
            ], ['pairing_id' => $pairing_id]);

            $data['device_name'] = $device_name;
            $data['lembaga_nama'] = 'Semua Lembaga';
            if ($id_lembaga) {
                $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $id_lembaga])->row();
                if ($lembaga) $data['lembaga_nama'] = $lembaga->nama;
            }

            $this->load->view('authorize_success', $data);
            return;
        }

        $this->load->view('authorize_confirm', $data);
    }

    public function sendScanPembiasaanSiswa()
    {
        header('Content-Type: application/json');

        // Check authentication
        if (!$this->session->userdata('level')) {
            echo json_encode(['valid' => false, 'message' => 'Sesi Anda telah berakhir. Harap login kembali.']);
            exit;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $token = trim($data['token'] ?? '');
        $mode  = trim($data['mode'] ?? 'masuk'); // 'masuk' atau 'pulang'
        $ket   = trim($data['ket'] ?? 'hadir'); // default 'hadir'

        if (empty($token)) {
            echo json_encode(['valid' => false, 'message' => 'Data QR Code kosong.']);
            exit;
        }

        // Dual-lookup: Search student by id_siswa (UUID) or nis (Nomor Induk Santri)
        $siswa = $this->db->query("SELECT * FROM siswa WHERE id_siswa = ? OR nis = ? LIMIT 1", [$token, $token])->row();

        if (!$siswa) {
            echo json_encode(['valid' => false, 'message' => 'Siswa/Santri tidak terdaftar (ID/NIS: ' . htmlspecialchars($token) . ')']);
            exit;
        }

        // Check if student is registered in an institution
        $regSiswa = $this->db->get_where('registrasi_siswa', ['id_siswa' => $siswa->id_siswa])->row();
        if (!$regSiswa) {
            echo json_encode(['valid' => false, 'message' => 'Siswa ' . $siswa->nama . ' tidak terdaftar di lembaga mana pun.']);
            exit;
        }

        $id_lembaga_siswa = $regSiswa->id_lembaga;

        // Enforce satminkal restriction for non-superadmin users: They can only scan their own satminkal (base institution) students
        $satminkal = null;
        $dtlUser = $this->db->query("SELECT * FROM user WHERE id_user = ?", [$this->iduser])->row();
        if ($dtlUser && $dtlUser->level === 'guru') {
            $reg = $this->db->query("SELECT id_lembaga FROM registrasi WHERE id_guru = ? AND (satminkal = 1 OR satminkal = '1') LIMIT 1", [$dtlUser->id_guru])->row();
            if ($reg) {
                $satminkal = $reg->id_lembaga;
            }
        }

        if (empty($satminkal)) {
            $satminkal = $this->id_lembaga;
        }

        if ($this->session->userdata('level') !== 'superadmin' && !empty($satminkal) && $id_lembaga_siswa !== $satminkal) {
            echo json_encode(['valid' => false, 'message' => 'Siswa ' . $siswa->nama . ' bukan dari lembaga satminkal Anda.']);
            exit;
        }

        // Get active semester
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        if (empty($id_semester_aktif)) {
            $active_sem = $this->db->get_where('semester', ['is_active' => 1])->row();
            $id_semester_aktif = $active_sem ? $active_sem->id_semester : null;
        }

        // Check today's attendance record
        $cekExist = $this->db->get_where('pembiasaan_siswa', [
            'id_siswa' => $siswa->id_siswa,
            'tanggal'  => date('Y-m-d')
        ])->row();

        $currentTime = date('H:i:s');
        $success = false;
        $msg = '';

        if ($mode === 'masuk') {
            if ($cekExist && !empty($cekExist->jam_masuk) && $cekExist->jam_masuk !== '00:00:00') {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Siswa ' . $siswa->nama . ' sudah melakukan Absensi Masuk hari ini pukul ' . date('H:i', strtotime($cekExist->jam_masuk))
                ]);
                exit;
            }

            if ($cekExist) {
                $updateData = [
                    'jam_masuk'  => $currentTime,
                    'ket'        => $ket,
                    'id_lembaga' => $id_lembaga_siswa
                ];
                if (!empty($id_semester_aktif)) {
                    $updateData['id_semester'] = $id_semester_aktif;
                }
                $success = $this->db->update('pembiasaan_siswa', $updateData, ['id_pembiasaan_siswa' => $cekExist->id_pembiasaan_siswa]);
            } else {
                $insertData = [
                    'id_siswa'   => $siswa->id_siswa,
                    'tanggal'    => date('Y-m-d'),
                    'jam_masuk'  => $currentTime,
                    'ket'        => $ket,
                    'id_lembaga' => $id_lembaga_siswa
                ];
                if (!empty($id_semester_aktif)) {
                    $insertData['id_semester'] = $id_semester_aktif;
                }
                $success = $this->db->insert('pembiasaan_siswa', $insertData);
            }
            $msg = 'Absen MASUK Berhasil: ' . $siswa->nama;

        } else { // mode === 'pulang'
            if ($cekExist && !empty($cekExist->jam_pulang) && $cekExist->jam_pulang !== '00:00:00') {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Siswa ' . $siswa->nama . ' sudah melakukan Absensi Pulang hari ini pukul ' . date('H:i', strtotime($cekExist->jam_pulang))
                ]);
                exit;
            }

            // Check if checking out too quickly (cooldown of 2 minutes to prevent instant duplicate scans)
            if ($cekExist && !empty($cekExist->jam_masuk) && $cekExist->jam_masuk !== '00:00:00') {
                $masukTime = strtotime($cekExist->jam_masuk);
                if (time() - $masukTime < 120) { // 2 minutes
                    echo json_encode([
                        'valid' => false,
                        'message' => 'Absensi Ditolak: Baru saja melakukan Absensi Masuk. Silakan coba kembali nanti.'
                    ]);
                    exit;
                }
            }

            if ($cekExist) {
                $updateData = [
                    'jam_pulang' => $currentTime,
                    'id_lembaga' => $id_lembaga_siswa
                ];
                if (!empty($id_semester_aktif)) {
                    $updateData['id_semester'] = $id_semester_aktif;
                }
                $success = $this->db->update('pembiasaan_siswa', $updateData, ['id_pembiasaan_siswa' => $cekExist->id_pembiasaan_siswa]);
            } else {
                $insertData = [
                    'id_siswa'   => $siswa->id_siswa,
                    'tanggal'    => date('Y-m-d'),
                    'jam_pulang' => $currentTime,
                    'ket'        => $ket,
                    'id_lembaga' => $id_lembaga_siswa
                ];
                if (!empty($id_semester_aktif)) {
                    $insertData['id_semester'] = $id_semester_aktif;
                }
                $success = $this->db->insert('pembiasaan_siswa', $insertData);
            }
            $msg = 'Absen PULANG Berhasil: ' . $siswa->nama;
        }

        if ($success) {
            echo json_encode([
                'valid'   => true,
                'message' => $msg,
                'siswa'   => $siswa->nama,
                'nis'     => $siswa->nis ?: '-',
                'waktu'   => date('H:i:s'),
                'type'    => strtoupper($mode)
            ]);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Gagal menyimpan absensi ke server.']);
        }
        exit;
    }

    public function pembiasaan_siswa_scan()
    {
        if (!$this->session->userdata('level')) {
            redirect('auth');
        }

        $data['title'] = "Absen Pembiasaan Siswa";
        $data['menu'] = "pembiasaan_siswa_scan";
        $data['sub'] = "pembiasaan_siswa_scan";

        $this->load->view('guru/pembiasaan_siswa_scan', $data);
    }

    public function pembiasaan_siswa_hasil()
    {
        if (!$this->session->userdata('level')) {
            redirect('auth');
        }

        $data['title'] = "Hasil Absen Pembiasaan Siswa";
        $data['menu'] = "pembiasaan_siswa_hasil";
        $data['sub'] = "pembiasaan_siswa_hasil";

        $this->load->view('guru/pembiasaan_siswa_hasil', $data);
    }

    public function ajaxHasilPembiasaanSiswa()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('level')) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $dari   = $this->input->post('dari', true) ?: date('Y-m-d', strtotime('-30 days'));
        $sampai = $this->input->post('sampai', true) ?: date('Y-m-d');
        $id_lembaga = $this->id_lembaga; // Force filter by logged-in teacher's institution

        $this->db->select('tanggal, COUNT(id_siswa) as total_siswa');
        $this->db->from('pembiasaan_siswa');
        $this->db->where('tanggal >=', $dari);
        $this->db->where('tanggal <=', $sampai);
        if (!empty($id_lembaga)) {
            $this->db->where('id_lembaga', $id_lembaga);
        }
        $this->db->group_by('tanggal');
        $this->db->order_by('tanggal', 'DESC');
        $data = $this->db->get()->result_array();

        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
        exit;
    }

    public function pembiasaan_siswa_hasil_detail($date)
    {
        if (!$this->session->userdata('level')) {
            redirect('auth');
        }

        $data['title'] = "Detail Hasil Absen Pembiasaan";
        $data['menu'] = "pembiasaan_siswa_hasil";
        $data['sub'] = "pembiasaan_siswa_hasil";
        $data['date'] = $date;

        $id_lembaga = $this->id_lembaga;
        $data['id_lembaga'] = $id_lembaga;

        // Fetch institution detail
        $data['lembaga_selected'] = null;
        if ($id_lembaga) {
            $data['lembaga_selected'] = $this->db->get_where('lembaga', ['id_lembaga' => $id_lembaga])->row();
        }

        // Fetch student list for this date & institution
        $this->db->select('ps.*, s.nama, s.nis, s.jkl');
        $this->db->from('pembiasaan_siswa ps');
        $this->db->join('siswa s', 'ps.id_siswa COLLATE utf8mb4_general_ci = s.id_siswa COLLATE utf8mb4_general_ci', 'inner', FALSE);
        $this->db->where('ps.tanggal', $date);
        if ($id_lembaga) {
            $this->db->where('ps.id_lembaga', $id_lembaga);
        }
        $this->db->order_by('s.nama', 'ASC');
        $data['list'] = $this->db->get()->result();

        // Calculate and pass class breakdown details
        $id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
        if (!$id_tahun_aktif) {
            $active_sem = $this->db->get_where('semester', ['is_active' => 1])->row();
            $id_tahun_aktif = $active_sem ? $active_sem->id_tahun : null;
        }

        $this->db->order_by('id_kelas', 'ASC');
        $this->db->where('id_lembaga', $id_lembaga);
        if ($id_tahun_aktif) {
            $this->db->where('id_tahun', $id_tahun_aktif);
        }
        $classes = $this->db->get('kelas')->result();
        $data_kelas = [];
        foreach ($classes as $cls) {
            // Get registered students in this class for the active year
            $this->db->select('id_siswa');
            $this->db->from('rombel');
            $this->db->where('id_kelas', $cls->id_kelas);
            if ($id_tahun_aktif) {
                $this->db->where('id_tahun', $id_tahun_aktif);
            }
            $students_res = $this->db->get()->result_array();
            $student_ids = array_column($students_res, 'id_siswa');
            $total_wajib = count($student_ids);

            $hadir = 0;
            if ($total_wajib > 0) {
                // Query attendance records for these students (only Hadir status)
                $this->db->select('id_siswa');
                $this->db->from('pembiasaan_siswa');
                $this->db->where('tanggal', $date);
                $this->db->where('ket', 'hadir');
                $this->db->where_in('id_siswa', $student_ids);
                $hadir = $this->db->count_all_results();
            }

            $belum_hadir = $total_wajib - $hadir;

            $data_kelas[] = [
                'nama_kelas' => $cls->nama,
                'wajib' => $total_wajib,
                'hadir' => $hadir,
                'belum_hadir' => $belum_hadir >= 0 ? $belum_hadir : 0
            ];
        }
        $data['data_kelas'] = $data_kelas;

        $this->load->view('guru/pembiasaan_siswa_hasil_detail', $data);
    }

    public function ajaxGetPembiasaanSiswaDetail($date)
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('level')) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id_lembaga = $this->id_lembaga;
        $id_tahun_aktif = $this->session->userdata('id_tahun_aktif');

        // Fetch student list for this date & institution
        $this->db->select('ps.*, s.nama, s.nis, s.jkl');
        $this->db->from('pembiasaan_siswa ps');
        $this->db->join('siswa s', 'ps.id_siswa COLLATE utf8mb4_general_ci = s.id_siswa COLLATE utf8mb4_general_ci', 'inner', FALSE);
        $this->db->where('ps.tanggal', $date);
        if ($id_lembaga) {
            $this->db->where('ps.id_lembaga', $id_lembaga);
        }
        $this->db->order_by('s.nama', 'ASC');
        $list = $this->db->get()->result_array();

        // Format times for JSON output
        foreach ($list as &$row) {
            $row['jam_masuk'] = (!empty($row['jam_masuk']) && $row['jam_masuk'] !== '00:00:00') ? date('H:i', strtotime($row['jam_masuk'])) : '-';
            $row['jam_pulang'] = (!empty($row['jam_pulang']) && $row['jam_pulang'] !== '00:00:00') ? date('H:i', strtotime($row['jam_pulang'])) : '-';
        }

        // Calculate and pass class breakdown details
        if (!$id_tahun_aktif) {
            $active_sem = $this->db->get_where('semester', ['is_active' => 1])->row();
            $id_tahun_aktif = $active_sem ? $active_sem->id_tahun : null;
        }

        $this->db->order_by('id_kelas', 'ASC');
        $this->db->where('id_lembaga', $id_lembaga);
        if ($id_tahun_aktif) {
            $this->db->where('id_tahun', $id_tahun_aktif);
        }
        $classes = $this->db->get('kelas')->result();
        $data_kelas = [];
        foreach ($classes as $cls) {
            // Get registered students in this class for the active year
            $this->db->select('id_siswa');
            $this->db->from('rombel');
            $this->db->where('id_kelas', $cls->id_kelas);
            if ($id_tahun_aktif) {
                $this->db->where('id_tahun', $id_tahun_aktif);
            }
            $students_res = $this->db->get()->result_array();
            $student_ids = array_column($students_res, 'id_siswa');
            $total_wajib = count($student_ids);

            $hadir = 0;
            if ($total_wajib > 0) {
                // Query attendance records for these students (only Hadir status)
                $this->db->select('id_siswa');
                $this->db->from('pembiasaan_siswa');
                $this->db->where('tanggal', $date);
                $this->db->where('ket', 'hadir');
                $this->db->where_in('id_siswa', $student_ids);
                $hadir = $this->db->count_all_results();
            }

            $belum_hadir = $total_wajib - $hadir;

            $data_kelas[] = [
                'nama_kelas' => $cls->nama,
                'wajib' => $total_wajib,
                'hadir' => $hadir,
                'belum_hadir' => $belum_hadir >= 0 ? $belum_hadir : 0
            ];
        }

        echo json_encode([
            'status' => true,
            'list' => $list,
            'data_kelas' => $data_kelas
        ]);
        exit;
    }

    public function download_pembiasaan_siswa_screen($tgl, $id_lembaga = null)
    {
        if ($this->session->userdata('level') !== 'superadmin' && $this->id_lembaga) {
            $id_lembaga = $this->id_lembaga;
        }

        if (!$id_lembaga) {
            show_error('Parameter lembaga tidak ditemukan.');
        }

        $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $id_lembaga])->row();
        $nick = $lembaga ? $lembaga->nickname : 'SISWA';

        $curl = curl_init();
        $targetUrl = base_url() . 'screen/pembiasaan_siswa/' . $tgl . '/' . $id_lembaga;
        $captureUrl = 'https://capture.ppdwk.site/capture?url=' . $targetUrl . '&filename=PEMBIASAAN-SISWA-' . $nick . '_' . $tgl;

        curl_setopt_array($curl, [
            CURLOPT_URL => $captureUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if ($result && isset($result['status']) && $result['status'] === true) {
            $fileUrl = "https://capture.ppdwk.site/capture-result/PEMBIASAAN-SISWA-$nick"  . "_$tgl.png";
            $fileName = "PEMBIASAAN-SISWA-$nick"  . "_$tgl.png";

            $ch = curl_init($fileUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 90,
            ]);

            $fileData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($fileData !== false && $httpCode === 200) {
                header('Content-Description: File Transfer');
                header('Content-Type: image/png');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Length: ' . strlen($fileData));
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                echo $fileData;
                exit;
            }
        }

        show_error('Gagal mengambil tangkapan layar rekap absensi. Silakan coba lagi.');
    }
}
