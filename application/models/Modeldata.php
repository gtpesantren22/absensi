<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modeldata extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function tambah($table, $data)
    {
        if ($this->db->insert($table, $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAll($table)
    {
        return $this->db->get($table);
    }
    public function hapus($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->delete($table);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function hapus2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->delete($table);
    }
    public function hapus3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        $this->db->delete($table);
    }
    public function edit($table, $where, $dtwhere, $data)
    {
        $this->db->where($where, $dtwhere);
        $this->db->update($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function edit2($table, $where, $dtwhere, $where2, $dtwhere2, $data)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->update($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function edit3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3, $data)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        $this->db->update($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function getBy($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }
    public function getBy2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }
    public function getBy3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        return $this->db->get($table);
    }
    public function getBy4($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3, $where4, $dtwhere4)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        $this->db->where($where4, $dtwhere4);
        return $this->db->get($table);
    }

    public function getBy5($tbl, $where1, $dtwhere1, $where2, $dtwhere2, $where3, $dtwhere3, $where4, $dtwhere4, $where5, $dtwhere5)
    {
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        $this->db->where($where4, $dtwhere4);
        $this->db->where($where5, $dtwhere5);
        return $this->db->get($tbl);
    }

    public function getBySelect($table, $where, $dtwhere, $select)
    {
        $this->db->select($select);
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }
    public function getGroup($table, $groupby)
    {
        $this->db->group_by($groupby);
        return $this->db->get($table);
    }
    public function getOrder($table, $orderby, $list)
    {
        $this->db->order_by($orderby, $list);
        return $this->db->get($table);
    }
    public function getOrder2($table, $orderby, $list, $orderby2, $list2)
    {
        $this->db->order_by($orderby, $list);
        $this->db->order_by($orderby2, $list2);
        return $this->db->get($table);
    }
    public function query($qr)
    {
        return $this->db->query($qr);
    }

    public function insertBatch($table, $data)
    {
        if (!empty($data)) {
            $this->db->insert_batch($table, $data);
        }
    }
}
