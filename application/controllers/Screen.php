<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Screen extends MY_Controller
{
    protected $db_active;
    protected $db_active_group;

    public function __construct()
    {
        parent::__construct();
    }

    protected function dbActive($bd_name)
    {
        $group = $$bd_name ?? 'default';

        if (
            !$this->db_active ||
            $this->db_active_group !== $group
        ) {
            $this->db_active = $this->load->database($group, TRUE);
            $this->db_active_group = $group;
        }

        return $this->db_active;
    }
    public function apel_guru($tgl, $idl)
    {
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();
        $iddb = $data['lembaga']->id_db;
        $db = $this->db->query("SELECT db_name FROM list_db WHERE id = '$iddb' ")->row();
        $this->dbActive($db->db_name);

        $hari_ini = $tgl;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db_active->query("SELECT * FROM apel_guru WHERE tanggal = '$hari_ini'")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        $dataJadwal = $this->db_active->query("SELECT * FROM apel_guru WHERE tanggal = '$tglni' ");
        $data['hadir'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'hadir' ")->row();
        $data['izin'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'izin' ")->row();
        $data['alpha'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM apel_guru WHERE tanggal = '$tglni' AND ket = 'alpha' ")->row();

        $data['data'] = $dataJadwal->result();
        $data['tanggal'] = $tglni;


        $this->load->view('absensi/screen_apel_guru', $data);
    }

    public function mengajar_guru($tglCari, $idl)
    {
        $data['lembaga'] = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$idl' ")->row();
        $iddb = $data['lembaga']->id_db;
        $db = $this->db->query("SELECT db_name FROM list_db WHERE id = '$iddb' ")->row();
        $this->dbActive($db->db_name);

        $hari_ini = $tglCari;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db_active->query("SELECT * FROM mengajar WHERE tanggal = '$hari_ini'")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        // $harini = 'Monday';
        $dataJadwal = $this->db_active->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' ");
        $dataKirim = [];
        $totalkehadiran = 0;
        $totaljamwajib = 0;
        $totaljammasuk = 0;
        foreach ($dataJadwal->result() as $key) {
            $hadir = $this->db_active->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' ")->row();
            $guru = $this->db->query("SELECT nama FROM guru WHERE id_guru = '$key->id_guru' ")->row();
            // $jam = $this->db->query("SELECT SUM((jam_sampai-jam_dari)+1) as jmlJam FROM jadwal WHERE hari = '$harini' AND guru = '$key->id_guru' ")->row();
            $jam = $this->db_active->query("SELECT COUNT(*) as jmlJam FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' ")->row();
            $masuk = $this->db_active->query("SELECT COUNT(*) as jmlJam FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND ket = 'H' ")->row();
            $alasan = $this->db_active->query("SELECT * FROM mengajar WHERE tanggal = '$tglni' AND id_guru = '$key->id_guru' AND alasan != '-' ")->row();
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
        $iddb = $data['lembaga']->id_db;
        $db = $this->db->query("SELECT db_name FROM list_db WHERE id = '$iddb' ")->row();
        $this->dbActive($db->db_name);

        $hari_ini = $tgl;
        // $hari_ini = date('2025-01-07');
        $dataCari = $this->db_active->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$hari_ini'")->row();
        if ($dataCari) {
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        } else {
            $harini = date('l', strtotime($hari_ini));
            $tglni = $hari_ini;
        }

        $dataJadwal = $this->db_active->query("SELECT * FROM kehadiran_guru WHERE tanggal = '$tglni' ");
        $data['hadir'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'hadir' ")->row();
        $data['izin'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'izin' ")->row();
        $data['alpha'] = $this->db_active->query("SELECT COUNT(*) as ttl FROM kehadiran_guru WHERE tanggal = '$tglni' AND ket = 'alpha' ")->row();

        $data['data'] = $dataJadwal->result();
        $data['tanggal'] = $tglni;


        $this->load->view('absensi/screen_hadir_guru', $data);
    }
}
