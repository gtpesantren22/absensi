<?php
class MY_Controller extends CI_Controller
{
    protected function mustLogin()
    {
        if (!$this->session->userdata('login')) {
            redirect('auth');
        }
    }

    protected function onlyAdmin()
    {
        if ($this->session->userdata('level') !== 'admin') {
            show_error('Akses ditolak', 403);
        }
    }
    protected function onlyAdminSuper()
    {
        if ($this->session->userdata('level') !== 'super_admin') {
            show_error('Akses ditolak', 403);
        }
    }
    protected function AdminOrSuper()
    {
        $allowed = ['admin', 'super_admin'];

        if (!in_array($this->session->userdata('level'), $allowed)) {
            // tidak punya akses
            show_error('Akses ditolak', 403);
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
            show_error('Anda bukan guru piket', 403);
        }
    }
}
