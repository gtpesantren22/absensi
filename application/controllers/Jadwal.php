<?php
defined('BASEPATH') or exit('No direct script access allowed');
// require FCPATH . 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Jadwal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->iduser = $this->session->userdata('id_user');

        $this->mustLogin();
        $this->AdminOrSuper();
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }

    public function index()
    {
        $data['hideSidebar'] = true;
        $data['judul'] = 'Data Jadwal Pelajaran';
        $data['menu'] = 'jadwal';
        $data['sub'] = 'jadwal';

        $data['kelas'] = $this->db
            ->select('id_kelas, nama')
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_tahun', $this->session->userdata('id_tahun_aktif'))
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();

        $data['mapel'] = $this->db
            ->select('id_mapel, nama')
            ->where('id_lembaga', $this->id_lembaga)
            ->order_by('nama', 'ASC')
            ->get('mapel')
            ->result();

        $data['guru'] = $this->db
            ->select('guru.id_guru, guru.nama')
            ->from('registrasi')
            ->join('guru', 'registrasi.id_guru = guru.id_guru')
            ->where('registrasi.id_lembaga', $this->id_lembaga)
            ->order_by('guru.nama', 'ASC')
            ->get()
            ->result();


        $this->load->view('admin/jadwal', $data);
    }


    public function fetch_jadwal($day)
    {

        $kelasList = $this->db
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_tahun', $this->session->userdata('id_tahun_aktif'))
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();

        $jadwalList = $this->db->query("
            SELECT 
                j.id_jadwal,
                j.id_kelas,
                j.id_mapel,
                j.id_guru,
                j.jam_dari,
                j.jam_sampai,
                g.kode_guru,
                g.warna
            FROM jadwal j
            LEFT JOIN guru g ON j.id_guru = g.id_guru
            WHERE j.hari = ? AND j.id_lembaga = ? AND j.id_semester = ?
        ", [$day, $this->id_lembaga, $this->session->userdata('id_semester_aktif')])->result_array();


        $mapelIds = array_unique(array_filter(array_column($jadwalList, 'id_mapel')));
        $mapelMap = [];
        if (!empty($mapelIds)) {
            $mapelList = $this->db
                ->where_in('id_mapel', $mapelIds)
                ->get('mapel')
                ->result_array();

            $mapelMap = array_column($mapelList, 'kode_mapel', 'id_mapel');
        }


        $jadwalMap = [];

        foreach ($jadwalList as $j) {

            $kodeMapel = $mapelMap[$j['id_mapel']] ?? '-';

            for ($jam = (int)$j['jam_dari']; $jam <= (int)$j['jam_sampai']; $jam++) {
                $jadwalMap[$jam][$j['id_kelas']][] = [
                    'id_jadwal' => $j['id_jadwal'],
                    'mapel'     => $kodeMapel,
                    'guru'      => $j['kode_guru'] ?? '-',
                    'warna'     => $j['warna'] ?? '#000',
                ];
            }
        }

        // $jadwalMap[jam][id_kelas][] = jadwal;
        $totalJam = $this->model->getBy2('setting', 'key', 'jml_jp', 'id_lembaga', $this->id_lembaga)->row('isi');

        echo '<div class="relative overflow-x-auto">
            <table class="w-full min-w-[640px] border border-gray-300 dark:border-gray-700 text-sm">';

        /* ===== HEADER ATAS ===== */
        echo '<thead class="bg-gray-100 dark:bg-gray-800">';
        echo '<tr>';
        echo '<th class="border p-2">Jam / Kelas</th>';

        foreach ($kelasList as $kelas) {
            echo '<th class="border p-2 text-center">' . $kelas->nama . '</th>';
        }
        echo '</tr>';
        echo '</thead>';

        /* ===== BODY ===== */
        echo '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

        for ($jam = 1; $jam <= $totalJam; $jam++) {

            echo '<tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">';

            // Header samping (jam)
            echo '
                <th class="border p-2 bg-gray-50 dark:bg-gray-900 text-center font-medium">
                    Jam ke-' . $jam . '
                </th>
            ';

            foreach ($kelasList as $kelas) {

                echo '<td class="border p-1 align-top min-h-[70px]">';

                if (!empty($jadwalMap[$jam][$kelas->id_kelas])) {

                    foreach ($jadwalMap[$jam][$kelas->id_kelas] as $jj) {

                        $bg      = $jj['warna'] ?? '#000600';
                        $jid     = $jj['id_jadwal'] ?? '-';
                        $gurukd  = $jj['guru'] ?? '-';
                        $kdmapel = $jj['mapel'] ?? '-';


                        echo '
                            <div 
                                class="rounded p-1 mb-1 text-white item-jadwal text-md justify-items-center cursor-pointer fade-in"
                                style="background-color: ' . $bg . '"
                                data-jadwal-id="' . $jid . '">
                                <div class="font-medium">' . $gurukd . '-' . $kdmapel . '</div>
                            </div>
                        ';
                    }
                }

                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table></div>';
    }

    public function add()
    {

        $jam_dari = $this->input->post('jam_dari');
        $jam_sampai = $this->input->post('jam_sampai');

        $cekjam = $this->model->getBy2('setting', 'key', 'jml_jp', 'id_lembaga', $this->id_lembaga)->row();
        if ($jam_dari > $cekjam->isi || $jam_sampai > $cekjam->isi) {
            echo json_encode(['status' => 'error', 'message' => 'jam input melebihi. Max jam ke-' . $cekjam->isi]);
            exit;
        }
        $id_kelas = $this->input->post('id_kelas');
        $id_mapel = $this->input->post('id_mapel');
        $id_guru = $this->input->post('id_guru');

        $dtkelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();
        $dtmapel = $this->model->getBy('mapel', 'id_mapel', $id_mapel)->row();
        $dtguru = $this->model->getBy('guru', 'id_guru', $id_guru)->row();
        $dtlembaga = $this->model->getBy('lembaga', 'id_lembaga', $this->id_lembaga)->row();
        $idnew = $this->uuid->v4();
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $data = [
            'id_jadwal' => $idnew,
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_guru' => $id_guru,
            'jam_dari' => $jam_dari,
            'jam_sampai' => $jam_sampai,
            'id_lembaga' => $this->id_lembaga,
            'id_semester' => $id_semester_aktif
        ];
        $dataDtl = [
            'id_jadwal' => $idnew,
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $dtkelas->nama,
            'id_mapel' => $dtmapel->nama,
            'id_guru' => $dtguru->nama,
            'jam_dari' => $jam_dari,
            'jam_sampai' => $jam_sampai,
            'id_lembaga' => $dtlembaga->nama,
            'id_semester' => $id_semester_aktif
        ];

        $sql = $this->db->insert('jadwal', $data);
        $sql2 = $this->db->insert('jadwal_dtl', $dataDtl);
        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan jadwal. Silakan coba lagi.']);
        }
    }

    public function get_jadwal($id = null)
    {
        $jadwal = $this->db
            ->where('id_jadwal', $id)
            ->get('jadwal')
            ->row();

        echo json_encode($jadwal);
    }

    public function update()
    {
        $id_jadwal = $this->input->post('id_jadwal', TRUE);

        $id_kelas = $this->input->post('id_kelas');
        $id_mapel = $this->input->post('id_mapel');
        $id_guru = $this->input->post('id_guru');

        $dtkelas = $this->model->getBy('kelas', 'id_kelas', $id_kelas)->row();
        $dtmapel = $this->model->getBy('mapel', 'id_mapel', $id_mapel)->row();
        $dtguru = $this->model->getBy('guru', 'id_guru', $id_guru)->row();

        $data = [
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_guru' => $id_guru,
            'jam_dari' => $this->input->post('jam_dari', TRUE),
            'jam_sampai' => $this->input->post('jam_sampai', TRUE),
        ];

        $dataDtl = [
            'hari' => $this->input->post('hari', TRUE),
            'id_kelas' => $dtkelas->nama,
            'id_mapel' => $dtmapel->nama,
            'id_guru' => $dtguru->nama,
            'jam_dari' => $this->input->post('jam_dari'),
            'jam_sampai' => $this->input->post('jam_sampai'),
        ];

        $sql = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->update('jadwal', $data);
        $sql2 = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->update('jadwal_dtl', $dataDtl);

        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui jadwal. Silakan coba lagi.']);
        }
    }

    public function hapus()
    {
        $id_jadwal = $this->input->post('id', TRUE);

        $sql = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->delete('jadwal');
        $sql2 = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->delete('jadwal_dtl');

        if ($sql && $sql2) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal. Silakan coba lagi.']);
        }
    }

    public function check_bentrok($day)
    {
        $bentrok = $this->cek_bentrok_hari($day);

        if (empty($bentrok)) {

            echo '
                <span class="ml-2 inline-flex items-center gap-2 
                    text-green-700 dark:text-green-400 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tidak ada bentrok jadwal
                </span>';
        } else {

            echo '
                <div class="ml-2 mt-2">
                    <ul class="space-y-2">';

            foreach ($bentrok as $b) {

                $dt = $this->db
                    ->query("SELECT * FROM jadwal_dtl WHERE id_jadwal = ?", [$b->id_jadwal])
                    ->row();

                if (!$dt) continue;
                if ($b->id_lembaga === $this->id_lembaga) {
                    echo '
                    <li class="
                            px-3 py-2
                            rounded-lg border
                            border-red-200 dark:border-red-800
                            bg-red-50 dark:bg-red-900/20
                            text-red-800 dark:text-red-800
                            text-sm
                            flex items-center justify-between
                            gap-3
                            whitespace-nowrap
                        ">
                            <!-- Info Guru -->
                            <span class="truncate font-medium">
                                Guru <strong>' . $dt->id_guru . '</strong>
                            </span>

                            <!-- Badge -->
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="
                                    text-xs px-2 py-0.5 rounded-full
                                    bg-red-100 dark:bg-red-800
                                    text-red-700 dark:text-red-200
                                    font-semibold
                                ">
                                    Bentrok
                                </span>

                                <span 
                                onclick="cekBentrokOne(\'' . $dt->id_jadwal . '\')"
                                class="cursor-pointer
                                    text-xs px-2 py-0.5 rounded-full
                                    bg-blue-100 dark:bg-blue-800
                                    text-blue-700 dark:text-blue-200
                                    font-semibold
                                ">
                                    Cek
                                </span>
                            </div>
                        </li>    
                    ';
                }
            }

            echo '
                                </ul>
                                </div>';
        }
    }


    // Jam <strong>' . $dt->jam_dari . '-' . $dt->jam_sampai . '</strong> • 
    //                             Kelas <strong>' . $dt->id_kelas . '</strong> 
    //                             <span class="opacity-70">(' . $dt->id_mapel . ')</span> • 
    //                             Lembaga <strong>' . $dt->id_lembaga . '</strong>

    public function cek_bentrok_hari($hari)
    {
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        // 1️⃣ Ambil semua jadwal di hari tersebut untuk semester aktif
        $jadwal = $this->db
            ->where('hari', $hari)
            ->where('id_semester', $id_semester_aktif)
            ->order_by('id_guru')
            ->order_by('jam_dari')
            ->get('jadwal')
            ->result();

        if (count($jadwal) === 0) {
            return $data = [];
        }

        // 2️⃣ Kelompokkan jadwal per guru
        $groupGuru = [];
        foreach ($jadwal as $j) {
            $groupGuru[$j->id_guru][] = $j;
        }

        // 3️⃣ Cari bentrok
        $bentrok = [];

        foreach ($groupGuru as $id_guru => $list) {

            $total = count($list);

            for ($i = 0; $i < $total; $i++) {
                for ($j = $i + 1; $j < $total; $j++) {

                    $a = $list[$i];
                    $b = $list[$j];

                    // OVERLAP CHECK
                    if (
                        $a->jam_dari <= $b->jam_sampai &&
                        $a->jam_sampai >= $b->jam_dari
                    ) {

                        // simpan dua-duanya
                        $bentrok[$a->id_jadwal] = $a;
                        $bentrok[$b->id_jadwal] = $b;
                    }
                }
            }
        }

        if (empty($bentrok)) {
            $data = [];
        } else {
            $data = array_values($bentrok);
        }
        return $data;
    }

    public function cek_bentrok_one()
    {
        $id_jadwal = $this->input->post('id_jadwal');

        // Jadwal acuan
        $jadwal = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->get('jadwal')
            ->row();
        $jadwalDt = $this->db
            ->where('id_jadwal', $id_jadwal)
            ->get('jadwal_dtl')
            ->row();

        if (!$jadwal) {
            echo json_encode(['status' => false]);
            return;
        }

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        // Jadwal bentrok
        $bentrok = $this->db
            ->select('jadwal_dtl.*')
            ->join('jadwal_dtl', 'jadwal.id_jadwal=jadwal_dtl.id_jadwal')
            ->where('jadwal.hari', $jadwal->hari)
            ->where('jadwal.id_guru', $jadwal->id_guru)
            ->where('jadwal.id_semester', $id_semester_aktif)
            ->where('jadwal.id_jadwal !=', $id_jadwal)
            ->where('jadwal.jam_dari <=', $jadwal->jam_sampai)
            ->where('jadwal.jam_sampai >=', $jadwal->jam_dari)
            ->get('jadwal')
            ->result();

        echo json_encode([
            'status' => true,
            'guru' => $jadwalDt->id_guru,
            'jadwal' => $jadwalDt,
            'bentrok' => $bentrok
        ]);
    }

    public function full()
    {
        $data['judul'] = 'Full Jadwal Pelajaran';
        $data['menu'] = 'jadwal';
        $data['sub'] = 'jadwal';
        $data['hideSidebar'] = true;

        $id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        $data['kelas'] = $this->db
            ->select('id_kelas, nama')
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_tahun', $id_tahun_aktif)
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();

        $data['gurus'] = $this->db->query("
            SELECT g.id_guru, g.nama, g.kode_guru, g.warna, 
                   COALESCE(SUM(j.jam_sampai - j.jam_dari + 1), 0) as total_jp
            FROM registrasi r
            JOIN guru g ON r.id_guru = g.id_guru
            LEFT JOIN jadwal j ON g.id_guru = j.id_guru AND j.id_lembaga = r.id_lembaga AND j.id_semester = ?
            WHERE r.id_lembaga = ?
            GROUP BY g.id_guru, g.nama, g.kode_guru, g.warna
            ORDER BY total_jp DESC, g.nama ASC
        ", [$id_semester_aktif, $this->id_lembaga])->result();

        $breakdown = $this->db->query("
            SELECT id_guru, id_kelas, SUM(jam_sampai - jam_dari + 1) as jp_kelas
            FROM jadwal
            WHERE id_lembaga = ? AND id_semester = ?
            GROUP BY id_guru, id_kelas
        ", [$this->id_lembaga, $id_semester_aktif])->result_array();

        $jpMap = [];
        foreach ($breakdown as $b) {
            $jpMap[$b['id_guru']][$b['id_kelas']] = (int)$b['jp_kelas'];
        }
        $data['jpMap'] = $jpMap;

        $this->load->view('admin/jadwal_full', $data);
    }

    public function export_excel()
    {
        $this->mustLogin();
        $this->AdminOrSuper();

        $spreadsheet = new Spreadsheet();

        // --- SHEET 1: REKAP JAM MENGAJAR GURU ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Jam Mengajar');

        // Styles
        $style_header = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        $style_data = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];

        // Title
        $sheet->setCellValue('A1', 'REKAP JAM MENGAJAR GURU');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $id_tahun_aktif = $this->session->userdata('id_tahun_aktif');
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        // Fetch data
        $kelas = $this->db
            ->select('id_kelas, nama')
            ->where('id_lembaga', $this->id_lembaga)
            ->where('id_tahun', $id_tahun_aktif)
            ->order_by('nama', 'ASC')
            ->get('kelas')
            ->result();

        $gurus = $this->db->query("
            SELECT g.id_guru, g.nama, g.kode_guru, 
                   COALESCE(SUM(j.jam_sampai - j.jam_dari + 1), 0) as total_jp
            FROM registrasi r
            JOIN guru g ON r.id_guru = g.id_guru
            LEFT JOIN jadwal j ON g.id_guru = j.id_guru AND j.id_lembaga = r.id_lembaga AND j.id_semester = ?
            WHERE r.id_lembaga = ?
            GROUP BY g.id_guru, g.nama, g.kode_guru
            ORDER BY total_jp DESC, g.nama ASC
        ", [$id_semester_aktif, $this->id_lembaga])->result();

        $breakdown = $this->db->query("
            SELECT id_guru, id_kelas, SUM(jam_sampai - jam_dari + 1) as jp_kelas
            FROM jadwal
            WHERE id_lembaga = ? AND id_semester = ?
            GROUP BY id_guru, id_kelas
        ", [$this->id_lembaga, $id_semester_aktif])->result_array();

        $jpMap = [];
        foreach ($breakdown as $b) {
            $jpMap[$b['id_guru']][$b['id_kelas']] = (int)$b['jp_kelas'];
        }

        // Header Row (Row 3)
        $sheet->setCellValueByColumnAndRow(1, 3, 'No');
        $sheet->setCellValueByColumnAndRow(2, 3, 'Nama Guru');

        $colIdx = 3;
        $classCols = [];
        foreach ($kelas as $k) {
            $sheet->setCellValueByColumnAndRow($colIdx, 3, $k->nama);
            $classCols[$k->id_kelas] = $colIdx;
            $colIdx++;
        }

        $totalColIdx = $colIdx;
        $sheet->setCellValueByColumnAndRow($totalColIdx, 3, 'Total JP');

        // Apply header styles
        $lastColLetter = Coordinate::stringFromColumnIndex($totalColIdx);
        $sheet->getStyle("A3:{$lastColLetter}3")->applyFromArray($style_header);

        // Data Rows
        $rowNum = 4;
        $no = 1;
        foreach ($gurus as $g) {
            $sheet->setCellValueByColumnAndRow(1, $rowNum, $no++);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $g->nama . ' (' . $g->kode_guru . ')');

            foreach ($kelas as $k) {
                $jp = $jpMap[$g->id_guru][$k->id_kelas] ?? 0;
                $colIndex = $classCols[$k->id_kelas];
                $sheet->setCellValueByColumnAndRow($colIndex, $rowNum, $jp > 0 ? $jp . ' JP' : '-');
            }

            $sheet->setCellValueByColumnAndRow($totalColIdx, $rowNum, $g->total_jp . ' JP');

            // Format numbers
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            for ($c = 3; $c <= $totalColIdx; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $sheet->getStyle("{$colLetter}{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $sheet->getStyle("A{$rowNum}:{$lastColLetter}{$rowNum}")->applyFromArray($style_data);
            $rowNum++;
        }

        // Auto width
        for ($c = 1; $c <= $totalColIdx; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // --- SHEET 2-7: JADWAL HARIAN ---
        $daysOfWeek = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $totalJam = $this->model->getBy2('setting', 'key', 'jml_jp', 'id_lembaga', $this->id_lembaga)->row('isi');

        foreach ($daysOfWeek as $eng => $ind) {
            $newSheet = $spreadsheet->createSheet();
            $newSheet->setTitle('Jadwal ' . $ind);

            // Title
            $newSheet->setCellValue('A1', 'JADWAL PELAJARAN - HARI ' . strtoupper($ind));
            $newSheet->mergeCells('A1:C1');
            $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Header Row (Row 3)
            $newSheet->setCellValueByColumnAndRow(1, 3, 'Jam / Kelas');

            $colIdx = 2;
            $kelasCols = [];
            foreach ($kelas as $k) {
                $newSheet->setCellValueByColumnAndRow($colIdx, 3, $k->nama);
                $kelasCols[$k->id_kelas] = $colIdx;
                $colIdx++;
            }
            $lastClassColIdx = $colIdx - 1;
            $lastClassColLetter = Coordinate::stringFromColumnIndex($lastClassColIdx);

            $newSheet->getStyle("A3:{$lastClassColLetter}3")->applyFromArray($style_header);

            // Load schedule entries for this day
            $jadwalList = $this->db->query("
                SELECT j.id_kelas, j.id_mapel, j.jam_dari, j.jam_sampai, g.kode_guru
                FROM jadwal j
                LEFT JOIN guru g ON j.id_guru = g.id_guru
                WHERE j.hari = ? AND j.id_lembaga = ? AND j.id_semester = ?
            ", [$eng, $this->id_lembaga, $id_semester_aktif])->result_array();

            $mapelIds = array_unique(array_filter(array_column($jadwalList, 'id_mapel')));
            $mapelMap = [];
            if (!empty($mapelIds)) {
                $mapelList = $this->db
                    ->where_in('id_mapel', $mapelIds)
                    ->get('mapel')
                    ->result_array();
                $mapelMap = array_column($mapelList, 'kode_mapel', 'id_mapel');
            }

            // Map hours
            $jadwalMap = [];
            foreach ($jadwalList as $j) {
                $kodeMapel = $mapelMap[$j['id_mapel']] ?? '-';
                for ($jam = (int)$j['jam_dari']; $jam <= (int)$j['jam_sampai']; $jam++) {
                    $jadwalMap[$jam][$j['id_kelas']][] = $j['kode_guru'] . '-' . $kodeMapel;
                }
            }

            // Render table content rows
            $rowNum = 4;
            for ($jam = 1; $jam <= $totalJam; $jam++) {
                $newSheet->setCellValueByColumnAndRow(1, $rowNum, 'Jam ke-' . $jam);
                $newSheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach ($kelas as $k) {
                    $colIndex = $kelasCols[$k->id_kelas];
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    $entries = $jadwalMap[$jam][$k->id_kelas] ?? [];
                    $val = !empty($entries) ? implode("\n", $entries) : '-';
                    $newSheet->setCellValueByColumnAndRow($colIndex, $rowNum, $val);

                    if (count($entries) > 1) {
                        $newSheet->getStyle("{$colLetter}{$rowNum}")->getAlignment()->setWrapText(true);
                    }
                    $newSheet->getStyle("{$colLetter}{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $newSheet->getStyle("A{$rowNum}:{$lastClassColLetter}{$rowNum}")->applyFromArray($style_data);
                $rowNum++;
            }

            // Auto width for all columns in this worksheet
            for ($c = 1; $c <= $lastClassColIdx; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $newSheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        // Set active sheet to sheet 1 (Rekap Jam Mengajar)
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $filename = 'Jadwal_Pelajaran_dan_Rekap_Mengajar.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function reset()
    {
        $this->mustLogin();
        $this->AdminOrSuper();

        $password = $this->input->post('password');

        if (empty($password)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'msg' => 'Password wajib diisi!']));
            return;
        }

        // Verify password
        $user = $this->db->get_where('user', ['id_user' => $this->iduser])->row();
        if (!$user || !password_verify($password, $user->password)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'msg' => 'Password salah!']));
            return;
        }

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        // Delete schedule for current institution and semester
        $this->db->trans_start();

        $this->db->query("
            DELETE FROM jadwal_dtl 
            WHERE id_jadwal IN (
                SELECT id_jadwal FROM jadwal WHERE id_lembaga = ? AND id_semester = ?
            )
        ", [$this->id_lembaga, $id_semester_aktif]);

        $this->db->where('id_lembaga', $this->id_lembaga)
            ->where('id_semester', $id_semester_aktif)
            ->delete('jadwal');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'msg' => 'Gagal menghapus jadwal!']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'msg' => 'Jadwal berhasil di-reset!']));
        }
    }
}
