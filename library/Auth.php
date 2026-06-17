<?php
/**
 * 用户认证和授权类
 */

namespace Library;

class Auth
{
    private $db;
    private $sessionKey = 'user_session';

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->startSession();
    }

    /**
     * 启动会话
     */
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * 用户登录
     */
    public function login($username, $password)
    {
        $users = $this->db->table('users')
            ->where('username', $username);

        if (empty($users)) {
            return ['success' => false, 'message' => '用户不存在'];
        }

        $user = $users[0];
        
        // 验证密码
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => '密码错误'];
        }

        // 检查用户状态
        if ($user['status'] != 1) {
            return ['success' => false, 'message' => '账户已禁用'];
        }

        // 设置会话
        $_SESSION[$this->sessionKey] = [
            'id'         => $user['id'],
            'username'   => $user['username'],
            'name'       => $user['name'],
            'role'       => $user['role'],
            'department_id' => $user['department_id'],
            'position'   => $user['position'],
        ];

        // 更新最后登录时间
        $this->db->table('users')->update(['last_login' => date('Y-m-d H:i:s')], $user['id']);

        return ['success' => true, 'message' => '登录成功'];
    }

    /**
     * 用户登出
     */
    public function logout()
    {
        unset($_SESSION[$this->sessionKey]);
        session_destroy();
        return true;
    }

    /**
     * 检查用户是否登录
     */
    public function isLoggedIn()
    {
        return isset($_SESSION[$this->sessionKey]);
    }

    /**
     * 获取当前用户
     */
    public function getUser()
    {
        return $_SESSION[$this->sessionKey] ?? null;
    }

    /**
     * 获取用户ID
     */
    public function getUserId()
    {
        $user = $this->getUser();
        return $user['id'] ?? null;
    }

    /**
     * 检查权限
     */
    public function hasRole($role)
    {
        $user = $this->getUser();
        if (!$user) return false;
        
        if (is_array($role)) {
            return in_array($user['role'], $role);
        }
        
        return $user['role'] === $role;
    }

    /**
     * 检查权限（多个角色）
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    /**
     * 用户注册
     */
    public function register($data)
    {
        // 检查用户名是否存在
        $existing = $this->db->table('users')
            ->where('username', $data['username']);
        
        if (!empty($existing)) {
            return ['success' => false, 'message' => '用户名已存在'];
        }

        // 加密密码
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        $data['role'] = $data['role'] ?? 'teacher';

        // 插入数据
        if ($this->db->table('users')->insert($data)) {
            return ['success' => true, 'message' => '注册成功'];
        }

        return ['success' => false, 'message' => '注册失败'];
    }

    /**
     * 更改密码
     */
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->db->table('users')->find($userId);
        
        if (!$user) {
            return ['success' => false, 'message' => '用户不存在'];
        }

        // 验证旧密码
        if (!password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => '旧密码错误'];
        }

        // 更新密码
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->db->table('users')->update(['password' => $newHashedPassword], $userId);

        return ['success' => true, 'message' => '密码修改成功'];
    }
}
