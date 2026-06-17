<?php
/**
 * 基础控制器
 */

namespace App\Controllers;

use Library\Auth;
use Library\Database;
use Library\Logger;

class BaseController
{
    protected $auth;
    protected $db;
    protected $logger;
    protected $view;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->auth = new Auth($this->db);
        $this->logger = new Logger();
    }

    /**
     * 渲染视图
     */
    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die('视图文件不存在: ' . $viewPath);
        }
        
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    /**
     * 返回JSON
     */
    protected function json($data, $code = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 返回成功
     */
    protected function success($message = '操作成功', $data = null)
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 返回错误
     */
    protected function error($message = '操作失败', $code = 400)
    {
        return $this->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

    /**
     * 重定向
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * 检查是否登录
     */
    protected function checkLogin()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/teacher-workload-system/public/login.php');
        }
    }

    /**
     * 检查权限
     */
    protected function checkPermission($roles)
    {
        $this->checkLogin();
        if (!$this->auth->hasRole($roles)) {
            $this->error('您没有权限访问此页面', 403);
        }
    }
}
