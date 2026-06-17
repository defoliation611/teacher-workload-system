<?php
/**
 * 基础模型类
 */

namespace App\Models;

use Library\Database;

class BaseModel
{
    protected $db;
    protected $table;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * 获取所有记录
     */
    public function all()
    {
        return $this->db->table($this->table)->select();
    }

    /**
     * 根据ID获取
     */
    public function find($id)
    {
        return $this->db->table($this->table)->find($id);
    }

    /**
     * 条件查询
     */
    public function where($field, $operator = '=', $value = null)
    {
        return $this->db->table($this->table)->where($field, $operator, $value);
    }

    /**
     * 创建
     */
    public function create($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    /**
     * 更新
     */
    public function update($data, $id)
    {
        return $this->db->table($this->table)->update($data, $id);
    }

    /**
     * 删除
     */
    public function delete($id)
    {
        return $this->db->table($this->table)->delete($id);
    }

    /**
     * 统计
     */
    public function count()
    {
        return $this->db->table($this->table)->count();
    }
}
