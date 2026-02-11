<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mengajar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()
        $this->mustLogin();

        $this->iduser = $this->session->userdata('id_user');
        $usrdtl = $this->db->query("SELECT * FROM user WHERE id_user = '$this->iduser' ")->row();
        $this->id_lembaga = $usrdtl->id_lembaga;
    }

    public function index()
    {
        $data['title'] = "Absensi Guru";
        $data['menu'] = "absensiguru";
        $data['sub'] = "mengajar";


        $this->load->view('absensi/mengajar', $data);
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

        $jmlKelas = $this->db_active->query("SELECT COUNT(DISTINCT tanggal) AS jml FROM mengajar")->row()->jml;


        // ================= TOTAL =================
        $this->db_active->from('mengajar');

        if (!empty($search)) {
            $this->db_active->group_start()
                ->like('tanggal', $search)
                ->group_end();
        }

        // count DISTINCT tanggal
        $this->db_active->select('COUNT(DISTINCT tanggal) AS total');
        $total = $this->db_active->get()->row()->total;


        // ================= DATA =================
        $this->db_active->select('
            mengajar.tanggal,
            MIN(mengajar.id) AS id,
            COUNT(mengajar.id_guru) AS jumlah
        ');

        $this->db_active->from('mengajar');

        if (!empty($search)) {
            $this->db_active->group_start()
                ->like('mengajar.tanggal', $search)
                ->group_end();
        }

        $this->db_active->group_by('mengajar.tanggal');
        $this->db_active->order_by($sortBy, $sortDir);
        $this->db_active->limit($perPage, $offset);

        $data = $this->db_active->get()->result_array();

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
        $dataJadwal = $this->db->query("SELECT id_guru FROM jadwal WHERE hari = '$harini' AND id_lembaga = '$this->id_lembaga' GROUP BY id_guru ")->result();
        $dataKirim = [];
        foreach ($dataJadwal as $key) {
            // $hadir = $this->db_active->query("SELECT * FROM kehadiran WHERE tanggal = '$tglni' AND guru = '$key->guru' ")->row();
            $jam = $this->db->query("SELECT * FROM jadwal WHERE hari = '$harini' AND id_guru = '$key->id_guru' ")->result();
            $guru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$key->id_guru' ")->row();
            $array_hasil = [];
            foreach ($jam as $datas) {
                $array_range = range($datas->jam_dari, $datas->jam_sampai);
                $array_hasil = array_merge($array_hasil, $array_range);
            }
            $dataKirim[] = [
                'id_guru' => $key->id_guru,
                // 'hadir' => $hadir ? $hadir->ket : '',
                'nama' => $guru->nama,
                'jam' => $array_hasil,
            ];
        }
        $data['data'] = $dataKirim;
        $data['tanggal'] = $tglni;
        $data['hari'] = $harini;

        // echo '<pre>';
        // var_dump($dataKirim);
        // echo '</pre>';

        $this->load->view('absensi/mengajar_add', $data);
    }

    public function rincian_guru()
    {

        $kdguru = $this->input->post('guru', true);
        $tanggal = $this->input->post('tanggal', true);
        $harini = date('l', strtotime($tanggal));

        $jml_jp = $this->model->getBy('setting', 'key', 'jml_jp')->row('isi');

        $guru = $this->db->query("SELECT * FROM guru WHERE id_guru = '$kdguru' ")->row();

        // $mapel = $this->model->getBy('mapel', 'kode_mapel', $key->mapel)->row();
        $jadwal = $this->db->query("SELECT * FROM jadwal WHERE hari = '$harini' AND id_guru = '$kdguru' AND id_lembaga = '$this->id_lembaga' ORDER BY jam_dari ASC ")->result();
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
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow mb-5 p-4">
                <table class="w-full text-sm border-separate border-spacing-y-2">';
        foreach ($jadwal as $jadwal):
            $mapel = $this->db_active->query("SELECT * FROM mapel WHERE id_mapel = '$jadwal->id_mapel'")->row();
            $kelas = $this->db_active->query("SELECT * FROM kelas WHERE id_kelas = '$jadwal->id_kelas'")->row();

            echo '<tr class="
                        bg-slate-50 dark:bg-slate-800
                        hover:bg-slate-100 dark:hover:bg-slate-700
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
        echo '
        <form id="form-absensi" method="POST">
            <div class="overflow-x-auto bg-white dark:bg-slate-900 rounded-xl shadow">
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
            $cek = $this->db_active->query("SELECT * FROM mengajar WHERE id_guru='$guru->id_guru' AND tanggal='$hariini' AND jam=$i")->row();
            $ket = $cek ? $cek->ket : '';

            echo '<tr class="
                        bg-white dark:bg-slate-900
                        hover:bg-slate-50 dark:hover:bg-slate-800
                        transition
                        border border-slate-200 dark:border-slate-700">
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
                    echo $ket == $val ? 'checked>' : '>';
                    echo '
                                                    <span class="
                                                        inline-flex items-center justify-center
                                                        w-5 h-5 rounded-full text-xs font-bold
                                                        border border-' . $color . '-500
                                                        text-' . $color . '-600
                                                        dark:text-' . $color . '-400
                                                        peer-checked:bg-' . $color . '-500
                                                        peer-checked:text-white
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
                echo '
                    <td rowspan="' . $jml_jp . '" class="px-3 py-2 align-top">
                        <textarea
                            name="alasan"
                            rows="10"
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
            <div class="mt-4">
            <button type="submit"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow">
                <i class="fa fa-save mr-1"></i> Simpan
            </button>
            <button type="button" onclick="closeModal(\'inputModal\')"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow">
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

        foreach ($datas as $data) {
            $guru = $data['guru'];
            $jam = $data['jam'];
            $ket = $data['value'];
            $alasan = !empty($data['alasan']) ? $data['alasan'] : '-';

            $cek = $this->model->getBy3('mengajar', 'id_guru', $guru, 'jam', $jam, 'tanggal', $tanggal)->row();
            if ($cek) {
                $this->db_active->where('id_guru', $guru);
                $this->db_active->where('jam', $jam);
                $this->db_active->where('tanggal', $tanggal);
                $this->db_active->update('mengajar', ['ket' => $ket, 'alasan' => $alasan]);
            } else {
                $simpan = [
                    'id_guru' => $guru,
                    'jam' =>  $jam,
                    'ket' =>  $ket,
                    'tanggal' =>  $tanggal,
                    'alasan' =>  $alasan,
                ];
                $this->model->tambah('mengajar', $simpan);
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
                CURLOPT_URL => 'http://31.97.179.141:3100/capture?url=' . base_url() . 'screen/mengajar_guru/' . $tgl . '/' . $lembaga->id_lembaga . '&filename=KBM-GURU-' . $nick . '_' . $tgl,
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
            $fileUrl = "http://31.97.179.141:3100/capture-result/KBM-GURU-$nick"  . "_$tgl.png";
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
