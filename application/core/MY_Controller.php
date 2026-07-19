<?php
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('cookie');

        // Load App Settings globally if setting table exists
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
        $this->load->vars([
            'app_name' => $app_name,
            'app_logo' => $app_logo
        ]);

        // Self-healing database check for remember_token
        if (!$this->db->field_exists('remember_token', 'user')) {
            $this->db->query("ALTER TABLE `user` ADD COLUMN `remember_token` VARCHAR(255) NULL;");
        }

        // Auto login via remember token if session does not exist
        if (!$this->session->userdata('login')) {
            $token = get_cookie('remember_token');
            if ($token) {
                $user = $this->db->get_where('user', ['remember_token' => $token, 'aktif' => 'Y'])->row();
                if ($user) {
                    $this->session->set_userdata([
                        'login' => true,
                        'id_user' => $user->id_user,
                        'nama_user' => $user->nama,
                        'level' => $user->level,
                        'id_lembaga' => $user->id_lembaga,
                        'foto_user' => $user->foto
                    ]);
                }
            }
        }

        // Check if logged in but academic session is not initialized
        if ($this->session->userdata('login') && !$this->session->userdata('id_semester_aktif')) {
            $active_sem = $this->db->select('s.*, t.nama_tahun')
                ->from('semester s')
                ->join('tahun_ajaran t', 's.id_tahun = t.id_tahun')
                ->where('s.is_active', 1)
                ->get()
                ->row();
            if ($active_sem) {
                $this->session->set_userdata([
                    'id_tahun_aktif' => $active_sem->id_tahun,
                    'nama_tahun_aktif' => $active_sem->nama_tahun,
                    'id_semester_aktif' => $active_sem->id_semester,
                    'nama_semester_aktif' => $active_sem->nama_semester
                ]);
            }
        }

        // Global Maintenance Mode check
        $maintenance_mode = false;
        if ($this->db->table_exists('setting')) {
            $row_maint = $this->db->get_where('setting', ['key' => 'maintenance_mode'])->row();
            if ($row_maint && $row_maint->isi === '1') {
                $maintenance_mode = true;
            }
        }

        if ($maintenance_mode) {
            $current_controller = $this->router->fetch_class();
            $current_method = $this->router->fetch_method();

            $is_auth_page = (strtolower($current_controller) === 'auth');
            $is_maintenance_page = (strtolower($current_controller) === 'welcome' && strtolower($current_method) === 'maintenance');
            $is_super_admin = ($this->session->userdata('level') === 'super_admin');

            if (!$is_super_admin && !$is_auth_page && !$is_maintenance_page) {
                // If it is an AJAX/API request, return JSON response
                if ($this->input->is_ajax_request()) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(503)
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => 'Sistem sedang dalam pemeliharaan (Maintenance Mode). Silakan coba lagi nanti.'
                        ]))
                        ->_display();
                    exit;
                } else {
                    redirect('welcome/maintenance');
                }
            }
        }
    }
    protected function mustLogin()
    {
        if (!$this->session->userdata('login')) {
            redirect('auth');
        }
    }

    protected function onlyAdmin()
    {
        if ($this->session->userdata('level') !== 'admin') {
            redirect('welcome/no_akes');
        }
    }
    protected function onlyAdminSuper()
    {
        if ($this->session->userdata('level') !== 'super_admin') {
            redirect('welcome/no_akes');
        }
    }
    protected function AdminOrSuper()
    {
        $allowed = ['admin', 'super_admin'];

        if (!in_array($this->session->userdata('level'), $allowed)) {
            // tidak punya akses
            redirect('welcome/no_akes');
        }
    }

    protected function onlyPiket()
    {
        $this->load->model('Modeldata', 'model');

        $iduser = $this->session->userdata('id_user');
        $hari = date('l');
        $cekGuru = $this->db->query("SELECT * FROM user WHERE id_user = '$iduser' ")->row();
        $cek = $this->model->getBy2('piket', 'id_guru', $cekGuru->id_guru, 'hari', $hari)->row();

        if ($this->session->userdata('level') !== 'admin' && $this->session->userdata('level') !== 'super_admin' && !$cek) {
            // show_error('Anda bukan guru piket', 403);
            redirect('welcome/no_akes');
        }
    }
}
