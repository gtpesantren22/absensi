<?php
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->database();
        if (!$this->db->field_exists('remember_token', 'user')) {
            $this->db->query("ALTER TABLE `user` ADD COLUMN `remember_token` VARCHAR(255) NULL;");
        }

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
    }

    public function index()
    {
        
        $this->load->helper('cookie');
        $token = get_cookie('remember_token');
        if ($token && !$this->session->userdata('login')) {
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

        if ($this->session->userdata('login')) {
            redirect(base_url());
        }

        $this->load->view('auth/login');
    }
    public function login()
    {
        header('Content-Type: application/json');

        // JANGAN redirect di AJAX
        if ($this->session->userdata('login')) {
            echo json_encode([
                'status' => true,
                'redirect' => base_url()
            ]);
            exit;
        }

        if (!$this->input->post()) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid request'
            ]);
            exit;
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ]);
            exit;
        }

        $username = $this->input->post('username', true);
        $password = $this->input->post('password');
        $remember = $this->input->post('remember') ? true : false;

        if ($this->auth->tooManyAttempts($username)) {
            echo json_encode([
                'status' => false,
                'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.'
            ]);
            exit;
        }

        $user = $this->auth->getUser($username);

        if ($user && password_verify($password, $user->password) && $user->aktif === 'Y') {

            // Check if maintenance mode is active and user is not super_admin
            $maintenance_mode = false;
            if ($this->db->table_exists('setting')) {
                $row_maint = $this->db->get_where('setting', ['key' => 'maintenance_mode'])->row();
                if ($row_maint && $row_maint->isi === '1') {
                    $maintenance_mode = true;
                }
            }
            if ($maintenance_mode && $user->level !== 'super_admin') {
                $this->auth->logAttempt($username);
                echo json_encode([
                    'status' => false,
                    'message' => 'Sistem sedang dalam pemeliharaan (Maintenance Mode). Hanya Super Admin yang dapat masuk saat ini.'
                ]);
                exit;
            }

            $this->session->sess_regenerate(true);

            $this->session->set_userdata([
                'login' => true,
                'id_user' => $user->id_user,
                'nama_user' => $user->nama,
                'level' => $user->level,
                'id_lembaga' => $user->id_lembaga,
                'foto_user' => $user->foto
            ]);

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $this->db->update('user', [
                    'remember_token' => $token,
                    'last_login' => date('Y-m-d H:i:s')
                ], [
                    'id_user' => $user->id_user
                ]);

                $this->load->helper('cookie');
                set_cookie([
                    'name'   => 'remember_token',
                    'value'  => $token,
                    'expire' => 30 * 24 * 3600,
                    'path'   => '/',
                    'secure' => FALSE,
                    'httponly' => TRUE
                ]);
            } else {
                $this->db->update('user', [
                    'remember_token' => NULL,
                    'last_login' => date('Y-m-d H:i:s')
                ], [
                    'id_user' => $user->id_user
                ]);

                $this->load->helper('cookie');
                delete_cookie('remember_token');
            }

            echo json_encode([
                'status' => true,
                'message' => 'Login berhasil',
                'redirect' => base_url()
            ]);
            exit;
        }

        $this->auth->logAttempt($username);

        echo json_encode([
            'status' => false,
            'message' => 'Login gagal! Username atau password salah'
        ]);
        exit;
    }

    public function logout()
    {
        $id_user = $this->session->userdata('id_user');
        if ($id_user) {
            $this->db->update('user', [
                'remember_token' => NULL
            ], [
                'id_user' => $id_user
            ]);
        }
        $this->load->helper('cookie');
        delete_cookie('remember_token');

        $this->session->sess_destroy();
        redirect('auth');
    }
}
