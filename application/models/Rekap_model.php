<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekap_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }
    public function getRekapApelGuru($dari, $sampai)
    {
        // Jika guru hadir pada hari yang namanya tidak ada di wajib apel, maka hasil absensi nya akan diabaikan
        $guruList = $this->db->query("SELECT guru.* FROM guru JOIN registrasi ON guru.id_guru=registrasi.id_guru WHERE id_lembaga = '$this->id_lembaga' ORDER BY guru.nama ASC")->result();
        $hasil = [];

        $start = new DateTime($dari);
        $end   = new DateTime($sampai);
        $end->modify('+1 day');

        foreach ($guruList as $guru) {

            $wajib = 0;
            $hadir = 0;
            $izin  = 0;
            $alpha = 0;

            $period = new DatePeriod($start, new DateInterval('P1D'), $end);

            foreach ($period as $tgl) {

                $tanggal = $tgl->format('Y-m-d');
                $hari    = $tgl->format('l'); // Monday, Tuesday

                // 1️⃣ CEK HARI LIBUR
                $libur = $this->db->get_where('hari_libur', [
                    'tanggal' => $tanggal,
                    'id_lembaga' => $this->id_lembaga
                ])->row();

                if ($libur) {
                    continue;
                }

                // if ($hari == 'Friday') {
                //     continue;
                // }

                $id_semester_aktif = $this->session->userdata('id_semester_aktif');

                // 2️⃣ CEK APEL SETT
                $apelWajib = $this->db->get_where('apel_sett', [
                    'id_guru' => $guru->id_guru,
                    'hari'    => $hari,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ])->row();

                if (!$apelWajib) {
                    continue;
                }

                // ✅ WAJIB
                $wajib++;

                // 3️⃣ CEK ABSENSI
                $absen = $this->db->get_where('apel_guru', [
                    'id_guru' => $guru->id_guru,
                    'tanggal' => $tanggal,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ])->row();

                if ($absen) {
                    if ($absen->ket === 'hadir') $hadir++;
                    elseif ($absen->ket === 'izin') $izin++;
                    elseif ($absen->ket === 'alpha') $alpha++;
                }
            }

            $hasil[] = (object)[
                'nama_guru' => $guru->nama,
                'wajib' => $wajib,
                'hadir' => $hadir,
                'izin'  => $izin,
                'alpha' => $alpha,
                'jml_tidak_hadir' => $izin + $alpha
            ];
        }

        return $hasil;
    }

    public function getRekapHadirGuru($dari, $sampai)
    {
        $guruList = $this->db->query("SELECT guru.* FROM guru JOIN registrasi ON guru.id_guru=registrasi.id_guru WHERE id_lembaga = '$this->id_lembaga' ORDER BY guru.nama ASC")->result();
        $hasil = [];

        $start = new DateTime($dari);
        $end   = new DateTime($sampai);
        $end->modify('+1 day');

        foreach ($guruList as $guru) {

            $wajib = 0;
            $hadir = 0;
            $izin  = 0;
            $alpha = 0;
            $cuti = 0;

            $period = new DatePeriod($start, new DateInterval('P1D'), $end);

            foreach ($period as $tgl) {

                $tanggal = $tgl->format('Y-m-d');
                $hari    = $tgl->format('l'); // Monday, Tuesday

                // 1️⃣ CEK HARI LIBUR
                $libur = $this->db->get_where('hari_libur', [
                    'tanggal' => $tanggal,
                    'id_lembaga' => $this->id_lembaga
                ])->row();

                if ($libur) {
                    continue;
                }
                // if ($hari == 'Friday') {
                //     continue;
                // }

                $id_semester_aktif = $this->session->userdata('id_semester_aktif');

                // 3️⃣ CEK ABSENSI
                $absen = $this->db->get_where('kehadiran_guru', [
                    'id_guru' => $guru->id_guru,
                    'tanggal' => $tanggal,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ])->row();

                if ($absen) {
                    if ($absen->ket === 'hadir') $hadir++;
                    elseif ($absen->ket === 'izin') $izin++;
                    elseif ($absen->ket === 'alpha') $alpha++;
                    elseif ($absen->ket === 'cuti') $cuti++;
                }

                // 2️⃣ CEK APEL SETT
                $apelWajib = $this->db->get_where('apel_sett', [
                    'id_guru' => $guru->id_guru,
                    'hari'    => $hari,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ])->row();

                if (!$apelWajib) {
                    continue;
                }

                // ✅ WAJIB
                $wajib++;
            }

            $hasil[] = (object)[
                'nama_guru' => $guru->nama,
                'wajib' => $wajib > $hadir ? $wajib : $hadir,
                'hadir' => $hadir,
                'izin'  => $izin,
                'alpha' => $alpha,
                'cuti' => $cuti,
                'jml_tidak_hadir' => $izin + $alpha + $cuti
            ];
        }

        return $hasil;
    }
    public function getRekapMengajarGuru($dari, $sampai)
    {
        $guruList = $this->db->query("SELECT guru.* FROM guru JOIN registrasi ON guru.id_guru=registrasi.id_guru WHERE id_lembaga = '$this->id_lembaga' ORDER BY guru.nama ASC")->result();
        $hasil = [];

        $start = new DateTime($dari);
        $end   = new DateTime($sampai);
        $end->modify('+1 day');

        foreach ($guruList as $guru) {

            $wajib = 0;
            $hadir = 0;
            $izin  = 0;
            $sakit = 0;
            $telat = 0;
            $alpha = 0;
            $cuti = 0;

            $period = new DatePeriod($start, new DateInterval('P1D'), $end);

            foreach ($period as $tgl) {

                $tanggal = $tgl->format('Y-m-d');
                $hari    = $tgl->format('l'); // Monday, Tuesday

                // 1️⃣ CEK HARI LIBUR
                $libur = $this->db->get_where('hari_libur', [
                    'tanggal' => $tanggal,
                    'id_lembaga' => $this->id_lembaga
                ])->row();

                if ($libur) {
                    continue;
                }
                // if ($hari == 'Friday') {
                //     continue;
                // }

                $id_semester_aktif = $this->session->userdata('id_semester_aktif');

                // 3️⃣ CEK ABSENSI
                $absen = $this->db
                    ->select('ket, COUNT(*) AS jml')
                    ->from('mengajar')
                    ->where('id_guru', $guru->id_guru)
                    ->where('tanggal', $tanggal)
                    ->where('id_semester', $id_semester_aktif)
                    ->group_by('ket')
                    ->get()
                    ->result();

                $map = [
                    'H' => 0,
                    'I' => 0,
                    'A' => 0,
                    'C' => 0,
                    'S' => 0,
                    'T' => 0,
                ];

                foreach ($absen as $row) {
                    if (isset($map[$row->ket])) {
                        $map[$row->ket] += (int) $row->jml;
                    }
                }

                $hadir  += $map['H'];
                $izin   += $map['I'];
                $alpha  += $map['A'];
                $cuti   += $map['C'];
                $sakit  += $map['S'];
                $telat  += $map['T'];


                // 2️⃣ CEK APEL SETT (Jadwal Wajib Mengajar)
                $apelWajib = $this->db
                    ->select('SUM((jam_sampai - jam_dari)+1) AS jml')
                    ->where('id_guru', $guru->id_guru)
                    ->where('hari', $hari)
                    ->where('id_lembaga', $this->id_lembaga)
                    ->where('id_semester', $id_semester_aktif)
                    ->from('jadwal')->get()
                    ->row();

                if (!$apelWajib || $apelWajib->jml <= 0) {
                    continue;
                }

                // ✅ WAJIB (akumulasi jam)
                $wajib += (int) $apelWajib->jml;
            }

            $hasil[] = (object)[
                'nama_guru' => $guru->nama,
                'wajib' => $wajib,
                'hadir' => $hadir + $telat,
                'izin'  => $izin,
                'alpha' => $alpha,
                'cuti' => $cuti,
                'sakit' => $sakit,
                'telat' => $telat ?? 0,
                'jml_tidak_hadir' => $izin + $alpha + $cuti + $sakit
            ];
        }

        return $hasil;
    }
}
