<?php
/**
 * 用户模型
 */

namespace App\Models;

class User extends BaseModel
{
    protected $table = 'users';

    /**
     * 根据用户名获取用户
     */
    public function getByUsername($username)
    {
        $users = $this->where('username', $username);
        return count($users) > 0 ? $users[0] : null;
    }

    /**
     * 根据部门获取教师
     */
    public function getByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId);
    }

    /**
     * 根据角色获取用户
     */
    public function getByRole($role)
    {
        return $this->where('role', $role);
    }

    /**
     * 获取所有教师
     */
    public function getTeachers()
    {
        return $this->where('role', 'teacher');
    }

    /**
     * 获取活跃用户
     */
    public function getActiveUsers()
    {
        return $this->where('status', 1);
    }
}
