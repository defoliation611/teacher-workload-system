<?php
/**
 * 管理员控制器
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\Paper;
use App\Models\Project;

class AdminController extends BaseController
{
    /**
     * 仪表板
     */
    public function dashboard()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        
        // 统计数据
        $totalTeachers = $this->db->table('users')->count();
        $totalDepartments = $this->db->table('departments')->count();
        
        $performances = $this->db->table('performance')->all();
        $pendingApproval = count(array_filter($performances, function($p) {
            return $p['status'] == 'submitted';
        }));
        
        echo $this->view('admin/dashboard', [
            'totalTeachers' => $totalTeachers,
            'totalDepartments' => $totalDepartments,
            'pendingApproval' => $pendingApproval
        ]);
    }

    /**
     * 用户管理
     */
    public function users()
    {
        $this->checkPermission(['admin']);
        
        $page = $_GET['page'] ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $users = $this->db->table('users')->select();
        $total = count($users);
        $users = array_slice($users, $offset, $perPage);
        
        echo $this->view('admin/users', [
            'users' => $users,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => ceil($total / $perPage)
        ]);
    }

    /**
     * 创建用户
     */
    public function createUser()
    {
        $this->checkPermission(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo $this->view('admin/create_user');
            return;
        }
        
        $data = [
            'username' => $_POST['username'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT),
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'role' => $_POST['role'] ?? 'teacher',
            'department_id' => $_POST['department_id'] ?? null,
            'position' => $_POST['position'] ?? '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->db->table('users')->insert($data)) {
            $this->logger->info('管理员创建了用户: ' . $data['username']);
            $this->success('用户创建成功');
        } else {
            $this->error('用户创建失败');
        }
    }

    /**
     * 编辑用户
     */
    public function editUser()
    {
        $this->checkPermission(['admin']);
        
        $userId = $_GET['id'] ?? null;
        
        if (!$userId) {
            $this->error('参数错误');
        }
        
        $user = $this->db->table('users')->find($userId);
        
        if (!$user) {
            $this->error('用户不存在');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'role' => $_POST['role'] ?? 'teacher',
                'department_id' => $_POST['department_id'] ?? null,
                'position' => $_POST['position'] ?? '',
                'status' => $_POST['status'] ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($this->db->table('users')->update($data, $userId)) {
                $this->logger->info('管理员编辑了用户: ' . $user['username']);
                $this->success('用户更新成功');
            } else {
                $this->error('用户更新失败');
            }
        }
        
        echo $this->view('admin/edit_user', ['user' => $user]);
    }

    /**
     * 系统配置
     */
    public function settings()
    {
        $this->checkPermission(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                $config = $this->db->table('config')
                    ->where('config_key', $key);
                
                if (count($config) > 0) {
                    $this->db->table('config')->update([
                        'config_value' => $value,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], $config[0]['id']);
                } else {
                    $this->db->table('config')->insert([
                        'config_key' => $key,
                        'config_value' => $value,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            $this->logger->info('管理员修改了系统配置');
            $this->success('配置已保存');
        }
        
        $config = $this->db->table('config')->all();
        $configArray = [];
        foreach ($config as $item) {
            $configArray[$item['config_key']] = $item['config_value'];
        }
        
        echo $this->view('admin/settings', ['config' => $configArray]);
    }
}
