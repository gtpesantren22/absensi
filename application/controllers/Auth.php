<?php
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
    }

    public function index()
    {
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

        if ($this->auth->tooManyAttempts($username)) {
            echo json_encode([
                'status' => false,
                'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.'
            ]);
            exit;
        }

        $user = $this->auth->getUser($username);

        if ($user && password_verify($password, $user->password) && $user->aktif === 'Y') {

            $this->session->sess_regenerate(true);

            if ($user->level != 'super_admin') {
                $dbdata = $this->db->query("SELECT a.db_name FROM list_db a JOIN lembaga b ON a.id=b.id_db WHERE b.id_lembaga = '$user->id_lembaga' ")->row();
                $this->session->set_userdata([
                    'login' => true,
                    'id_user' => $user->id_user,
                    'nama_user' => $user->nama,
                    'level' => $user->level,
                    'db_selected' => $dbdata ? $dbdata->db_name : ''
                ]);
            } else {
                $this->session->set_userdata([
                    'login' => true,
                    'id_user' => $user->id_user,
                    'nama_user' => $user->nama,
                    'level' => $user->level,
                ]);
            }


            $this->db->update('user', [
                'last_login' => date('Y-m-d H:i:s')
            ], [
                'id_user' => $user->id_user
            ]);

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
        $this->session->sess_destroy();
        redirect('auth');
    }
}
