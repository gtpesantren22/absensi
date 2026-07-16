<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keaktifanguru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->mustLogin();

        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }

    public function index()
    {
        $data['title'] = "Keaktifan Guru";
        $data['sub'] = "keaktifanguru";

        if ($this->session->userdata('level') === 'admin' || $this->session->userdata('level') === 'super_admin') {
            $data['menu'] = "absensiguru";
            $this->load->view('admin/keaktifan_guru', $data);
        } else {
            $data['menu'] = "keaktifanguru";
            $this->load->view('guru/keaktifan_guru', $data);
        }
    }

    public function getKeaktifanData()
    {
        $date = $this->input->get('date', true);
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $hari = date('l', strtotime($date));
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        // 1. Get registered teachers
        $level = $this->session->userdata('level');
        $iduser = $this->session->userdata('id_user');

        $this->db->select('g.id_guru, g.nama');
        $this->db->from('registrasi r');
        $this->db->join('guru g', 'r.id_guru = g.id_guru');

        if ($level === 'guru') {
            $cekGuru = $this->db->get_where('user', ['id_user' => $iduser])->row();
            if ($cekGuru) {
                $this->db->where('g.id_guru', $cekGuru->id_guru);
            } else {
                $this->db->where('g.id_guru', 'NONE');
            }
        }
        $this->db->where('r.id_lembaga', $this->id_lembaga);
        $this->db->where('r.satminkal', 1);
        $this->db->order_by('g.nama', 'ASC');
        $teachers = $this->db->get()->result_array();

        $result_data = [];

        foreach ($teachers as $teacher) {
            // 2. Get attendance status
            $attendance = $this->db->get_where('kehadiran_guru', [
                'id_guru' => $teacher['id_guru'],
                'tanggal' => $date,
                'id_lembaga' => $this->id_lembaga,
                'id_semester' => $id_semester_aktif
            ])->row();
            
            $status_kehadiran = $attendance ? $attendance->ket : null;
            $waktu_absen = $attendance ? $attendance->waktu : null;

            // 3. Get schedule for the day of week
            $scheduled = $this->db->get_where('jadwal', [
                'id_guru' => $teacher['id_guru'],
                'hari' => $hari,
                'id_lembaga' => $this->id_lembaga,
                'id_semester' => $id_semester_aktif
            ])->result_array();

            $detail_classes = [];
            $total_scheduled_hours = 0;
            $total_filled_hours = 0;

            foreach ($scheduled as $sch) {
                // Class name
                $class_name = '';
                $kls_row = $this->db->get_where('kelas', ['id_kelas' => $sch['id_kelas']])->row();
                if ($kls_row) {
                    $class_name = $kls_row->nama;
                }

                // Mapel name
                $mapel_name = '';
                $mpl_row = $this->db->get_where('mapel', ['id_mapel' => $sch['id_mapel']])->row();
                if ($mpl_row) {
                    $mapel_name = $mpl_row->nama;
                }

                $hours = [];
                $filled_count = 0;
                for ($i = (int)$sch['jam_dari']; $i <= (int)$sch['jam_sampai']; $i++) {
                    $total_scheduled_hours++;
                    $check_mengajar = $this->db->get_where('mengajar', [
                        'id_guru' => $teacher['id_guru'],
                        'tanggal' => $date,
                        'jam' => $i,
                        'id_lembaga' => $this->id_lembaga,
                        'id_semester' => $id_semester_aktif
                    ])->row();
                    if ($check_mengajar) {
                        $total_filled_hours++;
                        $filled_count++;
                    }
                    $hours[] = $i;
                }

                // Get journal content & student attendance summary
                $harian_entry = $this->db->get_where('harian', [
                    'id_guru' => $teacher['id_guru'],
                    'id_kelas' => $sch['id_kelas'],
                    'id_mapel' => $sch['id_mapel'],
                    'tanggal' => $date,
                    'dari' => $sch['jam_dari'],
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ])->row();

                $isi_jurnal = '';
                $siswa_stats = null;

                if ($harian_entry) {
                    $jurnal_row = $this->db->get_where('jurnal_guru', [
                        'kode_absen' => $harian_entry->kode,
                        'id_lembaga' => $this->id_lembaga,
                        'id_semester' => $id_semester_aktif
                    ])->row();
                    $isi_jurnal = $jurnal_row ? $jurnal_row->isi : '-';

                    $q_stats = $this->db->query("
                        SELECT 
                            SUM(CASE WHEN ket = 'hadir' THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN ket = 'sakit' THEN 1 ELSE 0 END) as sakit,
                            SUM(CASE WHEN ket = 'izin' THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN ket = 'alpha' THEN 1 ELSE 0 END) as alpha,
                            SUM(CASE WHEN ket = 'telat' THEN 1 ELSE 0 END) as telat
                        FROM harian 
                        WHERE kode = '{$harian_entry->kode}' AND id_lembaga = '{$this->id_lembaga}'
                    ")->row();

                    if ($q_stats) {
                        $siswa_stats = [
                            'hadir' => (int)($q_stats->hadir ?? 0),
                            'sakit' => (int)($q_stats->sakit ?? 0),
                            'izin' => (int)($q_stats->izin ?? 0),
                            'alpha' => (int)($q_stats->alpha ?? 0),
                            'telat' => (int)($q_stats->telat ?? 0),
                        ];
                    }
                }

                // Fallback to mengajar.alasan if jurnal_guru not exists
                if (empty($isi_jurnal) || $isi_jurnal == '-') {
                    $mengajar_recs = $this->db->get_where('mengajar', [
                        'id_guru' => $teacher['id_guru'],
                        'tanggal' => $date,
                        'id_lembaga' => $this->id_lembaga,
                        'id_semester' => $id_semester_aktif
                    ])->result_array();
                    foreach ($mengajar_recs as $mr) {
                        if (!empty($mr['alasan']) && $mr['alasan'] !== '-') {
                            $isi_jurnal = $mr['alasan'];
                            break;
                        }
                    }
                }

                $detail_classes[] = [
                    'id_jadwal' => $sch['id_jadwal'],
                    'kelas' => $class_name,
                    'mapel' => $mapel_name,
                    'jam_ke' => implode(' - ', $hours),
                    'is_terisi' => ($filled_count > 0),
                    'isi_jurnal' => !empty($isi_jurnal) ? $isi_jurnal : '-',
                    'siswa_stats' => $siswa_stats
                ];
            }

            $result_data[] = [
                'id_guru' => $teacher['id_guru'],
                'nama' => $teacher['nama'],
                'status_kehadiran' => $status_kehadiran,
                'waktu_absen' => $waktu_absen,
                'total_jp_jadwal' => $total_scheduled_hours,
                'total_jp_terisi' => $total_filled_hours,
                'classes' => $detail_classes
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result_data));
    }

    public function getMyKeaktifanMonthly()
    {
        $this->mustLogin();

        $month = $this->input->get('month', true);

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $cekGuru = $this->db->get_where('user', ['id_user' => $this->iduser])->row();
        
        if (!$cekGuru) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'attendance' => [],
                    'scheduled_jp' => 0,
                    'filled_jp' => 0,
                    'journals' => []
                ]));
            return;
        }

        // Get all registered institutions for this teacher to support cross-institution schedules/journals
        $reg_lembagas = $this->db->select('id_lembaga')->get_where('registrasi', ['id_guru' => $cekGuru->id_guru])->result_array();
        $lembaga_ids = array_column($reg_lembagas, 'id_lembaga');
        if (empty($lembaga_ids)) {
            $lembaga_ids = [$this->id_lembaga];
        }

        // 1. Get all daily attendance records (sorted DESC, optionally filtered by month)
        $this->db->where('id_guru', $cekGuru->id_guru);
        $this->db->where('id_lembaga', $this->id_lembaga);
        $this->db->where('id_semester', $id_semester_aktif);
        if (!empty($month)) {
            $this->db->like('tanggal', $month, 'after');
        }
        $this->db->order_by('tanggal', 'DESC');
        $attendance = $this->db->get('kehadiran_guru')->result_array();

        // Find the earliest date in this semester to calculate scheduling stats up to today
        $start_date_row = $this->db->select('MIN(tanggal) AS start_date')
            ->from('kehadiran_guru')
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_semester', $id_semester_aktif)
            ->get()->row();
        $start_date = $start_date_row ? $start_date_row->start_date : null;
        if (empty($start_date)) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }

        // 2. Get total scheduled JP from start of semester to today across all registered institutions
        $total_scheduled_jp = 0;
        $start_time = strtotime($start_date);
        $end_time = time();
        
        $this->db->where('id_guru', $cekGuru->id_guru);
        $this->db->where_in('id_lembaga', $lembaga_ids);
        $this->db->where('id_semester', $id_semester_aktif);
        $schedules = $this->db->get('jadwal')->result_array();

        for ($t = $start_time; $t <= $end_time; $t += 86400) {
            $day_name = date('l', $t);
            foreach ($schedules as $sch) {
                if (strtolower($sch['hari']) === strtolower($day_name)) {
                    $total_scheduled_jp += ((int)$sch['jam_sampai'] - (int)$sch['jam_dari'] + 1);
                }
            }
        }

        // 3. Get total filled JP in this semester across all registered institutions
        $this->db->select('COUNT(*) AS total');
        $this->db->from('mengajar');
        $this->db->where('id_guru', $cekGuru->id_guru);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', date('Y-m-d'));
        $this->db->where_in('id_lembaga', $lembaga_ids);
        $this->db->where('id_semester', $id_semester_aktif);
        $filled_jp_row = $this->db->get()->row();
        $total_filled_jp = $filled_jp_row ? (int)$filled_jp_row->total : 0;

        // 4. Get list of filled journals across all registered institutions
        $this->db->select('h.kode, h.tanggal, h.dari, h.sampai, h.id_kelas, h.id_mapel, h.id_lembaga, k.nama AS nama_kelas, mp.nama AS nama_mapel');
        $this->db->from('harian h');
        $this->db->join('kelas k', 'h.id_kelas = k.id_kelas', 'left');
        $this->db->join('mapel mp', 'h.id_mapel = mp.id_mapel', 'left');
        $this->db->where('h.id_guru', $cekGuru->id_guru);
        $this->db->where_in('h.id_lembaga', $lembaga_ids);
        $this->db->where('h.id_semester', $id_semester_aktif);
        $this->db->group_by(array('h.kode', 'h.tanggal', 'h.dari', 'h.sampai', 'h.id_kelas', 'h.id_mapel', 'h.id_lembaga', 'k.nama', 'mp.nama'));
        $this->db->order_by('h.tanggal', 'DESC');
        $sessions = $this->db->get()->result_array();

        $journals = [];
        foreach ($sessions as $session) {
            $q_stats = $this->db->query("
                SELECT 
                    SUM(CASE WHEN ket = 'hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN ket = 'sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN ket = 'izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN ket = 'alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN ket = 'telat' THEN 1 ELSE 0 END) as telat
                FROM harian 
                WHERE kode = '{$session['kode']}' AND id_lembaga = '{$session['id_lembaga']}'
            ")->row();

            $siswa_stats = null;
            if ($q_stats) {
                $siswa_stats = [
                    'hadir' => (int)($q_stats->hadir ?? 0),
                    'sakit' => (int)($q_stats->sakit ?? 0),
                    'izin' => (int)($q_stats->izin ?? 0),
                    'alpha' => (int)($q_stats->alpha ?? 0),
                    'telat' => (int)($q_stats->telat ?? 0),
                ];
            }

            // Get journal content from database matching the specific institution of this session
            $jurnal_row = $this->db->get_where('jurnal_guru', [
                'kode_absen' => $session['kode'],
                'id_lembaga' => $session['id_lembaga'],
                'id_semester' => $id_semester_aktif
            ])->row();
            
            $isi = $jurnal_row ? $jurnal_row->isi : null;
            if (empty($isi) || $isi == '-') {
                $mengajar_recs = $this->db->get_where('mengajar', [
                    'id_guru' => $cekGuru->id_guru,
                    'tanggal' => $session['tanggal'],
                    'id_kelas' => $session['id_kelas'],
                    'id_mapel' => $session['id_mapel'],
                    'id_lembaga' => $session['id_lembaga'],
                    'id_semester' => $id_semester_aktif
                ])->result_array();

                foreach ($mengajar_recs as $mr) {
                    if (!empty($mr['alasan']) && $mr['alasan'] !== '-') {
                        $isi = $mr['alasan'];
                        break;
                    }
                }
            }

            $journals[] = [
                'kode' => $session['kode'],
                'tanggal' => $session['tanggal'],
                'kelas' => $session['nama_kelas'] ?? '-',
                'mapel' => $session['nama_mapel'] ?? '-',
                'jam_ke' => $session['dari'] . ' - ' . $session['sampai'],
                'isi_jurnal' => !empty($isi) ? $isi : '-',
                'siswa_stats' => $siswa_stats
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'attendance' => $attendance,
                'scheduled_jp' => $total_scheduled_jp,
                'filled_jp' => $total_filled_jp,
                'journals' => $journals
            ]));
    }

    public function getJournalDetailData()
    {
        $this->mustLogin();
        $kode = $this->input->get('kode', true);

        if (empty($kode)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
            return;
        }

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        // Fetch students attendance for this session code (globally unique UUID)
        $this->db->select('nama_siswa, ket');
        $this->db->from('harian');
        $this->db->where('kode', $kode);
        $this->db->where('id_semester', $id_semester_aktif);
        $this->db->order_by('nama_siswa', 'ASC');
        $students = $this->db->get()->result_array();

        // Fetch journal text
        $jurnal = $this->db->get_where('jurnal_guru', [
            'kode_absen' => $kode,
            'id_semester' => $id_semester_aktif
        ])->row();
        $isi_jurnal = $jurnal ? $jurnal->isi : '-';

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'students' => $students,
                'isi_jurnal' => $isi_jurnal
            ]));
    }
}
