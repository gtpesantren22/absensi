<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Screen extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function apel_guru($tgl, $idl)
    {
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();

        $hari_ini = $tgl;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db->query("SELECT * FROM apel_guru WHERE tanggal = '$hari_ini' AND id_lembaga = '$idl' ")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        $dataJadwal = $this->db->query("SELECT * FROM apel_guru WHERE tanggal = '$tglni' AND id_lembaga = '$idl' ");
        $data['hadir'] = $this->db->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'hadir' AND id_lembaga = '$idl' ")->row();
        $data['izin'] = $this->db->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'izin' AND id_lembaga = '$idl' ")->row();
        $data['alpha'] = $this->db->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'alpha' AND id_lembaga = '$idl' ")->row();

        $data['data'] = $dataJadwal->result();
        $data['tanggal'] = $tglni;


        $this->load->view('absensi/screen_apel_guru', $data);
    }

    public function mengajar_guru($tglCari, $idl)
    {
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();

        $hari_ini = $tglCari;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db->query("SELECT * FROM mengajar WHERE tanggal = '$hari_ini' AND id_lembaga = '$idl' ")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        // $harini = 'Monday';
        $dataJadwal = $this->db->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' AND id_lembaga = '$idl' ");
        $dataKirim = [];
        $totalkehadiran = 0;
        $totaljamwajib = 0;
        $totaljammasuk = 0;
        foreach ($dataJadwal->result() as $key) {
            $hadir = $this->db->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND id_lembaga = '$idl' ")->row();
            $guru = $this->db->query("SELECT nama FROM guru WHERE id_guru = '$key->id_guru' ")->row();
            // $jam = $this->db->query("SELECT SUM((jam_sampai-jam_dari)+1) as jmlJam FROM jadwal WHERE hari = '$harini' AND guru = '$key->id_guru' ")->row();
            $jam = $this->db->query("SELECT COUNT(*) as jmlJam FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND id_lembaga = '$idl' ")->row();
            $masuk = $this->db->query("SELECT COUNT(*) as jmlJam FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND ket = 'H' AND id_lembaga = '$idl' ")->row();
            $alasan = $this->db->query("SELECT * FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND alasan != '-' AND id_lembaga = '$idl' ")->row();
            $jamwajib = $jam->jmlJam != 0 ? $jam->jmlJam : 0;
            $dataKirim[] = [
                'guru' => $key->id_guru,
                'hadir' => $hadir ? $hadir->ket : '',
                'waktu' => $hadir ? $hadir->waktu : '00:00',
                'nama_guru' => $guru->nama,
                'jam' => $jamwajib,
                'masuk' => $masuk->jmlJam,
                'persen' => $jamwajib == 0 ? 0 : ($masuk->jmlJam / $jamwajib) * 100,
                'alasan' => $alasan ? $alasan->alasan : '-',
            ];
            $totalkehadiran += $hadir && $hadir->ket == 'hadir' ? 1 : 0;
            $totaljamwajib += $jamwajib;
            $totaljammasuk += $masuk->jmlJam;
        }
        $data['data'] = $dataKirim;
        $data['hari'] = translateDay($harini, 'id');
        $data['tanggal'] = $tglni;
        $data['totalguru'] = $dataJadwal->num_rows();
        $data['totalkehadiran'] = $totalkehadiran;
        $data['totaljamwajib'] = $totaljamwajib;
        $data['totaljammasuk'] = $totaljammasuk;

        // echo '<pre>';
        // var_dump($dataKirim);
        // echo '</pre>';

        $this->load->view('absensi/screen_mengajar_guru', $data);
    }

    public function kehadiran_guru($tgl, $idl)
    {
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();

        $hari_ini = $tgl;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$hari_ini' AND id_lembaga = '$idl' ")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        $dataJadwal = $this->db->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' AND id_lembaga = '$idl' ");
        $data['hadir'] = $this->db->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'hadir' AND id_lembaga = '$idl' ")->row();
        $data['izin'] = $this->db->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'izin' AND id_lembaga = '$idl' ")->row();
        $data['alpha'] = $this->db->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'alpha' AND id_lembaga = '$idl' ")->row();

        $data['data'] = $dataJadwal->result();
        $data['tanggal'] = $tglni;


        $this->load->view('absensi/screen_hadir_guru', $data);
    }

    public function pembiasaan_siswa($tgl, $idl)
    {
        $data['lembaga'] = $this->db->get_where('lembaga', ['id_lembaga' => $idl])->row();
        $data['tanggal'] = $tgl;

        // Fetch active academic year
        $id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
        if (!$id_tahun_aktif) {
            // Fallback to latest active year
            $latest_year = $this->db->order_by('id_tahun', 'DESC')->get('tahun_ajaran')->row();
            $id_tahun_aktif = $latest_year ? $latest_year->id_tahun : null;
        }

        // 1. Fetch all classes for this institution and active academic year
        $this->db->order_by('id_kelas', 'ASC');
        $this->db->where('id_lembaga', $idl);
        if ($id_tahun_aktif) {
            $this->db->where('id_tahun', $id_tahun_aktif);
        }
        $classes = $this->db->get('kelas')->result();

        $data_kelas = [];
        $grand_total_wajib = 0;
        $grand_total_hadir = 0;
        $grand_total_sakit = 0;
        $grand_total_izin = 0;
        $grand_total_alpha = 0;

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
            $sakit = 0;
            $izin = 0;
            $alpha = 0;

            if ($total_wajib > 0) {
                // Query attendance records for these students
                $this->db->select('id_siswa, ket');
                $this->db->from('pembiasaan_siswa');
                $this->db->where('tanggal', $tgl);
                $this->db->where_in('id_siswa', $student_ids);
                $attendance_list = $this->db->get()->result();

                foreach ($attendance_list as $att) {
                    $status = strtolower($att->ket);
                    if ($status === 'hadir') {
                        $hadir++;
                    } elseif ($status === 'sakit') {
                        $sakit++;
                    } elseif ($status === 'izin') {
                        $izin++;
                    } elseif ($status === 'alpha') {
                        $alpha++;
                    }
                }
            }

            $belum_hadir = $total_wajib - $hadir;

            $data_kelas[] = [
                'id_kelas' => $cls->id_kelas,
                'nama_kelas' => $cls->nama,
                'wajib' => $total_wajib,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'belum_hadir' => $belum_hadir >= 0 ? $belum_hadir : 0
            ];

            $grand_total_wajib += $total_wajib;
            $grand_total_hadir += $hadir;
            $grand_total_sakit += $sakit;
            $grand_total_izin += $izin;
            $grand_total_alpha += $alpha;
        }

        $data['data_kelas'] = $data_kelas;
        $data['total_wajib'] = $grand_total_wajib;
        $data['total_hadir'] = $grand_total_hadir;
        $data['total_sakit'] = $grand_total_sakit;
        $data['total_izin'] = $grand_total_izin;
        $data['total_alpha'] = $grand_total_alpha;
        $data['total_belum_hadir'] = $grand_total_wajib - $grand_total_hadir;

        $this->load->view('absensi/screen_pembiasaan_siswa', $data);
    }
}
