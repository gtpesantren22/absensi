<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modeldata extends CI_Model
{
    protected $db_active;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()
    }

    public function tambah($table, $data)
    {
        if ($this->db_active->insert($table, $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAll($table)
    {
        return $this->db_active->get($table);
    }
    public function hapus($table, $where, $dtwhere)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->delete($table);
        if ($this->db_active->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function hapus2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->delete($table);
    }
    public function hapus3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        $this->db_active->delete($table);
    }
    public function edit($table, $where, $dtwhere, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->update($table, $data);
        if ($this->db_active->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function edit2($table, $where, $dtwhere, $where2, $dtwhere2, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->update($table, $data);
        if ($this->db_active->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function edit3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        $this->db_active->update($table, $data);
        if ($this->db_active->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function getBy($table, $where, $dtwhere)
    {
        $this->db_active->where($where, $dtwhere);
        return $this->db_active->get($table);
    }
    public function getBy2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        return $this->db_active->get($table);
    }
    public function getBy3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        return $this->db_active->get($table);
    }

    public function getBy5($tbl, $where1, $dtwhere1, $where2, $dtwhere2, $where3, $dtwhere3, $where4, $dtwhere4, $where5, $dtwhere5)
    {
        $this->db_active->where($where1, $dtwhere1);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        $this->db_active->where($where4, $dtwhere4);
        $this->db_active->where($where5, $dtwhere5);
        return $this->db_active->get($tbl);
    }

    public function getBySelect($table, $where, $dtwhere, $select)
    {
        $this->db_active->select($select);
        $this->db_active->where($where, $dtwhere);
        return $this->db_active->get($table);
    }
    public function getGroup($table, $groupby)
    {
        $this->db_active->group_by($groupby);
        return $this->db_active->get($table);
    }
    public function getOrder($table, $orderby, $list)
    {
        $this->db_active->order_by($orderby, $list);
        return $this->db_active->get($table);
    }
    public function getOrder2($table, $orderby, $list, $orderby2, $list2)
    {
        $this->db_active->order_by($orderby, $list);
        $this->db_active->order_by($orderby2, $list2);
        return $this->db_active->get($table);
    }
    public function query($qr)
    {
        return $this->db_active->query($qr);
    }

    public function insertBatch($table, $data)
    {
        if (!empty($data)) {
            $this->db_active->insert_batch($table, $data);
        }
    }
}
