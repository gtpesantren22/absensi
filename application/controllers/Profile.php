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

        // Self-healing database check for profile photo column
        if (!$this->db->field_exists('foto', 'user')) {
            $this->load->dbforge();
            $this->dbforge->add_column('user', [
                'foto' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE,
                    'default' => NULL
                ]
            ]);
        }
    }

    public function index()
    {
        $data['title'] = 'Profile';
        $data['menu'] = 'profile';
        $data['sub'] = 'profile';

        $data['data'] = $this->model->getBy('user', 'id_user', $this->iduser)->row();
        $data['guru'] = $this->model->getBy('guru', 'id_guru', $data['data']->id_guru)->row();

        if ($this->session->userdata('level') === 'guru') {
            $this->load->view('guru/profile', $data);
        } else {
            $this->load->view('profile', $data);
        }
    }

    public function update_account()
    {
        $id_user = $this->iduser;

        $username = trim($this->input->post('username', true));
        $nama_user = trim($this->input->post('nama_user', true));
        $no_hp = trim($this->input->post('no_hp', true));
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

        if ($nama_user != '') {
            $data_update['nama'] = $nama_user;
        }

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

        // Upload handler untuk foto profil
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path']   = './uploads/profile/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'p_' . $id_user . '_' . time();

            // pastikan folder exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto')) {
                // hapus foto lama jika ada
                if ($user->foto && file_exists('./uploads/profile/' . $user->foto)) {
                    @unlink('./uploads/profile/' . $user->foto);
                }

                $upload_data = $this->upload->data();
                $data_update['foto'] = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('profile');
            }
        }

        // transaksi database
        $this->db->trans_start();

        $this->db->where('id_user', $id_user);
        $this->db->update('user', $data_update);

        // Jika user terhubung ke data guru, perbarui nama & no_hp di sana juga
        if (!empty($user->id_guru) && $user->id_guru !== '0') {
            $guru_update = [];
            if ($nama_user != '') {
                $guru_update['nama'] = $nama_user;
            }
            if ($no_hp != '') {
                $guru_update['no_hp'] = $no_hp;
            }

            if (!empty($guru_update)) {
                $this->db->where('id_guru', $user->id_guru);
                $this->db->update('guru', $guru_update);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menyimpan perubahan');
        } else {
            // Perbarui data nama_user di session
            if ($nama_user != '') {
                $this->session->set_userdata('nama_user', $nama_user);
            }
            if (isset($data_update['foto'])) {
                $this->session->set_userdata('foto_user', $data_update['foto']);
            }
            $this->session->set_flashdata('ok', 'Data profil berhasil diperbarui');
        }

        redirect('profile');
    }
}

