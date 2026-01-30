<?php
class Auth_model extends CI_Model
{
    public function getUser($username)
    {
        return $this->db->get_where('user', ['username' => $username])->row();
    }


    public function logAttempt($username)
    {
        $this->db->insert('login_attempts', [
            'username' => $username,
            'ip_address' => $this->input->ip_address()
        ]);
    }


    public function tooManyAttempts($username)
    {
        $this->db->where('username', $username);
        $this->db->where('attempt_time >=', date('Y-m-d H:i:s', strtotime('-5 minutes')));
        return $this->db->count_all_results('login_attempts') >= 5;
    }
}
