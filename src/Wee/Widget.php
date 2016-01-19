<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Wee\Db;

abstract class Widget
{
    public $database = '';
    public $db;

    public function __construct()
    {
        if ('' != $this->database) {
            $this->db = Db::instance($this->database);
        }
        $this->auto();
    }

    public function auto()
    {}

    public function min($table, $field = 'id')
    {
        $appends = "order by $field asc";
        $ret = $this->db->selectField($table, $field, null, null, $appends);
        return $ret;
    }

    public function max($table, $field = 'id')
    {
        $appends = "order by $field desc";
        $ret = $this->db->selectField($table, $field, null, null, $appends);
        return $ret;
    }

    public function hash($id)
    {
        return '';
    }

    /**
     * insert array struct
     */
    public function in(array $fields, array $bind)
    {
        $data = array();
        foreach ($fields as $field => $val) {
            if (isset($bind[$field])) {
                $data[$field] = $bind[$field];
            } elseif (null !== $val['default']) {
                $data[$field] = $val['default'];
            }
        }

        return $data;
    }

    /**
     * update array struct
     */
    public function up(array $fields, array $bind)
    {
        $data = array();
        foreach ($fields as $field => $default) {
            if (isset($bind[$field])) {
                $data[$field] = $bind[$field];
            }
        }

        return $data;
    }

    /**
     * replace
     */
    public function create(array $bind, $method = 'REPLACE')
    {
        $data = $this->in($this->fields, $bind);

        if ($method == 'REPLACE') {
            $this->db->replace($this->table, $data);
        } else {
            $this->db->insert($this->table, $data);
        }

        return $this->db->insert_id;
    }

    /**
     * delete
     */
    public function destory($id)
    {
        $conds = ['id=' => $id];
        $id = $this->db->delete($this->table, $conds);
        return $id;
    }

    /**
     * update
     */
    public function store($id, array $bind)
    {
        $conds = ['id=' => $id];
        $data = $this->up($this->fields, $bind);
        return $this->db->update($this->table, $data, $conds);
    }

    /**
     * select
     */
    public function get($id)
    {
        $conds = ['id=' => $id];
        $row = $this->db->selectRow($this->table, '*', $conds);
        return $row;
    }

    public function find($conds = null, $limit = 20, $offset = 0, $desc = true)
    {
        $fields = ['*'];
        $appends = '';
        if ($desc) {
            $appends .= " order by id desc";
        }
        $appends .= " limit $limit offset $offset";
        $rows = $this->db->select($this->table, $fields, $conds, null, $appends);
        return $rows;
    }

}
