<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('help'); // for translateDay and tanggal_indo helpers
	}

	public function kirim_jadwal()
	{
		$time_now = date('H:i');
		$day_now = date('l');

		// Get active semester from database
		$active_semester = $this->db->get_where('semester', ['is_active' => 1])->row();
		$id_semester = $active_semester ? $active_semester->id_semester : null;

		// Find settings where key = 'waktu_info_jadwal' and value matches the current hour/minute
		$settings = $this->db->get_where('setting', [
			'key' => 'waktu_info_jadwal',
			'isi' => $time_now
		])->result();

		if (empty($settings)) {
			echo json_encode([
				'status' => true,
				'message' => 'Tidak ada jadwal pengiriman pada waktu ini: ' . $time_now
			]);
			return;
		}

		// Get global WA settings
		$wa_api_url_db = $this->db->get_where('setting', ['key' => 'wa_api_url'])->row('isi');
		$wa_api_key_db = $this->db->get_where('setting', ['key' => 'wa_api_key'])->row('isi');

		$wa_api_url = $wa_api_url_db ?: (getenv('WA_API_URL') ?: '');
		$wa_api_key = $wa_api_key_db ?: (getenv('WA_API_KEY') ?: '');

		$details_sent = [];

		foreach ($settings as $s_item) {
			$id_lembaga = $s_item->id_lembaga;

			$lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $id_lembaga])->row();
			if (!$lembaga) continue;

			// Get selected groups for this institution
			$selected_groups_db = $this->db->get_where('setting', [
				'key' => 'wa_selected_groups',
				'id_lembaga' => $id_lembaga
			])->row('isi');

			$selected_groups = json_decode($selected_groups_db, true) ?: [];
			if (empty($selected_groups)) continue;

			// Fetch schedules for today (English day name in DB)
			$this->db->select('jd.id_kelas as kelas_nama, jd.id_mapel as mapel_nama, jd.id_guru as guru_nama, j.jam_dari, j.jam_sampai')
				->from('jadwal j')
				->join('jadwal_dtl jd', 'j.id_jadwal = jd.id_jadwal')
				->where('j.hari', $day_now)
				->where('j.id_lembaga', $id_lembaga);
			if ($id_semester !== null) {
				$this->db->where('j.id_semester', $id_semester);
			}
			$schedules = $this->db->order_by('jd.id_kelas', 'ASC')
				->order_by('j.jam_dari', 'ASC')
				->get()
				->result_array();

			// If no schedule exists today (e.g. holiday or Friday off), do not send
			if (empty($schedules)) continue;

			// Format header message
			$hari_indo = translateDay($day_now, 'id');
			$tgl_indo = tanggal_indo(date('Y-m-d'), false);

			$message = "INFO JADWAL PELAJARAN\n";
			$message .= "Lembaga: " . $lembaga->nama . "\n";
			$message .= "Hari/Tanggal: " . $hari_indo . ", " . $tgl_indo . "\n";
			$message .= "=========================\n\n";

			// Group schedules by class
			$schedules_by_kelas = [];
			foreach ($schedules as $s) {
				$kelas_name = $s['kelas_nama'] ?: '-';
				$schedules_by_kelas[$kelas_name][] = $s;
			}

			foreach ($schedules_by_kelas as $kelas => $items) {
				$message .= "*" . $kelas . "*\n";
				foreach ($items as $item) {
					$message .= $item['jam_dari'] . "-" . $item['jam_sampai'] . " : " . $item['guru_nama'] . " (" . $item['mapel_nama'] . ")\n";
				}
				$message .= "\n";
			}

			$message .= "=========================\n";

			// Get piket teachers
			$this->db->select('g.nama')
				->from('piket p')
				->join('guru g', 'p.id_guru = g.id_guru')
				->join('registrasi r', 'r.id_guru = g.id_guru AND r.id_lembaga = p.id_lembaga')
				->where('p.hari', $day_now)
				->where('p.id_lembaga', $id_lembaga);
			if ($id_semester !== null) {
				$this->db->where('p.id_semester', $id_semester);
			}
			$piket_teachers = $this->db->order_by('g.nama', 'ASC')
				->get()
				->result_array();

			$message .= "*Guru Piket:*\n";
			if (!empty($piket_teachers)) {
				$no = 1;
				foreach ($piket_teachers as $pt) {
					$message .= $no . ". " . $pt['nama'] . "\n";
					$no++;
				}
			} else {
				$message .= "-\n";
			}
			$message .= "=========================\n";

			$message .= "Harap diperhatikan oleh guru dan siswa. Terima kasih.";

			// Send to all selected groups
			$sessionId = $lembaga->session_id ?: "default";
			foreach ($selected_groups as $group) {
				$groupId = $group['id'];

				$ch = curl_init();
				curl_setopt_array($ch, [
					CURLOPT_URL => $wa_api_url . '/send-group',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => http_build_query([
						'groupId' => $groupId,
						'message' => $message,
						'apiKey' => $wa_api_key,
						'sessionId' => $sessionId
					]),
					CURLOPT_TIMEOUT => 20
				]);
				$response = curl_exec($ch);
				curl_close($ch);

				$details_sent[] = [
					'lembaga' => $lembaga->nama,
					'group' => $group['subject'],
					'groupId' => $groupId,
					'response' => json_decode($response, true) ?: $response
				];
			}
		}

		echo json_encode([
			'status' => true,
			'message' => 'Proses pengiriman jadwal selesai',
			'time' => $time_now,
			'day' => $day_now,
			'details' => $details_sent
		]);
	}
}
