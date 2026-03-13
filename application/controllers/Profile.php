<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $this->mustLogin();
        $this->iduser = $this->session->userdata('id_user');
    }

    public function index()
    {
        $data['title'] = 'Profile';
        $data['menu'] = '-';
        $data['sub'] = '-';

        $data['data'] = $this->model->getBy('user', 'id_user', $this->iduser)->row();
        $data['guru'] = $this->model->getBy('guru', 'id_guru', $data['data']->id_guru)->row();

        $this->load->view('profile', $data);
    }

    public function update_account()
    {
        $id_user = $this->iduser;

        $username = trim($this->input->post('username'));
        $password_baru = $this->input->post('password_baru');
        $password_konfirmasi = $this->input->post('password_konfirmasi');

        // ambil data user
        $user = $this->db->get_where('user', ['id_user' => $id_user])->row();

        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan');
            redirect('profile');
        }

        $data_update = [
            'username' => $username
        ];

        // jika password baru diisi
        if ($password_baru != '' || $password_konfirmasi != '') {

            // cek konfirmasi
            if ($password_baru != $password_konfirmasi) {
                $this->session->set_flashdata('error', 'Konfirmasi password tidak sama');
                redirect('profile');
            }

            // hash password baru
            $data_update['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
            $data_update['pass_v'] = $password_baru;
        }

        // transaksi database
        $this->db->trans_start();

        $this->db->where('id_user', $id_user);
        $this->db->update('user', $data_update);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $this->session->set_flashdata('error', 'Gagal menyimpan perubahan');
        } else {

            $this->session->set_flashdata('ok', 'Data akun berhasil diperbarui');
        }

        redirect('profile');
    }
}
