<?php
/**
 * 用户认证控制器
 */

namespace App\Controllers;

class AuthController extends BaseController
{
    /**
     * 登录页面
     */
    public function login()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/teacher-workload-system/public/dashboard.php');
        }
        echo $this->view('auth/login');
    }

    /**
     * 处理登录
     */
    public function doLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->error('用户名和密码不能为空');
        }

        $result = $this->auth->login($username, $password);
        
        if ($result['success']) {
            $this->success($result['message']);
        } else {
            $this->error($result['message']);
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/teacher-workload-system/public/login.php');
    }

    /**
     * 个人资料
     */
    public function profile()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        
        if (!$user) {
            $this->error('用户未登录');
        }

        $userData = $this->db->table('users')->find($user['id']);
        echo $this->view('auth/profile', ['user' => $userData]);
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $this->error('新密码与确认密码不一致');
        }

        $result = $this->auth->changePassword($user['id'], $oldPassword, $newPassword);
        
        if ($result['success']) {
            $this->success($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}
