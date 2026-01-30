<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dynamic_db
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database(); // koneksi default ke DB utama
    }

    public function connect()
    {
        $selected = $this->CI->session->userdata('db_selected');

        if ($selected) {
            $config = $this->get_db_config($selected);
        } else {
            $config = $this->get_default_db_config();
        }

        return $this->CI->load->database($config, TRUE);
    }

    private function get_db_config($db_name)
    {
        $row = $this->CI->db->get_where('list_db', ['db_name' => $db_name])->row();
        if (!$row) return $this->get_default_db_config();

        return [
            'hostname' => $row->hostname,
            'username' => $row->username,
            'password' => $row->password,
            'database' => $row->db_name,
            'dbdriver' => $row->dbdriver,
            'pconnect' => FALSE,
            'db_debug' => TRUE,
        ];
    }

    private function get_default_db_config()
    {
        $row = $this->CI->db->get_where('list_db', ['aktif' => 1])->row();
        if (!$row) show_error('Tidak ada database default aktif!');

        return [
            'hostname' => $row->hostname,
            'username' => $row->username,
            'password' => $row->password,
            'database' => $row->db_name,
            'dbdriver' => $row->dbdriver,
            'pconnect' => FALSE,
            'db_debug' => TRUE,
        ];
    }

    public function list_databases()
    {
        return $this->CI->db->get('list_db')->result();
    }
}
