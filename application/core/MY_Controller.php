<?php
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');

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
