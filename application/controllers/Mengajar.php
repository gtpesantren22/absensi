<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mengajar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->mustLogin();
        $this->onlyPiket();

        $this->iduser = $this->session->userdata('id_user');
        $this->id_lembaga = $this->session->userdata('id_lembaga');
    }

    public function index()
    {
        $data['title'] = "Absensi Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "mengajar";

        if ($this->session->userdata('level') === 'guru') {
            $this->load->view('guru/mengajar', $data);
        } else {
            $this->load->view('absensi/mengajar', $data);
        }
    }

    public function mengajarData()
    {
        // $result = $this->Model_mapel->getData($params);
        $search   = $this->input->get('search') ?? '';
        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
        $sortBy   = $this->input->get('sortBy') ?? 'tanggal';
        $sortDir  = strtoupper($this->input->get('sortDir') ?? 'DESC');

        $offset = ($page - 1) * $perPage;

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        /* ================= TOTAL ================= */
        $this->db->from('mengajar');

        $this->db->where('mengajar.id_lembaga', $this->id_lembaga);
        $this->db->where('mengajar.id_semester', $id_semester_aktif);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('mengajar.tanggal', $search)
                ->group_end();
        }

        // count DISTINCT tanggal
        $this->db->select('COUNT(DISTINCT mengajar.tanggal) AS total');

        $total = $this->db->get()->row()->total;


        /* ================= DATA ================= */
        $this->db->select('
            mengajar.tanggal,
            MIN(mengajar.id) AS id,
            COUNT(mengajar.id_guru) AS jumlah
        ');

        $this->db->from('mengajar');

        $this->db->where('mengajar.id_lembaga', $this->id_lembaga);
        $this->db->where('mengajar.id_semester', $id_semester_aktif);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('mengajar.tanggal', $search)
                ->group_end();
        }

        $this->db->group_by('mengajar.tanggal');
        $this->db->order_by($sortBy, $sortDir);
        $this->db->limit($perPage, $offset);

        $data = $this->db->get()->result_array();

        $result = [
            'data'      => $data,
            'total'     => (int) $total,
            'page'      => (int) $page,
            'perPage'   => (int) $perPage,
            'lastPage'  => ceil($total / $perPage),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }


    public function mengajar_add($id = null)
    {
        $data['judul'] = "Absensi Mengajar Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "mengajar";
        $data['id_lembaga'] = $this->id_lembaga;

        $data['jml_jp'] = $this->model->getBy('setting', 'key', 'jml_jp')->row('isi');

        if ($id == null) {
            $harini = date('l');
            $tglni = date('Y-m-d');
        } else {
            $dataCari = $this->model->getBy('mengajar', 'id', $id)->row();
            $harini = date('l', strtotime($dataCari->tanggal));
            $tglni = $dataCari->tanggal;
        }


        // $harini = 'Monday';
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $dataJadwal = $this->db->query("SELECT id_guru FROM jadwal WHERE hari = '$harini' AND id_lembaga = '$this->id_lembaga' AND id_semester = '$id_semester_aktif' GROUP BY id_guru ")->result();
        $dataKirim = [];
        foreach ($dataJadwal as $key) {
            // $hadir = $this->db->query("SELECT * FROM kehadiran WHERE tanggal = '$tglni' AND guru = '$key->guru' ")->row();
            $jam = $this->db->query("SELECT * FROM jadwal WHERE hari = '$harini' AND id_guru = '$key->id_guru' AND id_lembaga = '$this->id_lembaga' AND id_semester = '$id_semester_aktif' ")->result();
            $guru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$key->id_guru' ")->row();
            $array_hasil = [];
            foreach ($jam as $datas) {
                $array_range = range($datas->jam_dari, $datas->jam_sampai);
                $array_hasil = array_merge($array_hasil, $array_range);
            }
            $dataKirim[] = [
                'id_guru' => $key->id_guru,
                'nama' => $guru->nama,
                'jam' => $array_hasil,
            ];
        }
        $data['data'] = $dataKirim;
        $data['tanggal'] = $tglni;
        $data['hari'] = $harini;

        if ($this->session->userdata('level') === 'guru') {
            $this->load->view('guru/mengajar_add', $data);
        } else {
            $this->load->view('absensi/mengajar_add', $data);
        }
    }

    public function rincian_guru()
    {

        $kdguru = $this->input->post('guru', true);
        $tanggal = $this->input->post('tanggal', true);
        $harini = date('l', strtotime($tanggal));

        $jml_jp = $this->model->getBy('setting', 'key', 'jml_jp')->row('isi');

        $guru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$kdguru' ")->row();

        // $mapel = $this->model->getBy('mapel', 'kode_mapel', $key->mapel)->row();
        $id_semester_aktif = $this->session->userdata('id_semester_aktif');
        $jadwal = $this->db->query("SELECT * FROM jadwal WHERE hari = '$harini' AND id_guru = '$kdguru' AND id_lembaga = '$this->id_lembaga' AND id_semester = '$id_semester_aktif' ORDER BY jam_dari ASC ")->result();
        $array_hasil = [];
        foreach ($jadwal as $datas) {
            $array_range = range($datas->jam_dari, $datas->jam_sampai);
            $array_hasil = array_merge($array_hasil, $array_range);
        }

        $jam = $array_hasil;
        $tanggalIni = $tanggal;

        echo '
            <h4 class="text-lg font-bold text-slate-700 dark:text-gray-100 mb-2">
                ' . $guru->nama . '
            </h4>
            <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80 rounded-xl shadow-sm mb-5 p-4">
                <table class="w-full text-sm border-separate border-spacing-y-2">';
        foreach ($jadwal as $jadwal):
            $mapel = $this->db->query("SELECT * FROM mapel WHERE id_mapel = '$jadwal->id_mapel'")->row();
            $kelas = $this->db->query("SELECT * FROM kelas WHERE id_kelas = '$jadwal->id_kelas'")->row();

            echo '<tr class="
                        bg-white dark:bg-slate-900/60
                        hover:bg-slate-100 dark:hover:bg-slate-800
                        transition rounded-lg">
                            <td class="px-3 py-2 font-medium text-slate-600 dark:text-slate-300">
                                Jam ' . $jadwal->jam_dari . ' - ' . $jadwal->jam_sampai . '
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-200">' . $kelas->nama . '</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-200">' . $mapel->nama . '</td>
                        </tr>';
        endforeach;
        // <h4 class="text-md font-semibold text-slate-700 mb-2">Input Absensi</h4>
        echo '</table>
            </div>
        ';
        $isReadOnly = false;
        if ($this->session->userdata('level') === 'guru' && $tanggal !== date('Y-m-d')) {
            $isReadOnly = true;
        }

        // Check if teacher is absent today
        $cek_hadir = $this->db->get_where('kehadiran_guru', [
            'id_guru' => $kdguru,
            'tanggal' => $tanggal,
            'id_semester' => $id_semester_aktif
        ])->row();

        $absentReason = null;
        $forcedLetter = '';
        if ($cek_hadir && in_array(strtolower($cek_hadir->ket), ['izin', 'sakit', 'alpha', 'alfa', 'cuti'])) {
            $absentReason = strtolower($cek_hadir->ket);
            $isReadOnly = true; // force read-only
            if ($absentReason === 'izin') $forcedLetter = 'I';
            elseif ($absentReason === 'sakit') $forcedLetter = 'S';
            elseif ($absentReason === 'alpha' || $absentReason === 'alfa') $forcedLetter = 'A';
            elseif ($absentReason === 'cuti') $forcedLetter = 'C';
        }

        echo '
        <form id="form-absensi" method="POST">
            <div class="overflow-x-auto bg-slate-50 dark:bg-slate-800/40 border border-slate-105 dark:border-slate-800/80 rounded-xl shadow-sm">
                <table class="w-full text-sm border-separate border-spacing-y-2">

                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                            <th class="px-3 py-2 rounded-lg text-center">Jam</th>
                            <th class="px-3 py-2 rounded-lg">Absen</th>
                            <th class="px-3 py-2 rounded-lg">Keterangan</th>
                        </tr>
                    </thead>


                    <tbody>';

        $hariini = $tanggalIni;
        for ($i = 1; $i <= $jml_jp; $i++):
            $cek = $this->db->query("SELECT * FROM mengajar WHERE id_guru='$guru->id_guru' AND tanggal='$hariini' AND jam=$i AND id_lembaga = '$this->id_lembaga' AND id_semester = '$id_semester_aktif' ")->row();
            $ket = $cek ? $cek->ket : '';
            if ($forcedLetter !== '') {
                $ket = $forcedLetter;
            }

            echo '<tr class="
                        bg-white dark:bg-slate-900/60
                        hover:bg-slate-50 dark:hover:bg-slate-800/40
                        transition">
                                <td class="px-3 py-2 text-center font-semibold text-slate-700 dark:text-slate-200">' . $i . '</td>

                                <!-- Radio Absen -->
                                <td class="px-3 py-2">';
            if (in_array($i, $jam)):
                echo '<div class="flex flex-wrap gap-2">';

                $opsi = [
                    'H' => 'emerald',
                    'S' => 'yellow',
                    'I' => 'blue',
                    'A' => 'red',
                    'T' => 'sky',
                    'C' => 'slate',
                ];
                foreach ($opsi as $val => $color):
                    echo '
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                        class="hidden peer"
                                                        name="djam_' . $i . '"
                                                        data-jam="' . $i . '"
                                                        data-guru="' . $guru->id_guru . '"
                                                        value="' . $val . '"';
                    echo $ket == $val ? 'checked' : '';
                    echo ($isReadOnly || $forcedLetter !== '') ? ' disabled>' : '>';
                    echo '
                                                    <span class="
                                                        inline-flex items-center justify-center
                                                        w-5 h-5 rounded-full text-xs font-bold
                                                        border border-' . $color . '-500
                                                        text-' . $color . '-600
                                                        dark:text-' . $color . '-400
                                                        peer-checked:bg-' . $color . '-500
                                                        peer-checked:text-white
                                                        peer-disabled:opacity-60
                                                        transition
                                                    ">
                                                        ' . $val . '
                                                    </span>
                                                </label>';
                endforeach;
                echo '</div>';
            endif;
            echo '</td>';

            if ($i === 1):
                $cekalasan =  $cek ? $cek->alasan : '-';
                if ($forcedLetter !== '') {
                    $cekalasan = 'Tercatat ' . ucfirst($absentReason) . ' di absensi harian guru.';
                }
                echo '
                    <td rowspan="' . $jml_jp . '" class="px-3 py-2 align-top">
                        <textarea
                            name="alasan"
                            rows="10"
                            ' . ($isReadOnly ? 'disabled' : '') . '
                            class="w-full rounded-lg
                                border border-slate-300 dark:border-slate-600
                                bg-white dark:bg-slate-800
                                text-slate-700 dark:text-slate-200
                                p-2 text-sm
                                focus:ring focus:ring-emerald-200 dark:focus:ring-emerald-500">' . $cekalasan . '</textarea>
                    </td>';
            endif;
            echo '
                </tr>';
        endfor;
        echo '
                    </tbody>
                </table>
            </div>

            <!-- Button -->
            <div class="mt-4 flex items-center gap-2">';
        if (!$isReadOnly) {
            echo '
            <button type="submit"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition active:scale-95">
                <i class="fa fa-save mr-1"></i> Simpan
            </button>';
        } else {
            if ($forcedLetter !== '') {
                echo '
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/20 px-3.5 py-2 rounded-lg border border-rose-200/30">
                    <i class="fas fa-ban mr-1"></i> Ditutup: Tercatat ' . ucfirst($absentReason) . ' di Kehadiran
                </span>';
            } else {
                echo '
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3.5 py-2 rounded-lg">
                    <i class="fas fa-eye mr-1"></i> Hanya Lihat (Read-Only)
                </span>';
            }
        }
        echo '
            <button type="button" onclick="closeModal(\'inputModal\')"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow transition active:scale-95">
                <i class="fa fa-x mr-1"></i> Close
            </button>
            </div>
        </form>
        ';
    }

    public function simpanJam()
    {
        $datas = $this->input->post('datas', true);
        $tanggal = $this->input->post('tanggal', true);

        if ($this->session->userdata('level') === 'guru' && $tanggal !== date('Y-m-d')) {
            echo json_encode(['status' => 'error', 'message' => 'Guru Piket hanya bisa mengisi/mengubah data hari ini.']);
            return;
        }

        $id_semester_aktif = $this->session->userdata('id_semester_aktif');

        // Check if any of the teachers in datas is recorded as absent in kehadiran_guru
        if (!empty($datas)) {
            $first_guru = $datas[0]['guru'];
            $cek_hadir = $this->db->get_where('kehadiran_guru', [
                'id_guru' => $first_guru,
                'tanggal' => $tanggal,
                'id_semester' => $id_semester_aktif
            ])->row();

            if ($cek_hadir && in_array(strtolower($cek_hadir->ket), ['izin', 'sakit', 'alpha', 'alfa', 'cuti'])) {
                echo json_encode(['status' => 'error', 'message' => 'Guru tercatat ' . $cek_hadir->ket . ' di absensi kehadiran. Absensi mengajar ditutup.']);
                return;
            }
        }
        foreach ($datas as $data) {
            $guru = $data['guru'];
            $jam = $data['jam'];
            $ket = $data['value'];
            $alasan = !empty($data['alasan']) ? $data['alasan'] : '-';

            $cek = $this->db->get_where('mengajar', [
                'id_guru' => $guru,
                'jam' => $jam,
                'tanggal' => $tanggal,
                'id_lembaga' => $this->id_lembaga,
                'id_semester' => $id_semester_aktif
            ])->row();
            if ($cek) {
                $this->db->where('id_guru', $guru);
                $this->db->where('jam', $jam);
                $this->db->where('tanggal', $tanggal);
                $this->db->where('id_lembaga', $this->id_lembaga);
                $this->db->where('id_semester', $id_semester_aktif);
                $this->db->update('mengajar', ['ket' => $ket, 'alasan' => $alasan]);
            } else {
                $simpan = [
                    'id_guru' => $guru,
                    'jam' =>  $jam,
                    'ket' =>  $ket,
                    'tanggal' =>  $tanggal,
                    'alasan' =>  $alasan,
                    'id_lembaga' => $this->id_lembaga,
                    'id_semester' => $id_semester_aktif
                ];
                $this->db->insert('mengajar', $simpan);
            }
        }

        echo json_encode(['status' => 'success']);
    }

    public function hapusKehadiran()
    {
        $id = $this->input->post('id', true);
        $this->model->hapus3('kehadiran_guru', 'tanggal', $id, 'id_guru !=', '0', 'ket !=', '');
        echo json_encode(['success' => true]);
    }

    public function screenMengajarGuru($tgl)
    {
        $lembaga = $this->db->query("SELECT * FROM lembaga WHERE id_lembaga = '$this->id_lembaga' ")->row();
        $nick = $lembaga->nickname;
        $curl2 = curl_init();

        curl_setopt_array(
            $curl2,
            array(
                CURLOPT_URL => 'https://capture.ppdwk.site/capture?url=' . base_url() . 'screen/mengajar_guru/' . $tgl . '/' . $lembaga->id_lembaga . '&filename=KBM-GURU-' . $nick . '_' . $tgl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET'
            )
        );

        $response = curl_exec($curl2);
        curl_close($curl2);

        $result = json_decode($response, true);

        // === VALIDASI RESPONSE ===
        if (!$result || !isset($result['status'])) {
            show_error('Response API tidak valid');
        }

        // === JIKA STATUS TRUE ===
        if ($result['status'] === true) {

            // URL FILE SUDAH DITENTUKAN
            $fileUrl = "https://capture.ppdwk.site/capture-result/KBM-GURU-$nick"  . "_$tgl.png";
            $fileName = "KBM-GURU-$nick"  . "_$tgl.png";

            // === AMBIL FILE DARI URL ===
            $ch = curl_init($fileUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 60,
            ]);

            $fileData = curl_exec($ch);

            if ($fileData === false) {
                $error = curl_error($ch);
                curl_close($ch);
                show_error('Gagal download file: ' . $error);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                show_error('Gagal download file. HTTP Code: ' . $httpCode);
            }

            // === FORCE DOWNLOAD ===
            header('Content-Description: File Transfer');
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . strlen($fileData));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            echo $fileData;
            exit;
        } else {
            show_error('Status false, download dibatalkan');
        }
    }
}
